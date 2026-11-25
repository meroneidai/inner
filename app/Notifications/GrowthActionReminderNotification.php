<?php

namespace App\Notifications;

use App\Models\DevelopmentAction;
use App\Notifications\Concerns\HasSelectableChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class GrowthActionReminderNotification extends Notification implements ShouldQueue
{
    use HasSelectableChannels, Queueable;

    /**
     * @param  Collection<int, DevelopmentAction>  $actions
     */
    public function __construct(public Collection $actions) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->resolveChannels($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(__('InnerPath — تذكير بخطوة نمو'))
            ->greeting(__('خطوة صغيرة بانتظارك 🌱'))
            ->line(__('لديك مهام نمو معلّقة:'));

        foreach ($this->actions->take(3) as $action) {
            $mail->line('• '.$action->title);
        }

        return $mail->action(__('صفحة النمو'), url('/growth'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $first = $this->actions->first();

        return [
            'category' => 'growth_reminder',
            'emoji' => '🌱',
            'title' => __('تذكير بخطوة نمو'),
            'body' => $first?->title ?: __('لديك مهام معلّقة'),
            'url' => route('growth.index'),
            'count' => $this->actions->count(),
        ];
    }
}
