<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public $comment,
        public $target,
        public $user
    ) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $commentBody = $this->comment->body;

        $isReply = $this->target instanceof \App\Models\Comment;

        $subject = $isReply
            ? "↩️  {$this->user->name} replied to your comment"
            : "💬  {$this->user->name}   commented on your post";

        $title = $isReply
            ? "New Reply on Your Comment"
            : "New Comment on Your Post";

        if ($isReply) {
            $this->target->load('commentable');
        }

        $post = $isReply ? $this->target->commentable : $this->target;

        $postUrl = url("/posts/{$post->id}");

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.comment-created', [
                'title' => $title,
                'subject' => $subject,
                'comment'   => $commentBody,
                'commenter' => $this->user,
                'postUrl'   => $postUrl,
                'notifiable' => $notifiable,
                'isReply'   => $isReply,
            ]);
    }

    public function toArray($notifiable)
    {
        $isReply = $this->target instanceof \App\Models\Comment;

        return [
            'comment_id' => $this->comment->id,
            'post_id'    => $this->comment->post_id,
            'type'       => $isReply ? 'reply' : 'comment',
            'message'    => $isReply
                ? "{$this->user->name} replied to your comment"
                : "{$this->user->name} commented on your post",
        ];
    }
}
