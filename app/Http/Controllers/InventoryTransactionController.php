<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\InventoryTransaction;

class InventoryTransactionController extends Controller
{
    public function index()
    {
        $transactions = InventoryTransaction::with(['product', 'user'])->latest()->get();
        return view('admin.inventory.index', compact('transactions'));
    }
}
