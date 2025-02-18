<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class HomeVisitAcceptedNotification extends Notification
{
    use Queueable;

    protected $doctor;
    protected $homeVisit;

    public function __construct($doctor, $homeVisit)
    {
        $this->doctor = $doctor;
        $this->homeVisit = $homeVisit;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Home Visit Request Has Been Accepted')
            ->line('Dr. ' . $this->doctor->name . ' has accepted your home visit request.')
            ->line('Address: ' . $this->homeVisit->address)
            ->line('Condition: ' . $this->homeVisit->patient_condition)
            ->line('Please be prepared for the doctor\'s arrival.')
            ->line('Thank you for using our service!');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Dr. ' . $this->doctor->name . ' has accepted your home visit request.',
            'home_visit_id' => $this->homeVisit->id,
        ];
    }
}
