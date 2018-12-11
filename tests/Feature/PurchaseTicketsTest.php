<?php

namespace Tests\Feature;

use App\Concert;
use App\Billing\PaymentGateway;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Fakes\FakePaymentGateway;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);

        $this->response = null;
    }

    private function orderTickets($concert, $params)
    {
        return $this->json('POST', "/concerts/{$concert->id}/orders", $params);
    }

    private function assertValidationError($message)
    {
        $this->response->assertStatus(422);
        $this->response->assertSee($message);
    }

    public function testCustomerCanPurchaseConcertTickets()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 3250]);

        $this->response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->response->assertStatus(201);

        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        $this->assertTrue($concert->orders->contains(function($order) {
            return $order->email == 'john@example.com';
        }));

        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());
    }

    public function testEmailIsRequiredToPurchaseTickets()
    {
        $concert = factory(Concert::class)->create();

        $this->response = $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertValidationError('The email field is required.');
    }

    public function testEmailMustBeValidToPurchaseTickets()
    {
        $concert = factory(Concert::class)->create();

        $this->response = $this->orderTickets($concert, [
            'email' => 'foobar',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertValidationError('The email must be a valid email address.');
    }

    public function testTicketQuantityIsRequiredToPurchaseTickets()
    {
        $concert = factory(Concert::class)->create();

        $this->response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertValidationError('The ticket quantity field is required.');
    }

    public function testTicketQuantityMustBeAtLeast1ToPurchaseTickets()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 3250]);

        $this->response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertValidationError('The ticket quantity must be at least 1.');
    }

    public function testPaymentTokenIsRequiredToPurchaseTickets()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 3250]);

        $this->response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 0
        ]);

        $this->assertValidationError('The payment token field is required.');
    }
}
