<?php $__env->startSection('title', 'Tambah Pengajar'); ?>
<?php $__env->startSection('subtitle', 'Tambah akun pengajar baru Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<div class="teacher-form-page">

    <section class="form-hero">
        <div>
            <span>Teacher Account</span>
            <h1>Tambah Pengajar</h1>
            <p>Daftarkan akun pengajar baru agar dapat mengakses portal Spekta Academy.</p>
        </div>

        <a href="<?php echo e(route('admin.manajemen-pengajar.index')); ?>">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </section>

    <?php if($errors->any()): ?>
        <div class="form-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Data belum valid.</strong>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <section class="form-card">
        <div class="card-heading">
            <div>
                <h2>Form Tambah Pengajar</h2>
                <p>Isi data pengajar dengan benar. Akun akan langsung bisa digunakan sesuai status yang dipilih.</p>
            </div>
        </div>

        <form action="<?php echo e(route('admin.manajemen-pengajar.store')); ?>" method="POST" class="teacher-form" id="teacherForm">
            <?php echo csrf_field(); ?>

            <div class="input-group">
                <label>Nama Lengkap</label>
                <div>
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" placeholder="Contoh: Kennedi Pane" required>
                </div>
                <small class="error-message" id="nameError" style="color: #b91c1c; font-size: 10px; display: none; margin-top: 5px;">Nama hanya boleh berisi huruf dan spasi</small>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <div>
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="teacher@gmail.com" required>
                </div>
            </div>

            <div class="input-group">
                <label>Nomor Telepon</label>
                <div>
                    <i class="fa-solid fa-phone"></i>
                    <input type="text" name="phone" id="phone" value="<?php echo e(old('phone')); ?>" placeholder="081234567890" required>
                </div>
                <small class="error-message" id="phoneError" style="color: #b91c1c; font-size: 10px; display: none; margin-top: 5px;">Nomor telepon hanya boleh berisi angka</small>
            </div>

            <div class="input-group">
                <label>Password</label>
                <div>
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" required>
                </div>
                <small class="error-message" id="passwordError" style="color: #b91c1c; font-size: 10px; display: none; margin-top: 5px;">Password harus mengandung minimal 1 huruf kapital dan 1 huruf biasa (kecil)</small>
            </div>

            <div class="input-group">
                <label>Status Akun</label>
                <div>
                    <i class="fa-solid fa-circle-check"></i>
                    <select name="is_verified" required>
                        <option value="1" <?php echo e(old('is_verified', '1') == '1' ? 'selected' : ''); ?>>Aktif</option>
                        <option value="0" <?php echo e(old('is_verified') == '0' ? 'selected' : ''); ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?php echo e(route('admin.manajemen-pengajar.index')); ?>" class="cancel-btn">
                    Batal
                </a>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fa-solid fa-user-plus"></i>
                    Simpan Pengajar
                </button>
            </div>
        </form>
    </section>
</div>

<style>
    .teacher-form-page { width: 100%; }

    .form-hero {
        background: linear-gradient(120deg, #c90025 0%, #7b001b 48%, #351225 100%);
        color: #fff;
        border-radius: 22px;
        padding: 30px 34px;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        box-shadow: 0 18px 35px rgba(134, 0, 24, .22);
    }

    .form-hero span {
        display: block;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        opacity: .78;
        margin-bottom: 8px;
    }

    .form-hero h1 {
        margin: 0 0 8px;
        font-size: 25px;
        font-weight: 900;
    }

    .form-hero p {
        margin: 0;
        font-size: 13px;
        font-weight: 500;
        opacity: .9;
    }

    .form-hero a {
        height: 42px;
        padding: 0 15px;
        display: inline-flex;
        align-items: center;
        gap: 9px;
        border-radius: 12px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.22);
        color: #fff;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
        text-decoration: none;
    }

    .form-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 700;
    }

    .form-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .form-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .form-card {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
        padding: 24px;
        width: 100%;
    }

    .card-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 24px;
    }

    .card-heading h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .card-heading p {
        margin: 7px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    .teacher-form {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .input-group label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .input-group div {
        position: relative;
    }

    .input-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
    }

    .input-group input,
    .input-group select {
        width: 100%;
        height: 48px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
        padding: 0 15px 0 42px;
        outline: none;
        color: #111827;
        font-size: 13px;
        font-weight: 700;
        font-family: inherit;
        appearance: none;
    }

    .input-group input:focus,
    .input-group select:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .input-group select {
        background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%239ca3af%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.4-12.8z%22%2F%3E%3C%2Fsvg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 10px auto;
    }

    .form-actions {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 8px;
    }

    .cancel-btn,
    .submit-btn {
        height: 46px;
        border-radius: 13px;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        font-size: 12px;
        font-weight: 900;
        font-family: inherit;
        text-decoration: none;
    }

    .cancel-btn {
        background: #f3f4f6;
        color: #374151;
    }

    .submit-btn {
        border: none;
        background: #d90429;
        color: #fff;
        cursor: pointer;
        box-shadow: 0 13px 25px rgba(217, 4, 41, .18);
    }

    @media (max-width: 768px) {
        .form-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .teacher-form {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .cancel-btn,
        .submit-btn {
            width: 100%;
        }
    }
</style>

<script>
    // Ambil elemen
    const nameInput = document.getElementById('name');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const form = document.getElementById('teacherForm');
    const submitBtn = document.getElementById('submitBtn');

    const nameError = document.getElementById('nameError');
    const phoneError = document.getElementById('phoneError');
    const passwordError = document.getElementById('passwordError');

    // Validasi nama: hanya huruf dan spasi
    function validateName() {
        const name = nameInput.value;
        const regex = /^[A-Za-z\s]+$/;
        if (!regex.test(name) && name !== '') {
            nameError.style.display = 'block';
            return false;
        } else {
            nameError.style.display = 'none';
            return true;
        }
    }

    // Validasi nomor telepon: hanya angka
    function validatePhone() {
        const phone = phoneInput.value;
        const regex = /^[0-9]+$/;
        if (!regex.test(phone) && phone !== '') {
            phoneError.style.display = 'block';
            return false;
        } else {
            phoneError.style.display = 'none';
            return true;
        }
    }

    // Validasi password: minimal 1 huruf kapital dan 1 huruf kecil
    function validatePassword() {
        const password = passwordInput.value;
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        if (password !== '' && (!hasUpper || !hasLower)) {
            passwordError.style.display = 'block';
            return false;
        } else {
            passwordError.style.display = 'none';
            return true;
        }
    }

    // Event listener saat input berubah
    nameInput.addEventListener('input', validateName);
    phoneInput.addEventListener('input', validatePhone);
    passwordInput.addEventListener('input', validatePassword);

    // Validasi sebelum submit
    form.addEventListener('submit', function(e) {
        const isNameValid = validateName();
        const isPhoneValid = validatePhone();
        const isPasswordValid = validatePassword();

        if (!isNameValid || !isPhoneValid || !isPasswordValid) {
            e.preventDefault(); // Hentikan pengiriman form jika validasi gagal
            alert('Harap periksa kembali data yang Anda masukkan:\n- Nama hanya boleh huruf dan spasi\n- Nomor telepon hanya angka\n- Password harus mengandung huruf kapital dan huruf biasa');
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/pengajar/create.blade.php ENDPATH**/ ?>