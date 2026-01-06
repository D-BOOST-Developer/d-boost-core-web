<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerFeedback;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class CustomerFeedbackController extends Controller
{
    /**
     * Display a listing of feedback
     */
    public function index(Request $request)
    {
        $query = CustomerFeedback::with(['customer', 'product', 'sale']);

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('feedback_type', $request->type);
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by published status
        if ($request->filled('published')) {
            $query->where('is_published', $request->published === 'yes');
        }

        // Filter by response status
        if ($request->filled('responded')) {
            if ($request->responded === 'yes') {
                $query->whereNotNull('admin_response');
            } else {
                $query->whereNull('admin_response');
            }
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => CustomerFeedback::count(),
            'avg_rating' => round(CustomerFeedback::avg('rating'), 1),
            'pending' => CustomerFeedback::whereNull('admin_response')->count(),
            'published' => CustomerFeedback::where('is_published', true)->count(),
            'positive' => CustomerFeedback::whereIn('rating', [4, 5])->count(),
            'negative' => CustomerFeedback::whereIn('rating', [1, 2])->count(),
        ];

        return view('customers.feedback.index', compact('feedbacks', 'stats'));
    }

    /**
     * Show the form for creating new feedback
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        $sales = Sale::with('customer')->latest()->take(100)->get();

        return view('customers.feedback.create', compact('customers', 'products', 'sales'));
    }

    /**
     * Store a newly created feedback
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'product_id' => 'nullable|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback_type' => 'required|in:product,service,general',
            'comment' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published', false);

        CustomerFeedback::create($validated);

        return redirect()->route('customers.feedback.index')
            ->with('success', 'Feedback created successfully.');
    }

    /**
     * Display the specified feedback
     */
    public function show(CustomerFeedback $feedback)
    {
        $feedback->load(['customer', 'product', 'sale', 'responder']);

        return view('customers.feedback.show', compact('feedback'));
    }

    /**
     * Show the form for editing the specified feedback
     */
    public function edit(CustomerFeedback $feedback)
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        $sales = Sale::with('customer')->latest()->take(100)->get();

        return view('customers.feedback.edit', compact('feedback', 'customers', 'products', 'sales'));
    }

    /**
     * Update the specified feedback
     */
    public function update(Request $request, CustomerFeedback $feedback)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'product_id' => 'nullable|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback_type' => 'required|in:product,service,general',
            'comment' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published', false);

        $feedback->update($validated);

        return redirect()->route('customers.feedback.index')
            ->with('success', 'Feedback updated successfully.');
    }

    /**
     * Remove the specified feedback
     */
    public function destroy(CustomerFeedback $feedback)
    {
        $feedback->delete();

        return redirect()->route('customers.feedback.index')
            ->with('success', 'Feedback deleted successfully.');
    }

    /**
     * Add admin response to feedback
     */
    public function respond(Request $request, CustomerFeedback $feedback)
    {
        $validated = $request->validate([
            'admin_response' => 'required|string',
        ]);

        $feedback->update([
            'admin_response' => $validated['admin_response'],
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Response added successfully.');
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(CustomerFeedback $feedback)
    {
        $feedback->update([
            'is_published' => !$feedback->is_published,
        ]);

        $status = $feedback->is_published ? 'published' : 'unpublished';

        return redirect()->back()
            ->with('success', "Feedback {$status} successfully.");
    }
}
