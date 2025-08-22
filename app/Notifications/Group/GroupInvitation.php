<?php

namespace App\Notifications\Group;

use App\Models\GroupUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupInvitation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public GroupUser $invite)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $url = url("/groups/{$this->invite->group_id}/invitations/{$this->invite->confirmation_token}");

        return (new MailMessage)
            ->greeting("Hello {$notifiable->name}!")
            ->line("You have been invited to join the group: {$this->invite->group->name}")
            ->action('Accept Invitation', $url)
            ->line('If you do not wish to join, you can ignore this message.');

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'group_id' => $this->invite->group_id,
            'inviter'  => $this->invite->inviter->name,
            'message'  => "You were invited to join {$this->invite->group->name}.",
            'token'    => $this->invite->token,
        ];
    }

}
