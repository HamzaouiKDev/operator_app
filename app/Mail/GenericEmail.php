<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $sujet;
    public $contenuMessage;
    public $attachmentPaths; // Renommé pour accepter un tableau de chemins

    /**
     * Crée une nouvelle instance du message.
     *
     * @param string $sujet Le sujet de l'email.
     * @param string $contenuMessage Le corps du message.
     * @param array $attachmentPaths Un tableau de chemins publics vers les pièces jointes.
     */
    public function __construct($sujet, $contenuMessage, array $attachmentPaths = [])
    {
        $this->sujet = $sujet;
        $this->contenuMessage = $contenuMessage;
        $this->attachmentPaths = $attachmentPaths;
    }

    /**
     * Construit le message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->subject($this->sujet)
                      ->view('emails.generic_template');

        // ** LOGIQUE MISE À JOUR POUR PLUSIEURS PIÈCES JOINTES **
        // Si le tableau de pièces jointes n'est pas vide...
        if (!empty($this->attachmentPaths)) {
            // ... on boucle sur chaque chemin de fichier...
            foreach ($this->attachmentPaths as $filePath) {
                // ... et on attache chaque fichier à l'e-mail.
                $email->attach(public_path($filePath));
            }
        }

        return $email;
    }
}
