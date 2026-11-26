<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    // Session key for the staff register cart
    const REG_KEY = 'pos_register';

    /**
     * Show the POS register page.
     */
    public function create(Request $request)
    {
        $allProducts = Product::orderBy('name')->get();   // all, for picker
        $customers   = Customer::orderBy('full_name')->get();

        // Build register items with live product data
        $register = $this->buildRegister();

        // Online customer orders grouped by user
        $pendingOrders = CartItem::with(['user', 'product'])
            ->where('status', 'ordered')
            ->get()
            ->groupBy('user_id');

        return view('admin.sales.create', compact(
            'allProducts', 'customers', 'register', 'pendingOrders'
        ));
    }

    // ─── Register helpers ──────────────────────────────────────────────────

    private function buildRegister(): array
    {
        $raw   = session(self::REG_KEY, []);
        $items = [];
        foreach ($raw as $i => $row) {
            $product = Product::find($row['product_id']);
            if (!$product) continue;
            $items[] = [
                'index'      => $i,
                'product'    => $product,
                'quantity'   => $row['quantity'],
                'line_total' => $product->price * $row['quantity'],
            ];
        }
        return $items;
    }

    // ─── Register: Add item ────────────────────────────────────────────────

    public function registerAdd(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $qty     = (int) $request->quantity;

        $register = session(self::REG_KEY, []);

        // If same product already in register, increment quantity
        $found = false;
        foreach ($register as &$row) {
            if ($row['product_id'] === $product->id) {
                $row['quantity'] += $qty;
                $found = true;
                break;
            }
        }
        unset($row);

        if (!$found) {
            $register[] = ['product_id' => $product->id, 'quantity' => $qty];
        }

        session([self::REG_KEY => array_values($register)]);

        return redirect()->route('admin.sales.create')
            ->with('success', "'{$product->name}' × {$qty} added to register.");
    }

    // ─── Register: Remove one line ─────────────────────────────────────────

    public function registerRemove(Request $request, int $index)
    {
        $register = session(self::REG_KEY, []);
        unset($register[$index]);
        session([self::REG_KEY => array_values($register)]);

        return redirect()->route('admin.sales.create')
            ->with('success', 'Item removed from register.');
    }

    // ─── Register: Clear all ───────────────────────────────────────────────

    public function registerClear()
    {
        session()->forget(self::REG_KEY);
        return redirect()->route('admin.sales.create')
            ->with('success', 'Register cleared.');
    }

    // ─── Register: Confirm all items → record sales → deduct stock ─────────

    public function registerConfirm(Request $request)
    {
        $request->validate([
            'customer_id'    => 'nullable|exists:customers,id',
            'payment_method' => 'required|string|in:Cash,GCash,Credit Card,Bank Transfer',
        ]);

        $register = session(self::REG_KEY, []);

        if (empty($register)) {
            return back()->with('error', 'Register is empty. Add items before confirming.');
        }

        $orderTotal = 0;
        $stockErrors = [];

        // Pre-validate all stock before touching anything
        foreach ($register as $row) {
            $product = Product::find($row['product_id']);
            if (!$product) continue;
            if ($row['quantity'] > $product->stock_quantity) {
                $stockErrors[] = "'{$product->name}': only {$product->stock_quantity} in stock, tried to sell {$row['quantity']}.";
            }
        }

        if (!empty($stockErrors)) {
            return back()->with('error', 'Stock check failed — ' . implode(' | ', $stockErrors));
        }

        DB::transaction(function () use ($register, $request, &$orderTotal) {
            foreach ($register as $row) {
                $product   = Product::findOrFail($row['product_id']);
                $qty       = $row['quantity'];
                $lineTotal = $product->price * $qty;
                $orderTotal += $lineTotal;

                $product->decrement('stock_quantity', $qty);

                Sale::create([
                    'product_id'     => $product->id,
                    'customer_id'    => $request->customer_id ?: null,
                    'sold_by'        => auth()->id(),
                    'quantity'       => $qty,
                    'total_price'    => $lineTotal,
                    'payment_method' => $request->payment_method,
                ]);
            }
        });

        session()->forget(self::REG_KEY);

        return redirect()->route('admin.sales.history')
            ->with('success', "✓ Sale confirmed! " . count($register) . " item(s) — Total: ₱" . number_format($orderTotal, 2));
    }

    // ─── Fulfill entire online customer order ──────────────────────────────

    public function fulfillOrder(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->input('user_id');
        $items  = CartItem::with('product')
            ->where('user_id', $userId)
            ->where('status', 'ordered')
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'No pending items found for this customer.');
        }

        $orderTotal = 0;
        $skipped    = [];

        DB::transaction(function () use ($items, &$orderTotal, &$skipped) {
            foreach ($items as $item) {
                $product = $item->product;
                if ($product->stock_quantity < $item->quantity) {
                    $skipped[] = "'{$product->name}' (only {$product->stock_quantity} in stock)";
                    continue;
                }
                $lineTotal   = $product->price * $item->quantity;
                $orderTotal += $lineTotal;

                $product->decrement('stock_quantity', $item->quantity);

                Sale::create([
                    'product_id'     => $product->id,
                    'customer_id'    => null,
                    'sold_by'        => auth()->id(),
                    'quantity'       => $item->quantity,
                    'total_price'    => $lineTotal,
                    'payment_method' => $item->payment_method ?? 'Cash',
                ]);

                $item->update(['status' => 'fulfilled']);
            }
        });

        $msg = "Order fulfilled! Total: ₱" . number_format($orderTotal, 2) . ".";
        if (!empty($skipped)) {
            $msg .= " ⚠ Skipped (stock issue): " . implode(', ', $skipped);
        }

        return redirect()->route('admin.sales.history')->with('success', $msg);
    }

    // ─── Sales History ─────────────────────────────────────────────────────

    public function history()
    {
        $sales = Sale::with(['product', 'customer', 'staff'])->latest()->get();
        return view('admin.sales.history', compact('sales'));
    }
}
