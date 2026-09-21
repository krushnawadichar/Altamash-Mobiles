<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Sale;
use App\Models\Setting;
use App\Services\PosService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PosController extends Controller
{
    protected PosService $posService;

    public function __construct(PosService $posService)
    {
        $this->posService = $posService;
    }

    public function index()
    {
        $categories = Category::where('status', 'active')->get();
        $brands = Brand::where('status', 'active')->get();
        $customers = Customer::latest()->take(50)->get();
        $defaultTaxPercent = Setting::get('default_tax_percent', 18);

        return view('admin.pos.index', compact('categories', 'brands', 'customers', 'defaultTaxPercent'));
    }

    /**
     * AJAX search endpoint for POS
     * Supports search by name, SKU, barcode, IMEI, model
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $categoryId = $request->input('category_id');

        if (empty($query) && empty($categoryId)) {
            $products = Product::where('status', 'active')
                ->where('current_stock', '>', 0)
                ->with(['brand', 'category', 'availableSerials'])
                ->take(24)
                ->get();

            return response()->json([
                'success' => true,
                'exact_imei_match' => false,
                'products' => $products,
            ]);
        }

        // 1. Direct IMEI barcode match check
        if (!empty($query)) {
            $serialMatch = ProductSerial::with('product')
                ->where('status', 'available')
                ->where(function ($q) use ($query) {
                    $q->where('imei_1', $query)
                      ->orWhere('imei_2', $query)
                      ->orWhere('serial_no', $query);
                })->first();

            if ($serialMatch && $serialMatch->product) {
                return response()->json([
                    'success' => true,
                    'exact_imei_match' => true,
                    'serial' => $serialMatch,
                    'product' => $serialMatch->product->load(['brand', 'category', 'availableSerials']),
                ]);
            }
        }

        // 2. Standard product search
        $q = Product::where('status', 'active')->with(['brand', 'category', 'availableSerials']);

        if (!empty($categoryId)) {
            $q->where('category_id', $categoryId);
        }

        if (!empty($query)) {
            $q->search($query);
        }

        $products = $q->take(30)->get();

        return response()->json([
            'success' => true,
            'exact_imei_match' => false,
            'products' => $products,
        ]);
    }

    /**
     * Complete checkout and generate invoice
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
        ]);

        try {
            $sale = $this->posService->processSale($request->all());

            return response()->json([
                'success' => true,
                'message' => "Sale {$sale->invoice_no} completed successfully.",
                'sale_id' => $sale->id,
                'invoice_no' => $sale->invoice_no,
                'redirect_url' => route('admin.pos.invoice', $sale->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Printable invoice view (A4 and 80mm thermal receipt formats)
     */
    public function invoice(Sale $sale)
    {
        $sale->load(['customer', 'items.product.brand', 'items.serial', 'payments', 'creator']);
        $settings = [
            'shop_name' => Setting::get('shop_name', 'MobileCare POS'),
            'shop_tagline' => Setting::get('shop_tagline', 'Sales & Service'),
            'shop_address' => Setting::get('shop_address', ''),
            'shop_phone' => Setting::get('shop_phone', ''),
            'shop_email' => Setting::get('shop_email', ''),
            'gst_number' => Setting::get('gst_number', ''),
            'currency_symbol' => Setting::get('currency_symbol', '₹'),
            'terms_conditions' => Setting::get('terms_conditions', ''),
            'invoice_footer' => Setting::get('invoice_footer', ''),
        ];

        return view('admin.pos.invoice', compact('sale', 'settings'));
    }

    /**
     * Download PDF Invoice
     */
    public function downloadPdf(Sale $sale)
    {
        $sale->load(['customer', 'items.product.brand', 'items.serial', 'payments', 'creator']);
        $settings = [
            'shop_name' => Setting::get('shop_name', 'MobileCare POS'),
            'shop_tagline' => Setting::get('shop_tagline', 'Sales & Service'),
            'shop_address' => Setting::get('shop_address', ''),
            'shop_phone' => Setting::get('shop_phone', ''),
            'shop_email' => Setting::get('shop_email', ''),
            'gst_number' => Setting::get('gst_number', ''),
            'currency_symbol' => Setting::get('currency_symbol', '₹'),
            'terms_conditions' => Setting::get('terms_conditions', ''),
            'invoice_footer' => Setting::get('invoice_footer', ''),
        ];

        $pdf = Pdf::loadView('admin.pos.pdf', compact('sale', 'settings'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("{$sale->invoice_no}.pdf");
    }
}
