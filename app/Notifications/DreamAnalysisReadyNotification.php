<?php

namespace App\Notifications;

use App\Models\DreamAnalysis;
use App\Notifications\Concerns\HasSelectableChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class DreamAnalysisReadyNotification extends Notification implements ShouldQueue
{
    use HasSelectableChannels, Queueable;

    public function __construct(public DreamAnalysis $analysis) {}

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
            ->subject(__('InnerPath — تحليل حلمك جاهز'))
            ->greeting(__('تحليل جديد جاهز 🌿'))
            ->line(Str::limit((string) $this->analysis->summary, 180))
            ->line(__('توجد مهام يومية/أسبوعية مقترحة بعد التحليل.'))
            ->action(__('عرض التحليل'), url('/analyses/'.$this->analysis->uuid));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'category' => 'analysis_ready',
            'emoji' => '🔍',
            'title' => __('تحليل حلمك جاهز'),
            'body' => Str::limit((string) $this->analysis->summary, 120),
            'url' => route('analyses.show', $this->analysis),
            'analysis_id' => $this->analysis->id,
        ];
    }
}
