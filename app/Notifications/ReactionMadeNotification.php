<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReactionMadeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public $target,
        public $user
    ) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {

        $target_type = $this->target instanceof \App\Models\Comment ? 'comment' : 'post';

        $postUrl = url("posts/{$this->target->id}");

        return (new MailMessage)
            ->view('emails.reaction-made', [
                'reactor' => $this->user->name,
                'target_type' => $target_type,
                'postUrl'   => $postUrl,
                'notifiable' => $notifiable,
            ]);
    }

}
