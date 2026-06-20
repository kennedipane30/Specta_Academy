<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi Keamanan Spekta Academy</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; padding: 40px 20px; margin: 0; -webkit-font-smoothing: antialiased;">
    <div style="max-width: 520px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">

        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="color: #c5352c; font-size: 24px; font-weight: 800; margin: 0; letter-spacing: -0.5px;">
                SPEKTA ACADEMY
            </h2>
            <div style="width: 40px; height: 3px; background-color: #2ea8ab; margin: 12px auto 0 auto; border-radius: 2px;"></div>
        </div>

        <div style="color: #334155; font-size: 15px; line-height: 1.6; text-align: left;">
            <p style="margin-top: 0;">Halo,</p>
            <p>Terima kasih telah melakukan pendaftaran di platform kami. Untuk memastikan keamanan kepemilikan akun Anda, sistem memerlukan konfirmasi identitas melalui kode keamanan di bawah ini:</p>

            <div style="background-color: #f1f5f9; border-radius: 12px; padding: 24px; text-align: center; margin: 30px 0; border: 1px dashed #cbd5e1;">
                <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #64748b; font-weight: 700; display: block; margin-bottom: 10px;">
                    KODE VERIFIKASI (OTP)
                </span>
                <h1 style="letter-spacing: 8px; color: #0f172a; font-size: 42px; font-weight: 800; margin: 0; padding-left: 8px;">
                    <?php echo e($otp); ?>

                </h1>
            </div>

            <p style="font-size: 14px; color: #475569;">
                Masa berlaku kode di atas adalah <strong>10 menit</strong> sejak email ini dikirimkan. Setelah batas waktu terlampaui, kode akan kedaluwarsa demi menjaga privasi data Anda.
            </p>
        </div>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">

        <div style="text-align: left; font-size: 12px; color: #94a3b8; line-height: 1.5;">
            <p style="margin-bottom: 8px; font-weight: 600; color: #64748b;">Mengapa Anda menerima email ini?</p>
            <p style="margin-top: 0; margin-bottom: 16px;">
                Email otomatis ini dikirimkan karena adanya permintaan registrasi akun baru menggunakan alamat email Anda di aplikasi Spekta Academy. Jika Anda tidak merasa melakukan tindakan ini, mohon abaikan pesan ini dengan aman; akun tidak akan aktif tanpa input kode di atas.
            </p>
            <p style="text-align: center; margin-top: 24px; font-size: 11px; color: #cbd5e1;">
                &copy; 2026 Spekta Academy. All Rights Reserved.
            </p>
        </div>

    </div>
</body>
</html>
<?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/emails/otp.blade.php ENDPATH**/ ?>