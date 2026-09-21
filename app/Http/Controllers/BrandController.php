<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->latest()->paginate(15);
        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brands', 'public');
        }

        $brand = Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'logo' => $logoPath,
            'status' => $request->status,
        ]);

        ActivityLog::log('BRAND_CREATED', 'brands', $brand->id, "Created brand: {$brand->name}");
        return back()->with('success', 'Brand created successfully.');
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $logoPath = $brand->logo;
        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $logoPath = $request->file('logo')->store('brands', 'public');
        }

        $brand->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'logo' => $logoPath,
            'status' => $request->status,
        ]);

        ActivityLog::log('BRAND_UPDATED', 'brands', $brand->id, "Updated brand: {$brand->name}");
        return back()->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->exists()) {
            return back()->with('error', 'Cannot delete brand with associated products.');
        }

        $name = $brand->name;
        $brand->delete();
        ActivityLog::log('BRAND_DELETED', 'brands', null, "Deleted brand: {$name}");
        return back()->with('success', 'Brand deleted successfully.');
    }
}
