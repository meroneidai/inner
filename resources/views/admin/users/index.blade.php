<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('إدارة المستخدمين') }}</h2></x-slot>
    <div class="ip-container space-y-3">
        @foreach($users as $user)
            <div class="ip-card flex items-center justify-between p-4">
                <div>
                    <p class="font-medium">{{ $user->name }} · {{ $user->email }}</p>
                    <p class="ip-muted">{{ $user->role }} · {{ __('أحلام') }}: {{ $user->dreams_count }} · {{ $user->is_active ? __('نشط') : __('موقوف') }}</p>
                </div>
                <form method="POST" action="{{ route('admin.users.toggle', $user) }}">@csrf<button class="ip-btn-secondary">{{ __('تبديل الحالة') }}</button></form>
            </div>
        @endforeach
        {{ $users->links() }}
    </div>
</x-app-layout>
