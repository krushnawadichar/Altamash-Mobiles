@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Invoice: {{ $sale->invoice_number }}</h2>
        <a href="{{ route('admin.sales.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to Sales</a>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.sales.print', $sale->id) }}" target="_blank" class="btn btn-secondary"><i class="fa-solid fa-print me-1"></i> Print Invoice</a>
    </div>
</div>
@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #print-area, #print-area * {
            visibility: visible;
        }
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            box-shadow: none !important;
            border: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .card-body {
            padding: 0 !important;
        }
    }
</style>
@endpush

<div class="card shadow-sm border-0 mb-4" id="print-area">
    <div class="card-body p-5">
        <div class="row mb-5">
            <div class="col-md-6">
                <h2 class="fw-bold text-primary mb-1">ALTAMASH MOBILE</h2>
                <p class="text-muted mb-0">New narsala road, <br>opposite Dhanashree apartment</p>
                <p class="text-muted mb-0">Narsala, Nagpur, Maharashtra, 440034.</p>
                <p class="text-muted">Phone: 8956586537</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h1 class="fw-bold text-uppercase text-muted">Invoice</h1>
                <h5 class="mb-1"><strong>Invoice #:</strong> {{ $sale->invoice_number }}</h5>
                <p class="mb-0"><strong>Date:</strong> {{ $sale->sale_date->format('d M, Y') }}</p>
                <p class="mb-0"><strong>Status:</strong> 
                    @if($sale->payment_status == 'paid')
                        <span class="text-success fw-bold">PAID</span>
                    @elseif($sale->payment_status == 'partial')
                        <span class="text-warning fw-bold">PARTIAL</span>
                    @else
                        <span class="text-danger fw-bold">UNPAID</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-12">
                <h5 class="fw-bold">Bill To:</h5>
                @if($sale->customer)
                    <p class="mb-0 fw-bold">{{ $sale->customer->name }}</p>
                    <p class="mb-0">{{ $sale->customer->address ?? '' }}</p>
                    <p class="mb-0">{{ $sale->customer->city ?? '' }}{{ $sale->customer->state ? ', ' . $sale->customer->state : '' }}</p>
                    <p class="mb-0">Phone: {{ $sale->customer->phone ?? 'N/A' }}</p>
                @else
                    <p class="mb-0 fw-bold">Walk-in Customer</p>
                @endif
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Item Description</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->product->name }}</strong>
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end fw-bold">₹{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-7">
                <p class="text-muted"><strong>Notes:</strong> {{ $sale->notes ?? 'Thank you for your business!' }}</p>
            </div>
            <div class="col-md-5">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-end"><strong>Subtotal:</strong></td>
                        <td class="text-end">₹{{ number_format($sale->subtotal, 2) }}</td>
                    </tr>
                    @if($sale->discount > 0)
                    <tr>
                        <td class="text-end"><strong>Discount:</strong></td>
                        <td class="text-end text-danger">- ₹{{ number_format($sale->discount, 2) }}</td>
                    </tr>
                    @endif
                    @if($sale->tax > 0)
                    <tr>
                        <td class="text-end"><strong>Tax:</strong></td>
                        <td class="text-end">₹{{ number_format($sale->tax, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="border-top border-bottom">
                        <td class="text-end py-3"><h4 class="fw-bold mb-0">Grand Total:</h4></td>
                        <td class="text-end py-3"><h4 class="fw-bold text-primary mb-0">₹{{ number_format($sale->grand_total, 2) }}</h4></td>
                    </tr>
                    <tr>
                        <td class="text-end pt-3">Amount Paid:</td>
                        <td class="text-end pt-3 text-success">₹{{ number_format($sale->paid_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-end">Amount Due:</td>
                        <td class="text-end text-danger">₹{{ number_format($sale->due_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="mt-5 text-center text-muted border-top pt-3">
            <small>This is a computer-generated invoice and does not require a signature.</small>
        </div>
    </div>
</div>
@endsection


