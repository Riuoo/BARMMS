<?php

namespace App\Http\Controllers;

use App\Models\Residents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class QRCodeController
{
    /**
     * Display QR code for resident
     */
    public function show()
    {
        $userId = Session::get('user_id');
        $resident = Residents::find($userId);

        if (!$resident) {
            notify()->error('Resident not found.');
            return redirect()->route('resident.dashboard');
        }

        // Generate QR code token if not exists
        $qrCodeToken = $resident->generateQrCodeToken();
        $qrCodeData = $resident->getQrCodeData();

        return view('resident.qr-code', compact('resident', 'qrCodeData', 'qrCodeToken'));
    }

    /**
     * Verify QR code token (used when scanning)
     */
    public function verify(Request $request, $token)
    {
        $resident = Residents::where('qr_code_token', $token)->first();

        if (!$resident) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code token.',
            ], 404);
        }

        if (!$resident->active) {
            return response()->json([
                'success' => false,
                'message' => 'Resident account is inactive.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'resident' => [
                'id' => $resident->id,
                'name' => $resident->full_name,
                'email' => $resident->email,
            ],
        ]);
    }

    /**
     * Download QR code as image (for printing)
     */
    public function download()
    {
        $userId = Session::get('user_id');
        $resident = Residents::find($userId);

        if (!$resident) {
            notify()->error('Resident not found.');
            return redirect()->route('resident.dashboard');
        }

        $qrCodeData = $resident->getQrCodeData();

        // Return view that will generate QR code via JavaScript and allow download
        return view('resident.qr-code-download', compact('resident', 'qrCodeData'));
    }
}
