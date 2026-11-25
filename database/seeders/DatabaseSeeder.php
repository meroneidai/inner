<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\AI\PromptManager;
use App\Services\Profile\ProfileConsentService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(PromptManager::class)->seedDefaultPrompt();
        $this->call(ProfileQuestionBankSeeder::class);
        $this->call(ContentHubSeeder::class);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@innerpath.test'],
            [
                'name' => 'InnerPath Admin',
                'password' => 'password',
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
                'onboarding_completed_at' => now(),
            ]
        );

        $admin->profile()->firstOrCreate(['user_id' => $admin->id], [
            'preferred_name' => 'Admin',
            'profile_status' => 'stage1_complete',
            'preferred_language' => 'ar',
        ]);
        $admin->preferences()->firstOrCreate(['user_id' => $admin->id], [
            'locale' => 'ar',
        ]);
        app(ProfileConsentService::class)->ensureDefaults($admin);

        $user = User::query()->updateOrCreate(
            ['email' => 'user@innerpath.test'],
            [
                'name' => 'مستخدم تجريبي',
                'password' => 'password',
                'role' => 'user',
                'is_active' => true,
                'email_verified_at' => now(),
                'onboarding_completed_at' => now(),
            ]
        );

        $user->profile()->firstOrCreate(['user_id' => $user->id], [
            'preferred_name' => 'مريم',
            'profile_status' => 'stage1_complete',
            'preferred_language' => 'ar',
        ]);
        $user->preferences()->firstOrCreate(['user_id' => $user->id], [
            'locale' => 'ar',
        ]);
        app(ProfileConsentService::class)->ensureDefaults($user);
    }
}
