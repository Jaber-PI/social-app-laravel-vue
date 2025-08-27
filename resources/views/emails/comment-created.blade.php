<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>New Comment</title>
</head>

<body style="font-family: Arial, sans-serif; background-color:#f4f4f7; padding:20px; margin:0;">
    <table align="center" width="600" cellpadding="0" cellspacing="0"
        style="background:#ffffff; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <tr>
            <td
                style="padding:20px; text-align:center; background:#4f46e5; color:#ffffff; font-size:20px; font-weight:bold; border-top-left-radius:8px; border-top-right-radius:8px;">
                {{ $title }}
            </td>
        </tr>
        <tr>
            <td style="padding:25px; color:#333333; font-size:15px; line-height:1.6;">
                <p>Hi {{ $notifiable->name }},</p>

                <p>{{ $subject }} :</p>

                <blockquote
                    style="margin:15px 0; padding:15px; background:#f9f9f9; border-left:5px solid #4f46e5; font-style:italic;">
                    {{ $comment }}
                </blockquote>

                <p style="margin-top:25px; text-align:center;">
                    <a href="{{ $postUrl }}"
                        style="background:#4f46e5; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:6px; font-weight:bold; display:inline-block;">
                        View Comment
                    </a>
                </p>

                <p style="margin-top:30px; font-size:13px; color:#777;">
                    You’re receiving this email because you are member on our platform.
                    If you no longer want these notifications, update your preferences.
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
