<?php

namespace App\Notifications;

use App\Models\DailyCareMessage;
use App\Notifications\Concerns\HasSelectableChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class DailyCareNotification extends Notification implements ShouldQueue
{
    use HasSelectableChannels, Queueable;

    /**
     * @param  Collection<int, DailyCareMessage>  $messages
     */
    public function __construct(public Collection $messages) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->resolveChannels($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $notifiable->profile?->preferred_name ?: $notifiable->name;
        $mail = (new MailMessage)
            ->subject(__('InnerPath — رسالة عناية ليومك'))
            ->greeting(__('صباح الطمأنينة، :name', ['name' => $name]))
            ->line(__('إليك تذكيراً لطيفاً من مساحتك الآمنة:'));

        foreach ($this->messages->take(4) as $message) {
            $mail->line(($message->emoji ?: '💛').' '.$message->title.' — '.$message->body);
        }

        return $mail
            ->action(__('افتح لوحة التحكم'), url('/dashboard'))
            ->line(__('خطوة صغيرة كافية اليوم.'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $first = $this->messages->first();

        return [
            'category' => 'daily_care',
            'emoji' => $first?->emoji ?: '🌅',
            'title' => __('عناية اليوم'),
            'body' => $first?->title ?: __('رسالة عناية جديدة بانتظارك'),
            'url' => route('dashboard'),
            'messages' => $this->messages->map(fn (DailyCareMessage $m) => [
                'emoji' => $m->emoji,
                'title' => $m->title,
                'body' => $m->body,
                'tone' => $m->tone,
            ])->values()->all(),
        ];
    }
}
