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
    public string $langue; // Stocke la langue ('ar' ou 'fr')

    /**
     * Create a new message instance.
     * Le constructeur accepte maintenant un tableau de données et la langue.
     */
    public function __construct(array $mailData, string $langue, ?Entreprise $entreprise = null)
    {
        $this->sujet = $mailData['sujet'];
        $this->corps = $mailData['corps'];
        $this->filesToAttach = $mailData['files_to_attach'] ?? [];
        $this->entreprise = $entreprise;
        $this->langue = $langue; // On sauvegarde la langue ('ar' ou 'fr')
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
     * C'est ici que la magie opère.
     */
    public function content(): Content
    {
        // CORRECTION : Le nom de la vue est construit dynamiquement.
        // Si $this->langue est 'ar', le nom de la vue sera 'emails.mail_template_ar'.
        // Si $this->langue est 'fr', le nom de la vue sera 'emails.mail_template_fr'.
        $viewName = 'emails.mail_template_' . $this->langue;

        return new Content(
            view: $viewName,
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