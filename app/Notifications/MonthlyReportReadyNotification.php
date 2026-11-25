<?php

namespace App\Notifications;

use App\Models\MonthlyReport;
use App\Notifications\Concerns\HasSelectableChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class MonthlyReportReadyNotification extends Notification implements ShouldQueue
{
    use HasSelectableChannels, Queueable;

    public function __construct(public MonthlyReport $report) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->resolveChannels($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('InnerPath — تقريرك الشهري جاهز'))
            ->greeting(__('ملخص شهرك 🌙'))
            ->line(Str::limit((string) $this->report->summary, 220))
            ->line(__('عدد الأحلام هذا الشهر: :count', ['count' => $this->report->dream_count]))
            ->action(__('افتح التقرير الشهري'), url('/reports/monthly/'.$this->report->uuid));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'category' => 'monthly_report',
            'emoji' => '🌙',
            'title' => __('تقريرك الشهري جاهز'),
            'body' => Str::limit((string) $this->report->summary, 120),
            'url' => route('reports.monthly.show', $this->report),
            'report_id' => $this->report->id,
        ];
    }
}
