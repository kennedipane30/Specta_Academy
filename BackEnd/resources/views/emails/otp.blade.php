<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP Specta Academy</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #990000; text-align: center;">Specta Academy</h2>
        <hr>
        <p>Halo <strong>{{ $name }}</strong>,</p>
        <p>Terima kasih telah bergabung. Berikut adalah kode OTP Anda untuk verifikasi akun:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: bold; color: #990000; letter-spacing: 5px; border: 2px dashed #990000; padding: 10px 20px;">
                {{ $otp }}
            </span>
        </div>
        
        <p>Kode ini hanya berlaku selama <strong>10 menit</strong>. Jangan berikan kode ini kepada siapapun.</p>
        <p>Jika Anda tidak merasa melakukan pendaftaran, silakan abaikan email ini.</p>
        <br>
        <p>Salam Hangat,<br><strong>Tim Specta Academy</strong></p>
    </div>
</body>
</html>