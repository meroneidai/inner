<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('اتجاهات المشاعر') }}</h2></x-slot>
    <div class="ip-container space-y-6">
        <div class="ip-card p-6">
            <canvas id="emotionChart" height="120"></canvas>
        </div>
        <div class="grid gap-3 md:grid-cols-2">
            @foreach($byEmotion as $row)
                <div class="ip-card p-4 text-sm">
                    <p class="font-medium">{{ $row['emotion'] }}</p>
                    <p class="ip-muted">{{ __('التكرار') }}: {{ $row['count'] }} · {{ __('متوسط الشدة') }}: {{ $row['avg_intensity'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const labels = @json($byEmotion->pluck('emotion'));
            const data = @json($byEmotion->pluck('count'));
            if (!labels.length || !window.Chart) return;
            new Chart(document.getElementById('emotionChart'), {
                type: 'bar',
                data: { labels, datasets: [{ label: '{{ __('التكرار') }}', data, backgroundColor: '#879B88' }] },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });
        });
    </script>
</x-app-layout>
