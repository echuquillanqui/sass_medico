<?php

namespace App\Mail;

use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CitaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Cita $cita,
        public string $tipo = 'confirmacion' // confirmacion | recordatorio
    ) {}

    public function envelope(): Envelope
    {
        $asunto = $this->tipo === 'recordatorio'
            ? 'Recordatorio de tu cita médica'
            : 'Confirmación de tu cita médica';

        return new Envelope(subject: $asunto);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cita',
            with: [
                'cita' => $this->cita,
                'tipo' => $this->tipo,
                'empresa' => $this->cita->empresa,
            ],
        );
    }
}
