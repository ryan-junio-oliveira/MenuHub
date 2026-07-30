<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerTag;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $query = Customer::where('restaurant_id', $restaurantId);

        if ($request->filled('tag_id')) {
            $query->whereHas('tags', fn($q) => $q->where('customer_tags.id', $request->tag_id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->withCount('orders')->with('tags')->paginate(20);
        $tags = CustomerTag::where('restaurant_id', $restaurantId)->get();

        return view('customers.index', compact('customers', 'tags'));
    }

    public function show(Customer $customer)
    {
        $customer->loadCount('orders');
        $customer->load('orders.items', 'tags');

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $restaurantId = request()->user()->restaurant_id;
        $tags = CustomerTag::where('restaurant_id', $restaurantId)->get();

        return view('customers.edit', compact('customer', 'tags'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:customer_tags,id'],
        ]);

        $customer->update($validated);

        if (isset($validated['tags'])) {
            $customer->tags()->sync($validated['tags']);
        }

        return redirect()->route('customers.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Customer $customer)
    {
        $customer->tags()->detach();
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Cliente excluído com sucesso!');
    }

    public function anonymize(Customer $customer)
    {
        $customer->anonymize();

        return redirect()->route('customers.index')->with('success', 'Dados do cliente anonimizados com sucesso!');
    }

    public function search(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;
        $query = $request->get('q');

        $customers = Customer::where('restaurant_id', $restaurantId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->with('tags')
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json($customers);
    }
}
