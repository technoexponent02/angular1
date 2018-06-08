<table style="width:600px; max-width:100%; margin:0 auto; font-family:Arial; margin:0 auto; font-size:16px; color:#222222; background-color:#f2f2f2;" cellpadding="12" cellspacing="0">
    <tr>
        <td style="text-align:center; vertical-align:top; padding:16px 16px 10px 16px; background-color:#505050;">
            <img src="https://swolk.com/assets/img/logo_white.png" alt="" style="display:inline-block; width:148px; height:auto;"/>
        </td>
    </tr>
    <tr>
        <td style="text-align:left; font-size:16px; line-height:22px; vertical-align:top; padding:30px 16px 0px 16px;">
            Hi <small><b>{{$email}}!</b></small>
        </td>
    </tr>
    <tr>
        <td style="text-align:center; font-size:20px; line-height:26px; vertical-align:top; padding:30px 16px 0px 16px;">
            <b>Feedback from swolk user</b>
        </td>
    </tr>
    <tr>
        <td style="text-align:center; font-size:20px; line-height:26px; vertical-align:top; padding:30px 16px 0px 16px; color:#008275;">
            Topic
        </td>
    </tr>
    <tr>
        <td style="text-align:left; font-size:13px; line-height:18px; vertical-align:top;  color:#3e3e3e; padding:10px 16px 16px 16px;">
            {{ $topic }}
        </td>
    </tr>
    <tr>
        <td style="text-align:center; font-size:20px; line-height:26px; vertical-align:top; padding:30px 16px 0px 16px; color:#008275;">
            Message
        </td>
    </tr>
    <tr>
        <td style="text-align:left; font-size:13px; line-height:18px; vertical-align:top;  color:#3e3e3e; padding:10px 16px 16px 16px;">
            {{ $feedback_message }}
        </td>
    </tr>
    <tr>
        <td height="10px"></td>
    </tr>
    <tr>
        <td style="text-align:left; font-size:13px; line-height:19px; vertical-align:top;  color:#3e3e3e; padding:16px;">
            From:<br/>
            <b>
                {{ $fullname . '@' . $username }})
            </b>
        </td>
    </tr>
    <tr>
        <td style="text-align:center; font-size:11px; line-height:18px; vertical-align:top; padding-top:0; color:#848484; padding:16px; border-top:1px solid #ddd;">
            Copyright &copy; 2016 <strong style="color:#545454;">Swolk.</strong> All rights reserved.
        </td>
    </tr>
</table>