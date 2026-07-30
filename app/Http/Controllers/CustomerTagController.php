<?php

namespace App\Http\Controllers;

use App\Models\CustomerTag;
use Illuminate\Http\Request;

class CustomerTagController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;
        $tags = CustomerTag::where('restaurant_id', $restaurantId)
            ->withCount('customers')
            ->orderBy('name')
            ->get();

        return view('customer-tags.index', compact('tags'));
    }

    public function create()
    {
        return view('customer-tags.form', ['tag' => null]);
    }

    public function show(CustomerTag $customerTag)
    {
        $customers = $customerTag->customers()->withCount('orders')->orderBy('name')->get();

        return view('customer-tags.show', compact('customerTag', 'customers'));
    }

    public function store(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:7'],
        ]);

        CustomerTag::create([
            ...$validated,
            'restaurant_id' => $restaurantId,
        ]);

        return redirect()->route('customer-tags.index')->with('success', 'Tag criada com sucesso!');
    }

    public function edit(CustomerTag $customerTag)
    {
        return view('customer-tags.form', ['tag' => $customerTag]);
    }

    public function update(Request $request, CustomerTag $customerTag)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:7'],
        ]);

        $customerTag->update($validated);

        return redirect()->route('customer-tags.index')->with('success', 'Tag atualizada com sucesso!');
    }

    public function destroy(CustomerTag $customerTag)
    {
        $customerTag->customers()->detach();
        $customerTag->delete();

        return redirect()->route('customer-tags.index')->with('success', 'Tag excluída com sucesso!');
    }
}
