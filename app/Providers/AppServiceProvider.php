<?php

namespace App\Providers;

use App\Models\DreamEntry;
use App\Models\ProfileAnswer;
use App\Policies\DreamEntryPolicy;
use App\Policies\ProfileAnswerPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(DreamEntry::class, DreamEntryPolicy::class);
        Gate::policy(ProfileAnswer::class, ProfileAnswerPolicy::class);

        View::composer([
            'layouts.partials.sidebar',
            'layouts.partials.topbar',
            'layouts.partials.bottom-nav',
            'layouts.navigation',
        ], function ($view): void {
            $count = 0;
            if (auth()->check()) {
                $count = auth()->user()->unreadNotifications()->count();
            }
            $view->with('unreadNotificationsCount', $count);
        });
    }
}
