<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 固定アカウント（ログイン用）
        AdminUser::create([
            'name' => '管理者',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        // ダミー5件
        AdminUser::factory()->count(5)->create();
    }
    //

}
