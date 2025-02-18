<?php

namespace App\Mail;

use App\Models\Prescription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PrescriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $prescription;

    /**
     * إنشاء رسالة البريد الإلكتروني جديدة.
     *
     * @param Prescription $prescription
     * @return void
     */
    public function __construct(Prescription $prescription)
    {
        $this->prescription = $prescription;
    }

    /**
     * بناء رسالة البريد الإلكتروني.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.prescription')
                    ->subject('Your Prescription from Doctor')
                    ->with([
                        'prescription' => $this->prescription,
                    ]);
    }
}
