<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'creator'])->latest();

        if ($request->filled('expense_category_id')) {
            $query->where('expense_category_id', $request->expense_category_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $expenses = $query->paginate(15)->withQueryString();
        $categories = ExpenseCategory::all();
        $totalAmount = $query->sum('amount');

        return view('admin.expenses.index', compact('expenses', 'categories', 'totalAmount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'payment_method' => 'required|string',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('expenses', 'public');
        }

        $expense = Expense::create([
            'expense_category_id' => $request->expense_category_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'payment_method' => $request->payment_method,
            'description' => $request->description,
            'attachment' => $attachmentPath,
            'created_by' => Auth::id(),
        ]);

        ActivityLog::log('EXPENSE_CREATED', 'expenses', $expense->id, "Recorded expense of ₹" . number_format($expense->amount, 2));

        return back()->with('success', 'Expense recorded successfully.');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create($request->only(['name', 'description']));

        return back()->with('success', 'Expense category created successfully.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->attachment) {
            Storage::disk('public')->delete($expense->attachment);
        }

        $amount = $expense->amount;
        $expense->delete();

        ActivityLog::log('EXPENSE_DELETED', 'expenses', null, "Deleted expense of ₹" . number_format($amount, 2));

        return back()->with('success', 'Expense deleted successfully.');
    }
}
