<?php

namespace App\Notifications;

use App\Services\Auth\EmailVerificationCodeService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationCodeNotification extends Notification
{
    public function __construct(public string $code) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $notifiable->profile?->preferred_name ?: $notifiable->name;

        return (new MailMessage)
            ->subject(__('InnerPath — رمز تأكيد حسابك'))
            ->greeting(__('مرحباً :name', ['name' => $name]))
            ->line(__('استخدم الرمز التالي لتفعيل حسابك والدخول إلى مساحتك:'))
            ->line('**'.$this->code.'**')
            ->line(__('الرمز صالح لمدة :minutes دقيقة.', [
                'minutes' => EmailVerificationCodeService::EXPIRES_MINUTES,
            ]))
            ->line(__('إذا لم تنشئ حساباً في InnerPath، يمكنك تجاهل هذه الرسالة.'));
    }
}
