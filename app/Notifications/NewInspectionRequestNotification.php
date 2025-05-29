<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
// ... other use statements

class NewInspectionRequestNotification extends Notification
{
    use Queueable;

    public $inspectionRequest;

    /**
     * Create a new notification instance.
     *
     * @param mixed
     */
    public function __construct($inspectionRequest)
    {
        $this->inspectionRequest = $inspectionRequest;
    }
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }
    

    /**
     * الحصول على طلب الفحص المرتبط بالإشعار.
     *
     * @return mixed // تأكد من نوع الإرجاع
     */
    public function getInspectionRequest() // أو يمكنك تسميتها inspectionRequest() فقط
    {
        return $this->inspectionRequest;
    }
}