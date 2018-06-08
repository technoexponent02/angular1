<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Invitation for you | Swolk</title>
</head>

<body style="background-color:#eeeeee; padding:0; margin:0;">
<table style="width:482px; max-width:100%; margin:0 auto; font-family:Arial; margin:0 auto; font-size:16px; color:#29353b; background-color:#fff;" cellpadding="12" cellspacing="0">
	<tr>
		<td style="padding:0;">
			<table style="width:100%;padding:0; margin:0;" cellpadding="12" cellspacing="0">
				<tr>
					<td style="text-align:left; vertical-align:top; padding:32px 16px 24px 16px; background-color:#eeeeee;">
						<img src="https://swolk.com/assets/img/swolk-logo.png" alt="" style="display:inline-block; height:22px; width:auto;"/>
					</td>
					<td style="text-align:right; font-size:13px; line-height:20px; vertical-align:top; color:#8e8e8e;  padding:32px 16px 24px 16px; background-color:#eeeeee;">
						{{--<a href="#" style="color:#8e8e8e; text-decoration:none;">Unsubscribe</a>--}}
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding:16px 24px; background-color:#6db220; color:#fff; text-align:center; font-size:28px; line-height:34px; text-transform:uppercase;">
			<strong>Invitation for you</strong>
		</td>
	</tr>
	<tr>
		<td style="padding:0; border-left:1px solid #d9d9d9;">
			<table style="width:100%;padding:0; margin:0;" cellpadding="0" cellspacing="0">
				<tr>
					<td style="text-align:center; font-size:22px; line-height:36px; vertical-align:top; padding:30px 16px 0px 16px;">
						You’re receiving this invitation from <span style="color:#6db220;">{{ $fullname }}</span> to join SWOLK’s beta version.
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="color:#818c92; font-size:16px; text-align:center; padding:30px 16px;">
			{{ $invite_message }}
		</td>
	</tr>
	<tr>
		<td style="text-align:center; font-size:24px; line-height:30px; vertical-align:top;  color:#2f2f2f; padding:10px 0;">
			<a href="{{ url('signup?code=' . $uniquecode) }}" style="text-align:center; display:inline-block; font-size:16px; line-height:30px; vertical-align:top; color:#757e84; padding:11px 53px 9px 54px; background-color:#eee; border:1px solid #d9d9d9; text-decoration:none; cursor:pointer; border-radius:4px;">JOIN NOW</a>
		</td>
	</tr>
	<tr>
		<td height="10px"></td>
	</tr>
	<tr>
		<td style="padding:0; background-color:#eeeeee;">
			<img src="https://swolk.com/assets/img/main-img2.png" alt="" style="width:100%; height:auto;"/>
		</td>
	</tr>
	<!-- <tr>
        <td style="padding:0;">
            <table style="width:100%;padding:0; margin:0;" cellpadding="12" cellspacing="0">
                <tr>
                    <td style="text-align:left; font-size:13px; line-height:20px; vertical-align:top; padding-top:0; color:#8e8e8e; padding:22px 16px 30px 0; background-color:#eeeeee;">
                    Copyright &copy; 2016 <strong style="color:#545454;">Swolk.</strong> All rights reserved.
                    </td>
                    <td style="text-align:right; font-size:13px; line-height:20px; vertical-align:top; padding-top:0; color:#8e8e8e; padding:22px 0 30px 16px; background-color:#eeeeee;">
                        <a href="#" style="color:#8e8e8e; text-decoration:none;">Unsubscribe</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>-->
</table>
</body>
</html>