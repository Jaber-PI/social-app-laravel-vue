<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Post $post, public User $user, public Group $group)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $postBody = $this->post->body;

        $subject = "{$this->user->name} created a post on the group {$this->group->name}";

        $title = "New  Post";

        $postUrl = url("groups/{$this->group->slug}/posts/{$this->post->id}");

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.post-created', [
                'title' => $title,
                'subject' => $subject,
                'post'   => $postBody,
                'creator' => $this->user,
                'postUrl'   => $postUrl,
                'notifiable' => $notifiable,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
