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
}