<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class CustomVerifyEmail extends VerifyEmail
{
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject(Lang::get('مرحباً بك في سوق السيارات السورية'))
            ->greeting(Lang::get('مرحباً'))
            ->line(Lang::get('شكراً لتسجيلك في سوق السيارات السورية. يرجى النقر على الزر أدناه لتأكيد عنوان بريدك الإلكتروني.'))
            ->action(Lang::get('تأكيد عنوان البريد الإلكتروني'), $url)
            ->line(Lang::get('إذا لم تقم بإنشاء حساب، فلا داعي لاتخاذ أي إجراء آخر.'))
            ->salutation(Lang::get('مع أطيب التحيات'));
    }
} 