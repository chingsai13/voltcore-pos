<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Welcome page showing product preview
     */
    public function welcome()
    {
        $products = Product::where('stock_quantity', '>', 0)->take(8)->get();
        return view('welcome', compact('products'));
    }

    /**
     * Products catalog page with category filtering and sorting
     */
    public function products(Request $request)
    {
        $category = $request->query('category', 'All');
        $sort = $request->query('sort', 'popularity'); // default to popularity/sales or name/price
        $search = $request->query('q');

        $query = Product::query();

        if ($category !== 'All' && !empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'popularity':
            default:
                $query->withCount('sales')->orderBy('sales_count', 'desc');
                break;
        }

        $products = $query->get();
        $categories = ['All', 'CPU', 'GPU', 'Memory', 'Storage', 'Display', 'Peripherals'];

        return view('products.catalog', compact('products', 'categories', 'category', 'sort', 'search'));
    }

    /**
     * User profile page
     */
    public function profile()
    {
        $user = auth()->user();
        return view('profile', compact('user'));
    }

    /**
     * About page identifying developers
     */
    public function about()
    {
        return view('about');
    }
}
