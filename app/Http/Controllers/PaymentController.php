<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentModel;
use App\Models\BookingModel;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function createPayment($bookingId, $paymentMethod, $amount)
    {
        try {
            $booking = BookingModel::findOrFail($bookingId);

            $payment = new PaymentModel;
            $payment->PaymentMethod = $paymentMethod;
            $payment->Amount = $amount;
            $payment->BookingID = $bookingId;
            $payment->PaymentStatus = 0; // 0 means waiting for confirmation
            $payment->save();

            Log::info('Payment created successfully:', $payment->toArray());

            return $payment;
        } catch (\Exception $e) {
            Log::error('Error creating payment:', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}