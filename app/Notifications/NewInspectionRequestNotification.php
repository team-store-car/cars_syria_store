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
     * E-Mail-Benachrichtigung erstellen.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Neue Inspektionsanfrage')
            ->line('Sie haben eine neue Inspektionsanfrage erhalten.')
            ->line('Anfrage Details:')
            ->line('Datum: ' . $this->inspectionRequest->preferred_date)
            ->line('Status: ' . $this->inspectionRequest->status)
            ->action('Anfrage ansehen', url('/inspection-requests/' . $this->inspectionRequest->id))
            ->line('Vielen Dank für die Nutzung unserer Anwendung!');
    }

    /**
     * Daten für die Datenbankbenachrichtigung.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'inspection_request_id' => $this->inspectionRequest->id,
            'user_id' => $this->inspectionRequest->user_id,
            'preferred_datetime' => $this->inspectionRequest->preferred_datetime,
            'status' => $this->inspectionRequest->status,
            'notes' => $this->inspectionRequest->notes,
            'type' => 'inspection_request',
            'message' => 'Neue Inspektionsanfrage erhalten'
        ];
    }

    /**
     * الحصول على طلب الفحص المرتبط بالإشعار.
     *
     * @return mixed
     */
    public function getInspectionRequest()
    {
        return $this->inspectionRequest;
    }
}