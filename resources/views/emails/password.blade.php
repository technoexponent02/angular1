{{--
<a href="{{url('password/reset/'.$token) }}">Click here to reset your password: </a>--}}

        <!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Template</title>
</head>

<body style="background-color:#eeeeee; padding:20px 0; margin:0;">
<table style="width:420px; max-width:100%; margin:0 auto; font-family:Arial; margin:0 auto; font-size:16px; color:#29353b;" cellpadding="12" cellspacing="0">
    <tr>
        <td style="padding:0;">
            <table style="width:100%; max-width:100%; background-color:#fff;" cellpadding="12" cellspacing="0">
                <tr>
                    <td style="padding:24px 24px 0 24px;">
                        <table style="width:100%; max-width:100%;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="text-align:center; vertical-align:top; padding:0;">
                                    <img src="{{ $profile_pic }}" alt="" style="width:80px; height:80px; border:6px solid #dedede; border-radius:100px;"/>
                                </td>
                            </tr>
                            <tr>
                                <td height="34"></td>
                            </tr>
                            <tr>
                                <td style="text-align:center; vertical-align:top; padding:0; color:#27abc9; font-size:28px;">Hi {{ $fullname }}</td>
                            </tr>
                            <tr>
                                <td height="20"></td>
                            </tr>
                            <tr>
                                <td style="text-align:center; vertical-align:top; padding:0; color:#293038; font-size:22px;">Did you forget something?</td>
                            </tr>
                            <tr>
                                <td height="40"></td>
                            </tr>
                            <tr>
                                <td style="text-align:center;">
                                    <a href="{{url('password/reset/'.$token) }}" style="display:inline-block; background-color:#27abc9; color:#ffffff; font-size:16px; line-height:24px; text-decoration:none; padding:12px 60px; border-radius:7px;"><strong>Reset Password</strong></a>
                                </td>
                            </tr>
                            <tr>
                                <td height="40"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {{--<tr>
                    <td style="background-color:#f6f6f6; padding:28px 24px; text-align:center;">
                        --}}{{--<a href="javascript:void(0);" style="color:#66717d; text-decoration:none; font-size:13px; line-height:16px;">Unsubscribe</a>--}}{{--
                    </td>
                </tr>--}}
            </table>
        </td>
    </tr>
    <tr>
        <td style="text-align:center; font-size:13px; line-height:20px; vertical-align:top; padding-top:0; color:#8e8e8e; padding:22px 24px 30px 24px;">
            Copyright &copy; 2017 <strong style="color:#545454;">Swolk.</strong> All rights reserved.
        </td>
    </tr>
</table>
</body>
</html>
