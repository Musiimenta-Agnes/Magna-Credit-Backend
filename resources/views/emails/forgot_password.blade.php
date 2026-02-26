<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
    <h2>Hello {{ $user->name }}</h2>

    <p>You requested to reset your password.</p>

    <p>Click the link below to reset it:</p>

    <a href="{{ $resetLink }}">
        Reset Password
    </a>

    <p>If you did not request this, please ignore this email.</p>
</body>
</html>