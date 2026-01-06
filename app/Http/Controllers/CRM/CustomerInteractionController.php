<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerInteraction;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerInteractionController extends Controller
{
    /**
     * Display a listing of interactions
     */
    public function index(Request $request)
    {
        $query = CustomerInteraction::with(['customer', 'assignedTo', 'creator']);

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by interaction type
        if ($request->filled('type')) {
            $query->where('interaction_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('interaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('interaction_date', '<=', $request->date_to);
        }

        $interactions = $query->orderBy('interaction_date', 'desc')->paginate(20);
        
        $customers = Customer::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('customers.interactions.index', compact('interactions', 'customers', 'users'));
    }

    /**
     * Show the form for creating a new interaction
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('customers.interactions.create', compact('customers', 'users'));
    }

    /**
     * Store a newly created interaction
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'interaction_type' => 'required|in:email,phone,meeting,chat,note,other',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'interaction_date' => 'required|date',
            'status' => 'required|in:scheduled,completed,cancelled,pending',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'follow_up_date' => 'nullable|date|after:interaction_date',
        ]);

        CustomerInteraction::create($validated);

        return redirect()->route('customers.interactions.index')
            ->with('success', 'Interaction logged successfully.');
    }

    /**
     * Store interaction for specific customer
     */
    public function storeForCustomer(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'interaction_type' => 'required|in:email,phone,meeting,chat,note,other',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'interaction_date' => 'required|date',
            'status' => 'required|in:scheduled,completed,cancelled,pending',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'follow_up_date' => 'nullable|date|after:interaction_date',
        ]);

        $validated['customer_id'] = $customer->id;
        CustomerInteraction::create($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Interaction logged successfully.');
    }

    /**
     * Display the specified interaction
     */
    public function show(CustomerInteraction $interaction)
    {
        $interaction->load(['customer', 'assignedTo', 'creator']);

        return view('customers.interactions.show', compact('interaction'));
    }

    /**
     * Show the form for editing the specified interaction
     */
    public function edit(CustomerInteraction $interaction)
    {
        $customers = Customer::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('customers.interactions.edit', compact('interaction', 'customers', 'users'));
    }

    /**
     * Update the specified interaction
     */
    public function update(Request $request, CustomerInteraction $interaction)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'interaction_type' => 'required|in:email,phone,meeting,chat,note,other',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'interaction_date' => 'required|date',
            'status' => 'required|in:scheduled,completed,cancelled,pending',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'follow_up_date' => 'nullable|date|after:interaction_date',
        ]);

        $interaction->update($validated);

        return redirect()->route('customers.interactions.index')
            ->with('success', 'Interaction updated successfully.');
    }

    /**
     * Remove the specified interaction
     */
    public function destroy(CustomerInteraction $interaction)
    {
        $interaction->delete();

        return redirect()->route('customers.interactions.index')
            ->with('success', 'Interaction deleted successfully.');
    }

    /**
     * Calendar view of interactions
     */
    public function calendar(Request $request)
    {
        $interactions = CustomerInteraction::with(['customer', 'assignedTo'])
            ->where('status', 'scheduled')
            ->orderBy('interaction_date')
            ->get();

        return view('customers.interactions.calendar', compact('interactions'));
    }
}
