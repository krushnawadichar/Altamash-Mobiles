<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Sale;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('customer')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $sales = $query->get();
        return view('admin.sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'items.product', 'creator']);
        return view('admin.sales.show', compact('sale'));
    }

    public function print(Sale $sale)
    {
        $sale->load(['customer', 'items.product', 'creator']);
        return view('admin.sales.print', compact('sale'));
    }

    /**
     * Public invoice view for customers clicking link from WhatsApp
     */
    public function publicInvoice($invoice_number)
    {
        $sale = Sale::with(['customer', 'items.product', 'creator'])
                    ->where('invoice_number', $invoice_number)
                    ->firstOrFail();

        return view('frontend.invoice', compact('sale'));
    }

    /**
     * Public direct print/download invoice view
     */
    public function publicPrint($invoice_number)
    {
        $sale = Sale::with(['customer', 'items.product', 'creator'])
                    ->where('invoice_number', $invoice_number)
                    ->firstOrFail();

        return view('admin.sales.print', compact('sale'));
    }

    public function recordPayment(Request $request, Sale $sale)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $paidNow = (float)$request->amount;
        if ($paidNow > $sale->due_amount) {
            return back()->with('error', 'Paid amount cannot exceed the pending due amount (₹' . number_format($sale->due_amount, 2) . ')')->withInput();
        }

        $newPaid = $sale->paid_amount + $paidNow;
        $newDue = max(0, $sale->grand_total - $newPaid);
        $newStatus = $newDue <= 0 ? 'paid' : 'partial';

        $stamp = now()->format('d M Y, h:i A');
        $paymentLog = "[{$stamp}] Paid ₹" . number_format($paidNow, 2) . " via {$request->payment_method}";
        if ($request->filled('notes')) {
            $paymentLog .= " ({$request->notes})";
        }
        $existingNotes = $sale->notes ? $sale->notes . "\n" . $paymentLog : $paymentLog;

        $sale->update([
            'paid_amount' => $newPaid,
            'due_amount' => $newDue,
            'payment_status' => $newStatus,
            'notes' => $existingNotes,
        ]);

        return back()->with('success', 'Payment of ₹' . number_format($paidNow, 2) . ' recorded successfully! Due balance updated.');
    }
}
