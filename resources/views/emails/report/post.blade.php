
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Post Reported</title>
</head>

<body style="background-color:#eeeeee; padding:20px 0; margin:0;">
<table style="width:510px; max-width:100%; margin:0 auto; font-family:Arial; margin:0 auto; font-size:16px; color:#29353b;" cellpadding="0" cellspacing="0">
    <tr>
        <td height="316px" style="text-align:center; vertical-align:top; padding:56px 34px 42px 34px; background:url(https://swolk.com/assets/img/bg.png); background-repeat:no-repeat; background-position:top center; background-size:100% 100%;">
            <table style="width:100%; max-width:100%;" cellpadding="10" cellspacing="0">
                <tr>
                    <td style="padding:0 16px 0 0; text-align:left;">
                        <a href="javascript:void(0);">
                            <img src="https://swolk.com/assets/img/swolk-logo.png" alt="" height="26"/>
                        </a>
                    </td>
                    <td width="78px" style="padding:0 0; text-align:right;">
                        <table style="max-width:100%;" cellpadding="10" cellspacing="0">
                            <tr>
                                <td style="padding:0 0;">
                                    <a href="https://www.facebook.com/swolkapp">
                                        <img src="https://swolk.com/assets/img/facebook.png" alt=""/>
                                    </a>
                                </td>
                                <td width="8"></td>
                                <td style="padding:0 0;">
                                    <a href="https://twitter.com/swolk_com">
                                        <img src="https://swolk.com/assets/img/twitter.png" alt=""/>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <img src="https://swolk.com/assets/img/signup-divider.png" alt=""/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" height="1"></td>
                </tr>

                <tr>
                    <td colspan="2" style="color:#3c3c3c; font-size:24px; line-height:26px;">
                        <strong>Post ID {{ $post->id }}<br/>
                            <span style="font-size:16px;">{{ $post->title ? $post->title : $post->caption }}</span>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" height="8"></td>
                </tr>
                <tr>
                    <td colspan="2" style="color:#717f8c; font-size:15px; line-height:24px;">
                        Created by : {{ $post->user->first_name . ' ' . $post->user->last_name }} / <a href="{{ url('profile/' . $post->user->username) }}">{{ '@' . $post->user->username }}</a>
                        <br/>
                        Report description : {{ $report->reports }}
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" height="6"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>