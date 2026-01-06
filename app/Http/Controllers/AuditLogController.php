<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Only admin can access audit logs
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $query = AuditLog::with(['user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('table_name', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('record_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Table filter
        if ($request->filled('table')) {
            $query->where('table_name', $request->table);
        }

        // Action filter
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // User filter
        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('performed_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('performed_at', '<=', $request->date_to);
        }

        $auditLogs = $query->orderBy('performed_at', 'desc')->paginate(20);
        
        // Get filter options
        $tables = AuditLog::distinct()->pluck('table_name')->filter();
        $users = \App\Models\User::orderBy('name')->get();

        return view('audit-logs.index', compact('auditLogs', 'tables', 'users'));
    }

    /**
     * Display the specified resource.
     */
    public function show(AuditLog $auditLog)
    {
        // Only admin can access audit logs
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $auditLog->load(['user']);
        return view('audit-logs.show', compact('auditLog'));
    }
}
