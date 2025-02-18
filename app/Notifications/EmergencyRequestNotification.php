<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmergencyAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $doctor;
    protected $emergency;

    public function __construct($doctor, $emergency)
    {
        $this->doctor = $doctor;
        $this->emergency = $emergency;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // يتم إرسال الإشعار عبر البريد الإلكتروني وقاعدة البيانات
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Emergency Appointment Accepted')
            ->line('Dear ' . $notifiable->name . ',')
            ->line('Your emergency request has been accepted by Dr. ' . $this->doctor->name . '.')
            ->line('Doctor will contact you soon.')
            ->action('View Appointment', url('/appointments/' . $this->emergency->id))
            ->line('Thank you for using our service!');
    }

    public function toArray($notifiable)
    {
        return [
            'doctor_id' => $this->doctor->id,
            'doctor_name' => $this->doctor->name,
            'emergency_id' => $this->emergency->id,
            'message' => 'Your emergency request has been accepted by Dr. ' . $this->doctor->name . '.'
        ];
    }
}
