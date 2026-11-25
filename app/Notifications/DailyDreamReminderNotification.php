<?php

namespace App\Notifications;

use App\Notifications\Concerns\HasSelectableChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyDreamReminderNotification extends Notification implements ShouldQueue
{
    use HasSelectableChannels, Queueable;

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

        return (new MailMessage)
            ->subject(__('InnerPath — هل سجّلت حلم اليوم؟'))
            ->greeting(__('مرحباً :name', ['name' => $name]))
            ->line(__('لم نسجّل حلماً لليوم بعد. دقيقة واحدة تكفي لالتقاط الشعور قبل أن يتبخر.'))
            ->action(__('سجّل حلمك الآن'), url('/dreams/create'))
            ->line(__('التحليل يبدأ تلقائياً بعد حفظ النص.'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'category' => 'daily_reminder',
            'emoji' => '🌙',
            'title' => __('تذكير حلم اليوم'),
            'body' => __('لم تسجّل حلماً بعد — دقيقة واحدة كافية.'),
            'url' => route('dreams.create'),
        ];
    }
}
