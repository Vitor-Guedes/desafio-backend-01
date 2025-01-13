<?php

namespace Desafio\User\Seeders;

use Desafio\User\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $idAndPasswords = [
            '922.821.180-65',
            '734.168.520-44',
            '37.662.793/0001-14',
            '71.799.101/0001-12'
        ];

        $users = [];
        foreach ($idAndPasswords as $cpfCnpj) {
            $users[] = User::create([
                'name' => fake()->name(),
                'cpf_cnpj' => $cpfCnpj,
                'email' => fake()->email(),
                'password' => (new User)->justNumbers($cpfCnpj)
            ]);
        }
    }
}
