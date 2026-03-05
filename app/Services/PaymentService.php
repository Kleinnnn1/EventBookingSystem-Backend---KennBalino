<?php

namespace App\Services;

class PaymentService
{
    public function process(float $amount): array
    {
        // Always fail for zero or negative amount
        if ($amount <= 0) {
            return [
                'status'  => 'failed',
                'amount'  => $amount,
                'message' => 'Invalid payment amount',
            ];
        }

        $success = (rand(1, 10) <= 8); // 80% success rate

        return [
            'status'  => $success ? 'success' : 'failed',
            'amount'  => $amount,
            'message' => $success
                ? 'Payment processed successfully'
                : 'Payment failed, please try again',
        ];
    }
}
