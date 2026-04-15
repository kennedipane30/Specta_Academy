@extends('layouts.spekta')
@section('title', 'Activation Form')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl border-t-8 border-[#990000]">
    <h2 class="text-xl font-bold mb-6 italic uppercase">ENROLLMENT AUDIT: {{ $enroll->user->name }}</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- STUDENT PROFILE DATA -->
        <div class="space-y-3 bg-gray-50 p-6 rounded-xl">
            <h4 class="font-bold text-spekta border-b pb-2">Student Profile</h4>
            <p class="text-sm"><b>Name:</b> {{ $enroll->user->name }}</p>
            <p class="text-sm"><b>National ID (NISN):</b> {{ $enroll->user->student->national_id_number }}</p>
            <p class="text-sm"><b>Address:</b> {{ $enroll->user->student->address }}</p>
            <p class="text-sm"><b>Date of Birth:</b> {{ $enroll->user->student->date_of_birth }}</p>
            <p class="text-sm"><b>Parent Name:</b> {{ $enroll->user->student->parent_name }}</p>
            <p class="text-sm"><b>Parent Phone:</b> {{ $enroll->user->student->parent_phone }}</p>
        </div>

        <!-- PAYMENT PROOF & ACTIVATION FORM -->
        <div>
            <h4 class="font-bold border-b pb-2 mb-4 text-spekta">Payment Proof</h4>
            <img src="{{ asset('storage/'.$enroll->payment_proof) }}" class="w-full rounded-lg shadow-md border" alt="Payment Proof">

            <!-- Menggunakan enrollment_id -->
            <form action="{{ route('admin.siswa.proses_aktivasi', $enroll->enrollment_id) }}" method="POST" class="mt-6">
                @csrf
                <div class="bg-red-50 p-4 rounded-xl border border-red-200">
                    <label class="block text-xs font-bold uppercase text-gray-600 mb-2">Set Active Period (Days)</label>
                    <input type="number" name="durasi" value="30" class="w-full border p-2 rounded-lg mb-4 shadow-sm" required>
                    <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold shadow-lg hover:bg-green-700">
                        CONFIRM & ACTIVATE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
