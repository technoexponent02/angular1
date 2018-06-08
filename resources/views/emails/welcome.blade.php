<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Template</title>
</head>

<body style="background-color:#eeeeee; padding:0; margin:0;">
<table style="width:510px; max-width:100%; margin:0 auto; font-family:Arial; margin:0 auto; font-size:16px; color:#29353b; background-color:#fff;" cellpadding="12" cellspacing="0">
    <tr>
        <td style="text-align:center; vertical-align:top; padding:32px 16px 24px 16px; background-color:#eeeeee;">
            <img src="https://swolk.com/assets/img/swolk-logo.png" alt="" style="display:inline-block; height:32px; width:auto;"/>
        </td>
    </tr>
    <tr>
        <td height="10"></td>
    </tr>
    <tr>
        <td style="text-align:center; font-size:24px; line-height:28px; vertical-align:top; padding:30px 16px 0px 16px;">
            hi! {{ $fullname }}
        </td>
    </tr>
    <tr>
        <td style="text-align:center; font-size:14px; line-height:24px; vertical-align:top;  color:#727e84; padding:20px 16px 0 16px;">
            Thanks for signing up with <strong>Swolk</strong>. To continue, please confirm your email address by clicking the button below.
        </td>
    </tr>
    <tr>
        <td height="10px"></td>
    </tr>
    <tr>
        <td style="text-align:center; font-size:24px; line-height:30px; vertical-align:top;  color:#2f2f2f; padding:10px 0; background:url(https://swolk.com/assets/img/divider-bg.png) no-repeat center center;">
            <span style="display:inline-block; padding:0 5px; background-color:#fff;"><a href="{{ url('signup/verify/' . $token) }}" style="text-align:center; display:inline-block; font-size:16px; line-height:30px; vertical-align:top; color:#ffffff; padding:15px 34px 15px 34px; background-color:#13b086; text-decoration:none; cursor:pointer; border-radius:4px;">Confirm email address</a></span>
        </td>
    </tr>
    <tr>
        <td height="10px"></td>
    </tr>
    <tr>
        <td style="padding:0;">
            <table style="width:100%;padding:0; margin:0;" cellpadding="12" cellspacing="0">
                <tr>
                    <td style="text-align:left; font-size:13px; line-height:20px; vertical-align:top; padding-top:0; color:#8e8e8e; padding:22px 16px 30px 0; background-color:#eeeeee;">
                        Copyright &copy; 2017 <strong style="color:#545454;">Swolk.</strong> All rights reserved.
                    </td>
                    <td style="text-align:right; font-size:13px; line-height:20px; vertical-align:top; color:#8e8e8e; padding:22px 0 30px 16px; background-color:#eeeeee;">
                        {{--<a href="#" style="color:#8e8e8e; text-decoration:none;">Unsubscribe</a>--}}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>