<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $request->merge([
            'quantity' => $this->sanitizeNumber($request->input('quantity')),
            'price' => $this->sanitizeNumber($request->input('price')),
        ]);

        $validator = Validator::make($request->all(), [
            'material_name' => 'required|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0.01|max:999999.99',
            'price' => 'required|numeric|min:0|max:999999999999.99',
            'receipt_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'quantity.required' => 'Jumlah material wajib diisi.',
            'quantity.numeric' => 'Jumlah harus berupa angka yang valid.',
            'quantity.min' => 'Jumlah minimal adalah 0.01.',
            'quantity.max' => 'Jumlah maksimal adalah 999.999,99.',
            'price.required' => 'Harga per unit wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka yang valid.',
            'price.min' => 'Harga minimal adalah 0.',
            'price.max' => 'Harga terlalu besar.',
            'receipt_photo.image' => 'File nota harus berupa gambar.',
            'receipt_photo.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'receipt_photo.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'pembelian')
                ->with('error', 'Gagal menambahkan data pembelian. Mohon periksa kembali input Anda.');
        }

        $validated = $validator->validated();
        $validated['quantity'] = round((float) $validated['quantity'], 2);
        $validated['price'] = round((float) $validated['price'], 2);

        if ($request->hasFile('receipt_photo')) {
            $photoPath = $request->file('receipt_photo')->store('receipts', 'public');
            $validated['receipt_photo'] = $photoPath;
        }

        $order->purchases()->create($validated);
        
        $currentTab = $request->input('current_tab', 'pembelian');
        return redirect()->route('orders.show', $order)->with('success', 'Data pembelian berhasil ditambahkan.')->with('active_tab', $currentTab);
    }

    public function storeMultiple(Request $request, Order $order)
    {
        $input = $request->all();

        if (isset($input['purchases']) && is_array($input['purchases'])) {
            foreach ($input['purchases'] as $index => $purchase) {
                $input['purchases'][$index]['quantity'] = $this->sanitizeNumber($purchase['quantity'] ?? null);
                $input['purchases'][$index]['price'] = $this->sanitizeNumber($purchase['price'] ?? null);
            }
            $request->merge(['purchases' => $input['purchases']]);
        }

        $validator = Validator::make($request->all(), [
            'purchases' => 'required|array|min:1',
            'purchases.*.material_name' => 'required|string|max:255',
            'purchases.*.supplier' => 'nullable|string|max:255',
            'purchases.*.quantity' => 'required|numeric|min:0.01|max:999999.99',
            'purchases.*.price' => 'required|numeric|min:0|max:999999999999.99',
            'receipt_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'purchases.required' => 'Minimal satu data pembelian harus diisi.',
            'purchases.*.material_name.required' => 'Nama material wajib diisi.',
            'purchases.*.quantity.required' => 'Jumlah material wajib diisi.',
            'purchases.*.quantity.numeric' => 'Jumlah harus berupa angka yang valid.',
            'purchases.*.quantity.min' => 'Jumlah minimal adalah 0.01.',
            'purchases.*.quantity.max' => 'Jumlah maksimal per baris adalah 999.999,99.',
            'purchases.*.price.required' => 'Harga per unit wajib diisi.',
            'purchases.*.price.numeric' => 'Harga harus berupa angka yang valid.',
            'purchases.*.price.max' => 'Harga terlalu besar.',
            'receipt_photo.image' => 'File nota harus berupa gambar.',
            'receipt_photo.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'receipt_photo.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'pembelian')
                ->with('error', 'Gagal menambahkan data pembelian. Mohon periksa kembali input Anda.');
        }

        $validated = $validator->validated();
        $purchases = $validated['purchases'];
        $createdCount = 0;

        foreach ($purchases as $purchaseData) {
            if (empty($purchaseData['material_name'])) {
                continue;
            }

            $quantity = round((float) $purchaseData['quantity'], 2);
            $price = round((float) $purchaseData['price'], 2);

            $order->purchases()->create([
                'material_name' => $purchaseData['material_name'],
                'supplier' => $purchaseData['supplier'] ?? null,
                'quantity' => $quantity,
                'price' => $price,
            ]);

            $createdCount++;
        }

        if ($request->hasFile('receipt_photo')) {
            $photoPath = $request->file('receipt_photo')->store('receipts', 'public');
            if ($createdCount > 0) {
                $lastPurchase = $order->purchases()->latest()->first();
                $lastPurchase?->update(['receipt_photo' => $photoPath]);
            }
        }

        $currentTab = $request->input('current_tab', 'pembelian');
        return redirect()->route('orders.show', $order)
            ->with('success', "Berhasil menambahkan {$createdCount} data pembelian material.")
            ->with('active_tab', $currentTab);
    }

    public function uploadReceipt(Request $request, Order $order)
    {
        $request->validate([
            'receipt_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = $request->file('receipt_photo')->store('receipts', 'public');
        $order->purchases()->create([
            'material_name' => 'Nota',
            'quantity' => 1,
            'price' => 0,
            'receipt_photo' => $photoPath,
        ]);

        $currentTab = $request->input('current_tab', 'pembelian');
        return redirect()->route('orders.show', $order)->with('success', 'Foto nota berhasil diupload.')->with('active_tab', $currentTab);
    }

    public function destroy(Purchase $purchase, Request $request)
    {
        $order = $purchase->order;
        $purchase->delete();
        $currentTab = $request->input('current_tab', 'pembelian');
        return redirect()->route('orders.show', $order)->with('success', 'Data pembelian berhasil dihapus.')->with('active_tab', $currentTab);
    }

    protected function sanitizeNumber($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $clean = preg_replace('/[^\d.,-]/', '', (string) $value);
        if ($clean === '' || $clean === null) {
            return null;
        }

        $clean = str_replace(['.', ','], ['', '.'], $clean);

        return $clean;
    }
}