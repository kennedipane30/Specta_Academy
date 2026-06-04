<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kode OTP</title>
</head>
<body style="font-family: Arial; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 500px; margin: auto; background: #ffffff; padding: 30px; border-radius: 10px; text-align: center; border: 1px solid #ddd;">
        <h2 style="color: #990000;">Kode Verifikasi Anda</h2>
        <p>Gunakan kode OTP berikut untuk mengaktifkan akun Anda:</p>
        <h1 style="letter-spacing: 10px; color: #990000; font-size: 40px; margin: 20px 0;">
            <?php echo e($otp); ?>

        </h1>
        <p>Kode ini berlaku selama <b>10 menit</b>.</p>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: gray;">Jangan bagikan kode ini kepada siapa pun demi keamanan akun Anda.</p>
    </div>
</body>
</html><?php /**PATH D:\Perkuliahan\SEMESTER 4\PA ll\Specta_Academy\BackEnd\resources\views/emails/otp.blade.php ENDPATH**/ ?>