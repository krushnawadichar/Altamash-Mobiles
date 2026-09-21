@extends('layouts.admin')

@section('title', 'Print Barcode - ' . $product->name)
@section('page_title', 'Print Barcode Labels')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3 no-print">
        <div>
            <h4 class="fw-bold mb-0">Barcode Labels: {{ $product->name }}</h4>
            <small class="text-muted">Generate stickers for shelves or phone boxes</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="bi bi-printer me-1"></i> Print Labels
            </button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-light border btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <style>
        .barcode-sheet {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
        }

        .barcode-sticker {
            border: 1px dashed #cbd5e1;
            padding: 12px 10px;
            text-align: center;
            border-radius: 8px;
            background: #fff;
        }

        .barcode-shop-name {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }

        .barcode-title {
            font-size: 0.8rem;
            font-weight: 700;
            margin: 4px 0;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .barcode-visual {
            font-family: 'Libre Barcode 128', monospace;
            font-size: 2.2rem;
            line-height: 1;
            margin: 6px 0;
            letter-spacing: 2px;
        }

        .barcode-num {
            font-size: 0.75rem;
            font-family: monospace;
            font-weight: 600;
            letter-spacing: 1px;
            color: #334155;
        }

        .barcode-price {
            font-size: 0.95rem;
            font-weight: 800;
            color: #059669;
            margin-top: 4px;
        }

        @media print {
            .no-print, #sidebar, #topbar {
                display: none !important;
            }
            body, #main-content, .content-body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .barcode-sheet {
                gap: 8px;
                padding: 0;
                grid-template-columns: repeat(3, 1fr);
            }
            .barcode-sticker {
                border: 1px solid #ccc;
                page-break-inside: avoid;
            }
        }
    </style>

    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&display=swap" rel="stylesheet">

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="barcode-sheet">
                @for($i = 1; $i <= 12; $i++)
                <div class="barcode-sticker">
                    <div class="barcode-shop-name">{{ \App\Models\Setting::get('shop_name', 'MobileCare') }}</div>
                    <div class="barcode-title" title="{{ $product->name }}">{{ Str::limit($product->name, 24) }}</div>
                    <div class="barcode-visual">*{{ $product->barcode ?: $product->sku }}*</div>
                    <div class="barcode-num">{{ $product->barcode ?: $product->sku }}</div>
                    <div class="barcode-price">₹{{ number_format($product->selling_price, 2) }}</div>
                </div>
                @endfor
            </div>
        </div>
    </div>

</div>
@endsection
