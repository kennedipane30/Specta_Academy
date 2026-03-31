@extends('layouts.spekta')
@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl border-t-8 border-[#990000]">
    <h2 class="text-xl font-bold mb-6 italic">AUDIT PENDAFTARAN: {{ $enroll->user->name }}</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- DATA LENGKAP DARI REGISTER & PROFIL -->
        <div class="space-y-3 bg-gray-50 p-6 rounded-xl">
            <h4 class="font-bold text-spekta border-b pb-2">Profil Siswa</h4>
            <p class="text-sm"><b>Nama:</b> {{ $enroll->user->name }}</p>
            <p class="text-sm"><b>NISN:</b> {{ $enroll->user->student->nisn }}</p>
            <p class="text-sm"><b>Alamat:</b> {{ $enroll->user->student->school }}</p>
            <p class="text-sm"><b>Tgl Lahir:</b> {{ $enroll->user->student->dob }}</p>
            <p class="text-sm"><b>Nama Ortu:</b> {{ $enroll->user->student->parent_name }}</p>
            <p class="text-sm"><b>WA Ortu:</b> {{ $enroll->user->student->wa_ortu }}</p>
        </div>

        <!-- BUKTI BAYAR & FORM DURASI -->
        <div>
            <h4 class="font-bold border-b pb-2 mb-4 text-spekta">Bukti Pembayaran</h4>
            <img src="{{ asset('storage/'.$enroll->payment_proof) }}" class="w-full rounded-lg shadow-md border" alt="Bukti Transfer">

            <form action="{{ route('admin.siswa.proses_aktivasi', $enroll->enrollmentsID) }}" method="POST" class="mt-6">
                @csrf
                <div class="bg-red-50 p-4 rounded-xl border border-red-200">
                    <label class="block text-xs font-bold uppercase text-gray-600 mb-2">Set Masa Aktif (Hari)</label>
                    <input type="number" name="durasi" value="30" class="w-full border p-2 rounded-lg mb-4 shadow-sm" required>
                    <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold shadow-lg hover:bg-green-700">
                        KONFIRMASI & AKTIFKAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
