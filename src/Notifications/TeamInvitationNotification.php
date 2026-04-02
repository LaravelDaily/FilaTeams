<?php

namespace LaravelDaily\FilaTeams\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use LaravelDaily\FilaTeams\Models\TeamInvitation;

class TeamInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TeamInvitation $invitation,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = route('filateams.invitations.accept', $this->invitation->code);

        return (new MailMessage)
            ->subject("You've been invited to join ".$this->invitation->team->name)
            ->line($this->invitation->inviter->name.' has invited you to join the '.$this->invitation->team->name.' team.')
            ->action('Accept Invitation', $acceptUrl)
            ->line('This invitation will expire on '.$this->invitation->expires_at->format('F j, Y').'.');
    }
}
