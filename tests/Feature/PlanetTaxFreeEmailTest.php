<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

/**
 * Planet Tax Free mandates shopper.email in the issuance payload from 1 Oct 2026.
 * These tests cover the server-side guard in BillingsController@store that rejects
 * a tax-refund bill before any call is made to the Planet API.
 */
class PlanetTaxFreeEmailTest extends TestCase
{
    private function taxRefundBill(array $customerOverrides = [])
    {
        return [
            '_token'  => 'test',
            'billing' => [
                'tax_refund_flag' => 1,
                'customer' => array_merge([
                    'firstName'      => 'Joe',
                    'lastName'       => 'Doe',
                    'customerNumber' => '+12025550123',
                    'email'          => '',
                ], $customerOverrides),
            ],
        ];
    }

    private function postAsAdmin(array $payload)
    {
        return $this->actingAs(User::find(1))->postJson('/billing/billing', $payload);
    }

    public function testTaxRefundBillWithoutEmailIsRejectedBeforeCallingPlanet()
    {
        $res = $this->postAsAdmin($this->taxRefundBill(['email' => '']));

        $res->assertStatus(200)
            ->assertJson(['status' => 'warning', 'code' => 999])
            ->assertJsonFragment(['message' => 'A valid customer email is required for Tax Free issuance']);
    }

    public function testTaxRefundBillWithWhitespaceEmailIsRejected()
    {
        $res = $this->postAsAdmin($this->taxRefundBill(['email' => '   ']));

        $res->assertStatus(200)->assertJson(['status' => 'warning', 'code' => 999]);
    }

    public function testTaxRefundBillWithInvalidEmailIsRejected()
    {
        $res = $this->postAsAdmin($this->taxRefundBill(['email' => 'joe.doe']));

        $res->assertStatus(200)->assertJson(['status' => 'warning', 'code' => 999]);
    }

    public function testTaxRefundBillWithMissingEmailKeyIsRejected()
    {
        $payload = $this->taxRefundBill();
        unset($payload['billing']['customer']['email']);

        $res = $this->postAsAdmin($payload);

        $res->assertStatus(200)->assertJson(['status' => 'warning', 'code' => 999]);
    }
}
