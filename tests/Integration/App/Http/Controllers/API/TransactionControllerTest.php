<?php

namespace Tests\App\Http\Controllers\API;

use App\Models\Account;
use App\Models\Admin;
use App\Models\Check;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;
    protected $customer;
    protected $account;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::find(1);
        $this->user = User::factory()->create();
        $this->customer = Customer::factory()->create();
        $this->account = Account::factory()->create();
    }

    public function testShouldReturnDebitTransactionsPerMonth()
    {
        $this->actingAs($this->user);

        Transaction::factory()->times(5)->create();

        $payload = [
            "account_id" => 1
        ];

        $this->json('post', '/api/v1/transactions/debits/' . date('Y-m'), $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['description', 'date', 'amount']]);
    }

    public function testShouldReturnCreditTransactionsPerMonth()
    {
        $payload = [
            "account_id" => 1
        ];

        $creditInfo = $this->generateChecksAndApproveItAsCreditTransactions(10);

        $this->actingAs($this->user);

        $this->postJson('/api/v1/transactions/credits/' . date('Y') . '-' . date('m'), $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['description', 'date', 'amount']])
            ->assertJsonCount($creditInfo['processedChecks']);
    }

    public function testShouldReturnTransactionsPerMonth()
    {
        $payload = [
            "account_id" => 1
        ];

        $creditInfo = $this->generateChecksAndApproveItAsCreditTransactions(10);

        $this->actingAs($this->user);

        $this->postJson('/api/v1/transactions/month/' . date('Y') . '-' . date('m'), $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([['description', 'amount']])
            ->assertJsonCount($creditInfo['processedChecks']);
    }

    private function generateChecksAndApproveItAsCreditTransactions($quantity): array
    {
        $checks = Check::factory()->times($quantity)->create();

        $this->actingAs($this->admin);

        $creditAmount = 0;
        $processedChecks = 0;
        foreach ($checks as $check) {
            if ($check['status'] === 'pending') {
                $this->putJson("/api/v1/admin/checks/" . $check['id'] . "/approve")
                    ->assertStatus(Response::HTTP_OK);

                $creditAmount += $check['amount'];
                $processedChecks++;
            }
        }

        return ['amount' => $creditAmount, 'processedChecks' => $processedChecks];
    }

    public function testShouldAddAPurchase()
    {
        $this->actingAs($this->user);

        $info = $this->generateChecksAndApproveItAsCreditTransactions(3);

        $purchaseAmount = 1.00;
        $payload = [
            'amount' => $purchaseAmount,
            'account_id' => $this->account['id'],
            'description' => "ice cream",
            'date' => date('Y-m-d')
        ];

        $this->postJson("/api/v1/purchase", $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(['message' => 'Done! New balance is: ' . number_format($info['amount'] - $purchaseAmount, 2)]);
    }

    public function testShouldNotAddAPurchaseWithInvalidPayload()
    {
        $this->actingAs($this->user);

        $info = $this->generateChecksAndApproveItAsCreditTransactions(3);

        $purchaseAmount = 1.00;
        $payload = [
            'account_id' => $this->account['id'],
            'description' => "ice cream",
            'date' => date('Y-m-d')
        ];

        $this->postJson("/api/v1/purchase", $payload)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(['errors' => ['amount' => ['The amount field is required.']]]);
    }

    public function testShouldNotAddAPurchaseWithNotPositiveBalanceInAccount()
    {
        $this->actingAs($this->user);

        $creditsInfo = $this->generateChecksAndApproveItAsCreditTransactions(10);

        $payload = [
            'amount' => ($creditsInfo['amount'] + 1000),
            'account_id' => $this->account['id'],
            'description' => 'much money',
            'date' => date('Y-m-d')
        ];

        $this->postJson("/api/v1/purchase", $payload)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(['message' => 'Account does not have enough money!']);
    }

    public function testShowTransaction()
    {
        $this->actingAs($this->user);

        $transaction = Transaction::factory()->create();

        $this->json('get', '/api/v1/transactions/' . $transaction['id'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['description', 'amount', 'type']);
    }
}
