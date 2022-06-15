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

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;
    
    protected $user;
    protected $admin;
    protected $customer;
    protected $account;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->admin = Admin::find(1);
        $this->customer = Customer::factory()->create();
        $this->account = Account::factory()->create();
    }

    public function testShouldNotAccessAccountWithoutLogin()
    {
        $payload = [
            'account_id' => $this->account['id']
        ];

        $this->postJson("/api/v1/accounts/resume/22-06", $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testShouldReturnAnAccountResume()
    {
        $this->actingAs($this->user);

        $creditAmount = $this->getCreditAmount();
        $debitAmount = $this->getDebitAmount();
        $expectedBalance = $creditAmount - $debitAmount;

        $payload = [
            'account_id' => $this->account['id']
        ];

        $this->postJson("/api/v1/accounts/resume/" . date('Y') . "-" . date('m'), $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'balance' => $expectedBalance,
                'expenseAmount' => $debitAmount,
                'incomesAmount' => $creditAmount
        ]);
    }

    private function getCreditAmount(): float
    {
        $this->actingAs($this->user);
        $checks = Check::factory()->times(5)->create();

        $creditAmount = 0;
        foreach ($checks as $check) {
            if ($check['status'] === 'pending') {
                $this->putJson("/api/v1/admin/checks/" . $check['id'] . "/approve")
                    ->assertStatus(Response::HTTP_OK)
                    ->assertJson(['message' => 'Check approved: ' . $check['id']]);

                $creditAmount += $check['amount'];
            }
        }

        return $creditAmount;
    }

    private function getDebitAmount()
    {
        $transactions = Transaction::factory()->times(2)->create();

        $debitAmount = 0;
        foreach ($transactions as $transaction) {
            if ($transaction['type'] === 'debit') {
                $account = Account::find($transaction['account_id']);
                $account->balance -= $transaction['amount'];
                $account->save();

                $debitAmount += $transaction['amount'];
            }
        }

        return $debitAmount;
    }
}
