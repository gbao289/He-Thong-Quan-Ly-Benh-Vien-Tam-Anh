<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WaitlistPromotedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Chúc Mừng: Bạn Đã Có Lịch Khám Chính Thức Tại Bệnh Viện Tâm Anh - ' . $this->appointment->appointment_code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.waitlist_promoted',
        );
    }
}
