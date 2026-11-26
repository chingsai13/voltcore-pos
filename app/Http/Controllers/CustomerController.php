<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:customers,email',
            'phone'     => 'nullable|string|max:20',
        ]);

        Customer::create($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Customer record added successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:customers,email,' . $customer->id,
            'phone'     => 'nullable|string|max:20',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->sales()->exists()) {
            return redirect()->route('admin.customers.index')->with('error', 'Cannot delete customer: sales are associated with this customer.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }
}
