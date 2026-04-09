<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use LaravelDaily\FilaTeams\Models\TeamInvitation;
use Illuminate\Notifications\Messages\MailMessage;

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
        $acceptUrl = $this->invitation->expires_at
            ? URL::temporarySignedRoute(
                'filateams.invitations.accept',
                $this->invitation->expires_at,
                ['code' => $this->invitation->code]
            )
            : URL::signedRoute('filateams.invitations.accept', ['code' => $this->invitation->code]);

        return (new MailMessage)
            ->subject(__('filateams::filateams.mail.invitation.subject', ['team' => $this->invitation->team->name]))
            ->line(__('filateams::filateams.mail.invitation.line_invited', ['inviter' => $this->invitation->inviter->name, 'team' => $this->invitation->team->name]))
            ->action(__('filateams::filateams.mail.invitation.action_accept'), $acceptUrl)
            ->line(__('filateams::filateams.mail.invitation.line_expiry', ['date' => $this->invitation->expires_at->isoFormat('LL')]));
    }
}
