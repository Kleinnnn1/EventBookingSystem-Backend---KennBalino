<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Notifications\BookingConfirmed;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    // POST /api/bookings/{id}/payment
    public function store($booking_id)
    {
        $booking = Booking::with('ticket')->find($booking_id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        if ($booking->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($booking->payment) {
            return response()->json(['message' => 'Payment already exists'], 422);
        }

        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Cannot pay for a cancelled booking'], 422);
        }

        $amount = $booking->ticket->price * $booking->quantity;
        $result = $this->paymentService->process($amount);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount'     => $amount,
            'status'     => $result['status'],
        ]);

        // ─── If payment success: confirm booking & notify customer ───
        if ($result['status'] === 'success') {
            $booking->update(['status' => 'confirmed']);

            // Fire notification (goes to queue automatically via ShouldQueue)
            $booking->user->notify(new BookingConfirmed($booking->load('ticket', 'payment')));
        }

        return response()->json([
            'message' => $result['message'],
            'payment' => $payment,
            'booking' => $booking->fresh(),
        ], 201);
    }

    // GET /api/payments/{id}
    public function show($id)
    {
        $payment = Payment::with('booking')->find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        if ($payment->booking->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($payment);
    }
}
