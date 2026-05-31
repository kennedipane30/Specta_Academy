@extends('layouts.spekta')

@section('title', 'Kurasi Paket Tryout - Spekta Academy')

@section('content')
<div class="cp-page" style="padding: 20px; font-family: 'Plus Jakarta Sans', sans-serif;">

    {{-- 1. NOTIFIKASI --}}
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 15px; background: #fee2e2; color: #b91c1c;">
            <i class="fa-solid fa-circle-exclamation mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 2. HEADER HERO --}}
    <section class="tm-hero-header" style="background: linear-gradient(135deg, #111827 0%, #1e293b 100%); border-radius: 30px; padding: 40px; color: white; margin-bottom: 30px; position: relative; overflow: hidden;">
        <div style="position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <a href="{{ route('admin.tryout.index') }}" style="width: 45px; height: 45px; background: rgba(255,255,255,0.1); border-radius: 15px; display: grid; place-items: center; color: white; text-decoration: none; transition: 0.3s;">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <span style="font-size: 11px; font-weight: 800; color: #ef4444; text-transform: uppercase; letter-spacing: 2px;">Reviewing Drafts</span>
                    <h1 style="margin: 5px 0 0; font-size: 32px; font-weight: 900; letter-spacing: -1px;">{{ $class->program_name }}</h1>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 42px; font-weight: 900; line-height: 1;">{{ $drafts->count() }}</div>
                <div style="font-size: 11px; font-weight: 700; opacity: 0.6; text-transform: uppercase;">Total Soal Draf</div>
            </div>
        </div>
        <!-- Dekorasi Background -->
        <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(217, 4, 41, 0.1); border-radius: 50%; filter: blur(50px);"></div>
    </section>

    {{-- 3. FORM KONFIGURASI (PUBLISH) --}}
    <section style="background: white; border-radius: 30px; padding: 35px; box-shadow: 0 20px 40px rgba(0,0,0,0.04); margin-bottom: 40px; border: 1px solid #f1f5f9;">
        <form action="{{ route('admin.tryout.publish') }}" method="POST">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class->class_id }}">
            
            <div class="row align-items-end">
                <div class="col-lg-6 mb-3">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 12px; margin-left: 5px;">Judul Paket Tryout Resmi</label>
                    <input type="text" name="title" class="form-control premium-input" placeholder="Contoh: Tryout Akbar Nasional 2024" required>
                </div>
                <div class="col-lg-3 mb-3">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 12px; margin-left: 5px;">Durasi (Menit)</label>
                    <input type="number" name="duration" class="form-control premium-input" value="90" required>
                </div>
                <div class="col-lg-3 mb-3">
                    <button type="submit" class="btn-publish-premium">
                        <i class="fa-solid fa-paper-plane"></i> PUBLISH KE MOBILE
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- 4. DAFTAR SOAL --}}
    <div style="display: grid; gap: 20px;">
        <h4 style="font-weight: 900; color: #1e293b; margin-bottom: 10px; padding-left: 10px;">Daftar Detail Soal</h4>
        
        @foreach($drafts as $index => $d)
            <div class="soal-card">
                <div class="soal-card-header">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span class="soal-badge">SOAL #{{ $index + 1 }}</span>
                        <span class="subject-badge">{{ $d->subject_name }}</span>
                    </div>
                    <form action="{{ route('admin.tryout.draft.delete', $d->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete-soal" onclick="return confirm('Hapus soal ini?')">
                            <i class="fa-solid fa-trash-can mr-1"></i> Hapus Soal
                        </button>
                    </form>
                </div>

                <div class="soal-body">
                    <div class="soal-text">{!! $d->question !!}</div>
                    
                    <div class="options-grid">
                        @foreach(['a','b','c','d','e'] as $opt)
                            @php $isCorrect = (strtoupper($opt) == strtoupper($d->correct_answer)); @endphp
                            <div class="option-item {{ $isCorrect ? 'correct' : '' }}">
                                <span class="opt-label">{{ strtoupper($opt) }}</span>
                                <span class="opt-text">{{ $d->{'option_'.$opt} }}</span>
                                @if($isCorrect) <i class="fa-solid fa-check-circle check-icon"></i> @endif
                            </div>
                        @endforeach
                    </div>

                    @if($d->explanation)
                    <div class="explanation-box">
                        <strong>PEMBAHASAN:</strong>
                        <p>{{ $d->explanation }}</p>
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    /* Styling Input */
    .premium-input {
        height: 55px;
        border-radius: 15px;
        border: 2px solid #f1f5f9;
        background: #f8fafc;
        padding: 0 20px;
        font-weight: 600;
        color: #1e293b;
        transition: 0.3s;
    }
    .premium-input:focus {
        border-color: #d90429;
        background: white;
        box-shadow: 0 10px 20px rgba(217, 4, 41, 0.05);
    }

    /* Tombol Publish */
    .btn-publish-premium {
        width: 100%;
        height: 55px;
        background: #d90429;
        color: white;
        border: none;
        border-radius: 15px;
        font-weight: 800;
        font-size: 13px;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: 0.3s;
        box-shadow: 0 15px 30px rgba(217, 4, 41, 0.25);
    }
    .btn-publish-premium:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px rgba(217, 4, 41, 0.4);
        background: #b90324;
    }

    /* Card Soal */
    .soal-card {
        background: white;
        border-radius: 25px;
        padding: 30px;
        border: 1px solid #f1f5f9;
        transition: 0.3s;
    }
    .soal-card:hover {
        border-color: #d90429;
        transform: scale(1.005);
    }
    .soal-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f8fafc;
    }
    .soal-badge {
        background: #111827;
        color: white;
        font-size: 10px;
        font-weight: 800;
        padding: 6px 12px;
        border-radius: 8px;
    }
    .subject-badge {
        background: #fff1f2;
        color: #d90429;
        font-size: 10px;
        font-weight: 800;
        padding: 6px 12px;
        border-radius: 8px;
        text-transform: uppercase;
    }
    .btn-delete-soal {
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-delete-soal:hover { color: #ef4444; }

    /* Soal Text & Options */
    .soal-text {
        font-size: 16px;
        color: #1e293b;
        line-height: 1.7;
        margin-bottom: 25px;
        font-weight: 500;
    }
    .options-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 12px;
    }
    .option-item {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        background: #f8fafc;
        border: 1.5px solid #f1f5f9;
        border-radius: 15px;
        font-size: 14px;
        color: #475569;
        position: relative;
    }
    .option-item.correct {
        background: #f0fdf4;
        border-color: #10b981;
        color: #166534;
        font-weight: 700;
    }
    .opt-label {
        font-weight: 800;
        margin-right: 15px;
        opacity: 0.4;
    }
    .check-icon {
        position: absolute;
        right: 20px;
        color: #10b981;
        font-size: 18px;
    }

    /* Explanation */
    .explanation-box {
        margin-top: 25px;
        padding: 20px;
        background: #fffbeb;
        border-radius: 18px;
        border: 1px solid #fef3c7;
    }
    .explanation-box strong {
        display: block;
        font-size: 10px;
        color: #92400e;
        margin-bottom: 8px;
        letter-spacing: 1px;
    }
    .explanation-box p {
        font-size: 13px;
        color: #78350f;
        margin: 0;
        line-height: 1.6;
    }
</style>
@endsection