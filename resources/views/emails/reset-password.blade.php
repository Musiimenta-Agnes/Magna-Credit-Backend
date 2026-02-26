<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial; background:#f4f6f9; padding:20px;">

    <div style="max-width:500px; margin:auto; background:white; padding:30px; border-radius:8px;">
        <h2 style="color:#007BFF;">Magna Credit</h2>

        <p>Hello,</p>

        <p>You requested to reset your password.</p>

        <h3 style="background:#007BFF; color:white; padding:10px; text-align:center; border-radius:6px;">
            {{ $token }}
        </h3>

        <p>This code expires in 15 minutes.</p>

        <p>If you did not request this, please ignore this email.</p>

        <br>
        <p style="font-size:12px; color:gray;">
            © {{ date('Y') }} Magna Credit. All rights reserved.
        </p>
    </div>

</body>
</html>