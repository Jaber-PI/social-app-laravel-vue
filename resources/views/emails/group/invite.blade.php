@component('mail::message')
# Group Invitation

Hello!

You have been invited to join the group **{{ $group->name }}** by {{ $inviter->name }}.

@component('mail::button', ['url' => $url])
Accept Invitation
@endcomponent

If you don’t want to join, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
