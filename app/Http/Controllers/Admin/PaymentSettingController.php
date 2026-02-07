<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $settings = PaymentSetting::getSettings();
        
        return Inertia::render('Admin/PaymentSettings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'payment_instructions' => 'nullable|string',
            'qris_enabled' => 'boolean',
            'bank_transfer_enabled' => 'boolean',
            'qris_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $settings = PaymentSetting::getSettings();
        
        // Handle QRIS image upload
        if ($request->hasFile('qris_image')) {
            // Delete old image if exists
            if ($settings->qris_image && Storage::disk('public')->exists($settings->qris_image)) {
                Storage::disk('public')->delete($settings->qris_image);
            }
            
            // Store new image
            $path = $request->file('qris_image')->store('qris', 'public');
            $settings->qris_image = $path;
        }

        // Update other fields
        $settings->bank_name = $request->input('bank_name');
        $settings->account_number = $request->input('account_number');
        $settings->account_name = $request->input('account_name');
        $settings->payment_instructions = $request->input('payment_instructions');
        $settings->qris_enabled = $request->input('qris_enabled', false);
        $settings->bank_transfer_enabled = $request->input('bank_transfer_enabled', false);
        
        $settings->save();

        return redirect()->route('admin.payment-settings')->with('success', 'Pengaturan pembayaran berhasil diperbarui');
    }
}
