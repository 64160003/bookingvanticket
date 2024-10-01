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
        $payment = PaymentModel::findOrFail($paymentId);

        $newStatus = $request->input('status');
        if ($newStatus != $payment->PaymentStatus) {
            $payment->PaymentStatus = $newStatus;
            $payment->save();

            $statusText = $newStatus == 1 ? 'confirmed' : 'cancelled';
            $message = "Payment status updated successfully. Payment is now {$statusText}.";
        } else {
            $message = "Payment status remains unchanged.";
        }

        return redirect()->route('admin.confirmation', ['status' => $payment->PaymentStatus])
            ->with('success', $message);
    }
}