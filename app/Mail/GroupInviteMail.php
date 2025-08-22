<?php

namespace App\Mail;

use App\Models\GroupUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GroupInviteMail extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * Create a new message instance.
     */
    public function __construct(public GroupUser $invite) {}

    public function build()
    {
        $url = url("/groups/{$this->invite->group_id}/invitations/{$this->invite->token}");

        return $this->subject('You have been invited to join a group')
            ->markdown('emails.group.invite', [
                'group' => $this->invite->group,
                'inviter' => $this->invite->inviter,
                'url' => $url,
            ]);
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Group Invite Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.group.invite',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
