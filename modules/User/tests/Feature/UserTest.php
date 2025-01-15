<?php

use Desafio\User\Models\Account;
use Desafio\User\Models\User;
use Desafio\User\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        app(UserSeeder::class)->run();
    }

    public function test_must_be_able_to_fail_due_to_lack_of_balance()
    {
        $user = User::whereHas('account', function ($builder) {
            $builder->where('type', Account::TYPE_NORMAL);
        })->inRandomOrder()->first();

        $user->account->amount = rand(1000, 2000);
        $user->account->save();

        $destiny = User::where('id', '<>', $user->id)->inRandomOrder()->first();

        $payload = [
            "value" => 100.0,
            "payer" => $user->id,
            "payee" => $destiny->id
        ];

        $response = $this->postJson(route('api.transfer'), $payload);

        $response->assertStatus(200);
        $this->assertEquals(false, $response->json('success'));
        $this->assertEquals(__('transaction::app.transfer.fail.insufficient_amount'), $response->json('message'));
        $this->assertDatabaseHas('user_accounts', [
            'user_id' => $user->id,
            'amount' => $user->account->amount
        ]);
        $this->assertDatabaseHas('user_accounts', [
            'user_id' => $destiny->id,
            'amount' => $destiny->account->amount
        ]);
        $this->assertDatabaseHas('transactions', [
            'status' => 'fail',
            'payer' => $user->id,
            'payee' => $destiny->id,
            'type' => 'transfer'
        ]);
    }

    public function test_must_fail_because_the_payer_is_a_shopkeeper()
    {
        $user = User::whereHas('account', function ($builder) {
            $builder->where('type', Account::TYPE_SHOPKEEPER);
        })->inRandomOrder()->first();

        $user->account->amount = rand(10000, 20000);
        $user->account->save();

        $destiny = User::where('id', '<>', $user->id)->inRandomOrder()->first();

        $payload = [
            "value" => 100.0,
            "payer" => $user->id,
            "payee" => $destiny->id
        ];

        $response = $this->postJson(route('api.transfer'), $payload);

        $response->assertStatus(200);
        $this->assertEquals(false, $response->json('success'));
        $this->assertEquals(__('transaction::app.transfer.fail.account_type.shopkeeper'), $response->json('message'));
        $this->assertDatabaseHas('user_accounts', [
            'user_id' => $user->id,
            'amount' => $user->account->amount
        ]);
        $this->assertDatabaseHas('user_accounts', [
            'user_id' => $destiny->id,
            'amount' => $destiny->account->amount
        ]);
        $this->assertDatabaseHas('transactions', [
            'status' => 'fail',
            'payer' => $user->id,
            'payee' => $destiny->id,
            'type' => 'transfer'
        ]);
    }

    public function test_must_prevent_the_user_from_being_able_to_transfer_the_value_to_himself()
    {
        $user = User::whereHas('account', function ($builder) {
            $builder->where('type', Account::TYPE_NORMAL);
        })->inRandomOrder()->first();

        $user->account->amount = rand(10000, 20000);
        $user->account->save();

        $destiny = $user;

        $payload = [
            "value" => 100.0,
            "payer" => $user->id,
            "payee" => $destiny->id
        ];

        $response = $this->postJson(route('api.transfer'), $payload);

        $response->assertStatus(200);
        $this->assertEquals(false, $response->json('success'));
        $this->assertEquals(__('transaction::app.transfer.fail.invalid_payee'), $response->json('message'));
        $this->assertDatabaseHas('user_accounts', [
            'user_id' => $user->id,
            'amount' => $user->account->amount
        ]);
        $this->assertDatabaseHas('user_accounts', [
            'user_id' => $destiny->id,
            'amount' => $destiny->account->amount
        ]);
        $this->assertDatabaseHas('transactions', [
            'status' => 'fail',
            'payer' => $user->id,
            'payee' => $destiny->id,
            'type' => 'transfer'
        ]);
    }

    public function test_must_be_able_to_transfer_if_the_external_service_allows()
    {
        $user = User::whereHas('account', function ($builder) {
            $builder->where('type', Account::TYPE_NORMAL);
        })->inRandomOrder()->first();

        $amount = rand(10000, 20000);
        $user->account->amount = $amount;
        $user->account->save();

        $destiny = User::where('id', '<>', $user->id)->inRandomOrder()->first();

        $payload = [
            "value" => 100.00,
            "payer" => $user->id,
            "payee" => $destiny->id
        ];

        $response = $this->postJson(route('api.transfer'), $payload);

        $response->assertStatus(200);
        
        $allow = $response->json('success');
        if ($allow) {
            $this->assertEquals(true, $response->json('success'));
            $this->assertEquals(__('transaction::app.transfer.success'), $response->json('message'));
            $this->assertDatabaseHas('user_accounts', [
                'user_id' => $user->id,
                'amount' => $amount - (100 * 100)
            ]);
            $this->assertDatabaseHas('user_accounts', [
                'user_id' => $destiny->id,
                'amount' => 100 * 100
            ]);
            $this->assertDatabaseHas('transactions', [
                'status' => 'success',
                'payer' => $user->id,
                'payee' => $destiny->id,
                'type' => 'transfer'
            ]);
        } else {
            $this->assertEquals(false, $response->json('success'));
            $this->assertEquals(__('transaction::app.transfer.fail.external_service'), $response->json('message'));
            $this->assertDatabaseHas('user_accounts', [
                'user_id' => $user->id,
                'amount' => $user->account->amount
            ]);
            $this->assertDatabaseHas('user_accounts', [
                'user_id' => $destiny->id,
                'amount' => $destiny->account->amount
            ]);
            $this->assertDatabaseHas('transactions', [
                'status' => 'fail',
                'payer' => $user->id,
                'payee' => $destiny->id,
                'type' => 'transfer'
            ]);
        }
    }

}