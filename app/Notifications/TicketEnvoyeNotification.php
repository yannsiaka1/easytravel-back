<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketEnvoyeNotification extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre ticket EasyTravel')
            ->greeting('Bonjour '.$notifiable->prenom.' '.$notifiable->nom)
            ->line('Votre paiement a été validé avec succès.')
            ->line('Code du billet : '.$this->ticket->code_unique)
            ->line('Prix : '.$this->ticket->prix.' FCFA')
            ->line('Heure de départ : '.$this->ticket->heure_depart)
            ->line('Trajet : '.$this->ticket->details_bus)
            ->line('Merci d’avoir choisi EasyTravel.')
            ->salutation('À bientôt !');
    }
}
