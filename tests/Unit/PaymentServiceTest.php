<?php

namespace Tests\Unit;

use App\Services\PaymentService;
use PHPUnit\Framework\TestCase;

class PaymentServiceTest extends TestCase
{
    private PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentService();
    }

    public function test_payment_returns_correct_structure()
    {
        $result = $this->service->process(100.00);

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('amount', $result);
        $this->assertArrayHasKey('message', $result);
    }

    public function test_payment_records_correct_amount()
    {
        $result = $this->service->process(250.00);

        $this->assertEquals(250.00, $result['amount']);
    }

    public function test_payment_fails_with_zero_amount()
    {
        $result = $this->service->process(0);

        $this->assertEquals('failed', $result['status']);
    }

    public function test_payment_status_is_valid()
    {
        $result = $this->service->process(100.00);

        $this->assertContains($result['status'], ['success', 'failed']);
    }
}
