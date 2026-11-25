<?php

namespace App\Notifications;

use App\Models\WeeklyReport;
use App\Notifications\Concerns\HasSelectableChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class WeeklyReportReadyNotification extends Notification implements ShouldQueue
{
    use HasSelectableChannels, Queueable;

    public function __construct(public WeeklyReport $report) {}

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
            ->subject(__('InnerPath — تقريرك الأسبوعي جاهز'))
            ->greeting(__('ملخص أسبوعك 📅'))
            ->line(Str::limit((string) $this->report->summary, 220))
            ->line(__('عدد الأحلام هذا الأسبوع: :count', ['count' => $this->report->dream_count]))
            ->action(__('افتح التقرير الأسبوعي'), url('/reports/weekly/'.$this->report->uuid));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'category' => 'weekly_report',
            'emoji' => '📅',
            'title' => __('تقريرك الأسبوعي جاهز'),
            'body' => Str::limit((string) $this->report->summary, 120),
            'url' => route('reports.weekly.show', $this->report),
            'report_id' => $this->report->id,
        ];
    }
}
