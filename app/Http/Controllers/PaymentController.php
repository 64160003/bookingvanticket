<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentModel;
use App\Models\BookingModel;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function createPayment($bookingId, $paymentMethod, $amount, $status = 0)
    {
        $payment = new PaymentModel;
        $payment->BookingID = $bookingId;
        $payment->PaymentMethod = $paymentMethod;
        $payment->Amount = $amount;
        $payment->PaymentStatus = $status;
        $payment->save();

        return $payment;
    }

    public function showConfirmation($status)
    {
        // Fetch payments based on the PaymentStatus
        $payments = PaymentModel::where('PaymentStatus', $status)->get();

        // Pass payments and status to the confirm.blade.php view
        return view('admin.confirm', [
            'payments' => $payments,
            'status' => $status
        ]);
    }

    public function showPaymentDetail($paymentId)
    {
        // Find payment by PaymentID
        $payment = PaymentModel::where('PaymentID', $paymentId)->first();

        if (!$payment) {
            return view('admin.payment-detail', [
                'message' => 'Payment not found'
            ]);
        }

        // Fetch associated booking details
        $booking = BookingModel::find($payment->BookingID);

        return view('admin.payment-detail', [
            'payment' => $payment,
            'booking' => $booking
        ]);
    }

    public function updatePaymentStatus(Request $request, $paymentId)
    {
        $payment = PaymentModel::where('PaymentID', $paymentId)->first();

        if (!$payment) {
            return redirect()->back()->with('error', 'Payment not found');
        }

        $payment->PaymentStatus = $request->input('status');
        $payment->save();

        return redirect()->route('admin.confirmation', ['status' => $payment->PaymentStatus])
            ->with('success', 'Payment status updated successfully');
    }
}