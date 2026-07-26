<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body>
    <p>Hello,</p>
    <p>You are receiving this email because we received a request to reset the password for your account.</p>
    <p>Please click the button below to reset your password:</p>
    <table>
        <tr>
            <td align="center">
                <table>
                    <tr>
                        <td bgcolor="#007bff" style="border-radius: 3px;">
                            <a href="{{ $url }}" target="_blank" style="font-size: 16px; font-family: Arial, sans-serif; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 3px; display: inline-block; border: 1px solid #007bff;">Reset Password</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Thank you.</p>
</body>
</html>
