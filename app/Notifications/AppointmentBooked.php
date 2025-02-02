<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentBooked extends Notification
{
    protected $appointment;

    // تمرير تفاصيل الموعد للإشعار
    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    // تحديد القناة التي سيُرسل من خلالها الإشعار
    public function via($notifiable)
    {
        return ['mail']; // يمكن إضافة قنوات أخرى مثل database أو SMS
    }

    // إعداد الرسالة التي سيتم إرسالها عبر البريد الإلكتروني
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting('Hello ' . $this->appointment->doctor->name)
                    ->line('A new appointment has been booked with you.')
                    ->line('Patient: ' . $this->appointment->patient->name)
                    ->line('Appointment Date: ' . $this->appointment->appointment_date)
                    ->line('Appointment Time: ' . $this->appointment->appointment_time)
                    ->action('View Appointment', url('/appointments/' . $this->appointment->id))
                    ->line('Thank you for using our application!');
    }

    // في حال أردت إضافة إشعارات إلى قاعدة البيانات (اختياري)
    public function toDatabase($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'patient_name' => $this->appointment->patient->name,
            'appointment_date' => $this->appointment->appointment_date,
        ];
    }
}
