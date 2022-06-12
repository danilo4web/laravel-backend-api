<?php

namespace Tests\Integration;

use App\Models\Account;
use App\Models\Admin;
use App\Models\Check;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class CheckControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->customer = Customer::factory()->create();
        $this->account = Account::factory()->create();
    }

    public function testShouldNotAccessChecksWithoutLogin()
    {
        $payload = [];
        $this->postJson("/api/v1/accounts/resume/2022-06", $payload)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testShouldApproveACheck()
    {
        $this->actingAs($this->user);

        $checks = Check::factory()->times(3)->create();
        $administrator = Admin::factory()->create();

        foreach ($checks as $check) {
            if ($check['status'] === 'pending') {
                $payload = [
                    'admin_id' => $administrator['id']
                ];
                $this->putJson("/api/v1/checks/" . $check['id'] . "/approve", $payload)
                    ->assertStatus(Response::HTTP_OK)
                    ->assertJson(['message' => 'Check approved: ' . $check['id']]);
            }
        }
    }

    public function testShouldNotApproveACheckNotPending()
    {
        $this->actingAs($this->user);
        $administrator = Admin::factory()->create();
        $check = Check::factory()->create();

        $payload = [
            'admin_id' => $administrator['id']
        ];

        $this->putJson("/api/v1/checks/" . $check['id'] . "/reject", $payload)
            ->assertStatus(Response::HTTP_OK);

        $this->putJson("/api/v1/checks/" . $check['id'] . "/approve", $payload)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(['message' => 'Check not is pending!']);
    }

    public function testShouldListPendingChecks()
    {
        $this->actingAs($this->user);

        Check::factory()->times(5)->create();
        
        $this->getJson('api/v1/checks/status/pending')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([[
                'file',
                'description',
                'amount',
                'status'
            ]]);
    }

    public function testShouldNotListStatusInexistent()
    {
        $this->actingAs($this->user);

        Check::factory()->times(5)->create();
        
        $this->getJson('api/v1/status/invalid')
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(['message' => 'Invalid payload!']);
    }

    public function testShouldListApprovedChecks()
    {
        $this->actingAs($this->user);
        $administrator = Admin::factory()->create();

        $checks = Check::factory()->times(5)->create();

        $approvedChecks = 0;
        foreach ($checks as $check) {
            if ($check['status'] === 'pending') {
                $payload = [
                    'admin_id' => $administrator['id']
                ];

                $this->putJson("/api/v1/checks/" . $check['id'] . "/approve", $payload)
                    ->assertStatus(Response::HTTP_OK);

                $approvedChecks++;
            }
        }
        
        $this->getJson('api/v1/checks/status/approved')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount($approvedChecks)
            ->assertJsonStructure([[
                'file',
                'description',
                'amount',
                'status'
            ]]);
    }

    public function testShouldListRejectedChecks()
    {
        $this->actingAs($this->user);
        $administrator = Admin::factory()->create();

        $checks = Check::factory()->times(5)->create();

        $rejectedChecks = 0;
        foreach ($checks as $check) {
            if ($check['status'] === 'pending') {
                $payload = [
                    'admin_id' => $administrator['id'],
                    'check_id' => $check['id']
                ];

                $this->putJson("/api/v1/checks/" . $check['id'] . "/reject", $payload)
                    ->assertStatus(Response::HTTP_OK);

                $rejectedChecks++;
            }
        }
        
        $this->getJson('api/v1/checks/status/rejected')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount($rejectedChecks)
            ->assertJsonStructure([[
                'file',
                'description',
                'amount',
                'status'
            ]]);
    }

    public function testShowCheck()
    {
        $this->actingAs($this->user);

        $check = Check::factory()->create();

        $response = $this->getJson('/api/v1/checks/' . $check['id'])
            ->assertStatus(Response::HTTP_OK);
    }

    public function testStoreCheck()
    {
        $this->actingAs($this->user);

        $payload = [
            'file' => 'https://via.placeholder.com/640x480.png/00cc44?text=non',
            'description' => 'Danilo',
            'amount' => 1000.90,
            'account_id' => $this->account['id']
        ];

        $response = $this->postJson('/api/v1/checks', $payload)
            ->assertStatus(Response::HTTP_CREATED);
    }
}
