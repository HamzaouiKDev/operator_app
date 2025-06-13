<?php

namespace App\Mail;

use App\Models\Entreprise;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class GenericEmail extends Mailable
{
    use Queueable, SerializesModels;

    // Les propriétés publiques sont automatiquement disponibles dans la vue Blade
    public string $sujet;
    public string $corps;
    public ?Entreprise $entreprise;
    public array $filesToAttach;

    /**
     * Create a new message instance.
     */
    public function __construct(string $sujet, string $corps, array $filesToAttach = [], ?Entreprise $entreprise = null)
    {
        $this->sujet = $sujet;
        $this->corps = $corps;
        $this->entreprise = $entreprise;
        $this->filesToAttach = $filesToAttach;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->sujet,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.generic_template',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->filesToAttach as $filePath) {
            $fullPath = public_path($filePath);
            if (file_exists($fullPath)) {
                $attachments[] = Attachment::fromPath($fullPath);
            }
        }

        return $attachments;
    }
}
