<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $email = 'cyprianwaclaw@gmail.com';
        $user = User::where('email', $email)->first();

        if ($user) {
            $role = Role::firstOrCreate(['name' => 'admin']);

            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
                $this->command->info("✅ Przypisano rolę 'admin' dla $email");
            } else {
                $this->command->info("ℹ️ $email już ma rolę 'admin'");
            }
        } else {
            $this->command->error("❌ Nie znaleziono użytkownika o emailu $email");
        }
    }
}
