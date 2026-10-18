<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user');

        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest()->paginate(20);
        $totalCollected = Transaction::where('status', 'completed')->sum('amount');
        $pendingCount = Transaction::where('status', 'pending')->count();

        return view('admin.transactions.index', compact('transactions', 'totalCollected', 'pendingCount'));
    }

    public function markAsCompleted(Transaction $transaction)
    {
        $transaction->update(['status' => 'completed']);
        return back()->with('success', "Transaction {$transaction->reference_id} marked as completed.");
    }

    public function markAsFailed(Transaction $transaction)
    {
        $transaction->update(['status' => 'failed']);
        return back()->with('success', "Transaction {$transaction->reference_id} marked as failed.");
    }
}
