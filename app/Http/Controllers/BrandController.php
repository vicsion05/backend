<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /**
     * Lấy danh sách thương hiệu
     */
    public function index()
    {
        return response()->json(Brand::all());
    }

    /**
     * Thêm thương hiệu mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands,name|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $logoPath = $request->file('logo') ? $request->file('logo')->store('logos', 'public') : null;

        $brand = Brand::create([
            'name' => $request->name,
            'description' => $request->description,
            'logo' => $logoPath
        ]);

        return response()->json($brand, 201);
    }

    /**
     * Lấy thông tin một thương hiệu
     */
    public function show($id)
    {
        return response()->json(Brand::findOrFail($id));
    }

    /**
     * Cập nhật thương hiệu
     */
    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255|unique:brands,name,' . $id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $brand->logo = $request->file('logo')->store('logos', 'public');
        }

        $brand->update($request->only(['name', 'description', 'logo']));

        return response()->json($brand);
    }

    /**
     * Xóa thương hiệu
     */
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }
        $brand->delete();
        return response()->json(['message' => 'Xóa thương hiệu thành công']);
    }
}
