@extends('layouts.spekta')

@section('title', 'Materi Saya - Spekta Academy')

@section('content')
<div class="tm-container">

    {{-- HERO --}}
    <section class="tm-hero-header">
        <div class="tm-hero-content">

            <span class="tm-pre-title">
                TEACHER PORTAL
            </span>

            <h1 class="tm-main-title">
                Materi Pembelajaran
            </h1>

            <p class="tm-sub-title">
                Pilih kelas yang Anda ampu untuk mengelola modul materi mingguan.
            </p>

        </div>

        <div class="tm-stat-box">
            <div class="tm-stat-value">
                {{ count($assignments) }}
            </div>

            <div class="tm-stat-label">
                Total Penugasan
            </div>
        </div>
    </section>



    {{-- CARD --}}
    <section class="tm-card">

        <div class="tm-card-head">

            <div>
                <h2>
                    <i class="fa-solid fa-chalkboard-user"></i>
                    Daftar Bidang Ajar
                </h2>

                <small>
                    Semua kelas yang sedang Anda ampu
                </small>
            </div>

        </div>


        <div class="tm-table-responsive">

            <table class="tm-table">

                <thead>

                <tr>
                    <th>Program Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th>Durasi</th>
                    <th class="text-end">Aksi</th>
                </tr>

                </thead>


                <tbody>

                @forelse($assignments as $assign)

                    <tr>

                        <td>

                            <div class="tm-class-info">

                                <strong>
                                    {{ $assign->classModel->program_name ?? 'Kelas' }}
                                </strong>

                                <small>
                                    ID #{{ $assign->class_id }}
                                </small>

                            </div>

                        </td>



                        <td>

                            <span class="tm-subject-pill">

                                {{ $assign->subject_name }}

                            </span>

                        </td>



                        <td>

                            <span class="tm-muted">

                                20 Minggu

                            </span>

                        </td>



                        <td class="text-end">

                            <a
                                href="{{ route('pengajar.materi.pilih', [$assign->class_id, $assign->subject_name]) }}"
                                class="tm-btn-manage"
                            >

                                Kelola Materi

                                <i class="fa-solid fa-arrow-right"></i>

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4">

                            <div class="tm-empty">

                                Belum ada penugasan mengajar.

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>




<style>

:root{

--red:#d90429;
--dark:#0f172a;
--gray:#64748b;
--light:#f8fafc;

}


.tm-container{

padding:24px;
font-family:'Plus Jakarta Sans',sans-serif;

}



/* ======================
HERO
====================== */

.tm-hero-header{

background:
linear-gradient(
135deg,
#b40018,
#52040d
);

padding:40px;

border-radius:24px;

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:28px;

color:white;

gap:20px;

flex-wrap:wrap;

}


.tm-pre-title{

font-size:12px;

font-weight:800;

letter-spacing:2px;

opacity:.8;

}


.tm-main-title{

font-size:34px;

font-weight:900;

margin:10px 0;

}


.tm-sub-title{

opacity:.9;

max-width:500px;

}



.tm-stat-box{

background:rgba(255,255,255,.15);

padding:20px;

border-radius:18px;

min-width:120px;

text-align:center;

backdrop-filter:blur(10px);

}


.tm-stat-value{

font-size:32px;

font-weight:900;

}


.tm-stat-label{

font-size:12px;

opacity:.8;

}



/* ===================
CARD
=================== */

.tm-card{

background:white;

padding:28px;

border-radius:24px;

box-shadow:
0 10px 30px rgba(0,0,0,.05);

}


.tm-card-head{

margin-bottom:20px;

}


.tm-card-head h2{

margin:0;

font-size:22px;

font-weight:800;

}


.tm-card-head small{

color:var(--gray);

}



/* ==================
TABLE
================== */

.tm-table{

width:100%;

border-collapse:collapse;

}


.tm-table th{

padding:18px;

font-size:12px;

font-weight:800;

color:#94a3b8;

text-transform:uppercase;

border-bottom:
2px solid #f1f5f9;

}


.tm-table td{

padding:22px 18px;

border-bottom:
1px solid #f1f5f9;

vertical-align:center;

}


.tm-table tr:hover{

background:#fafafa;

}



/* ===================
CLASS INFO
=================== */

.tm-class-info strong{

display:block;

font-size:15px;

font-weight:800;

}


.tm-class-info small{

color:#94a3b8;

}



/* ===================
SUBJECT
=================== */

.tm-subject-pill{

background:#fff1f2;

padding:

8px
14px;

border-radius:12px;

color:var(--red);

font-size:12px;

font-weight:800;

display:inline-block;

}



/* ===================
BUTTON
=================== */

.tm-btn-manage{

background:var(--dark);

padding:

12px
20px;

border-radius:14px;

text-decoration:none;

color:white;

font-size:13px;

font-weight:700;

display:inline-flex;

gap:8px;

align-items:center;

transition:.3s;

white-space:nowrap;

}


.tm-btn-manage:hover{

background:var(--red);

transform:
translateY(-2px);

box-shadow:
0 10px 20px rgba(
217,
4,
41,
.25
);

}



.tm-muted{

color:var(--gray);

font-weight:600;

}



.text-end{

text-align:right;

}



/* ==================
EMPTY
================== */

.tm-empty{

padding:30px;

text-align:center;

color:#94a3b8;

}



/* ==================
RESPONSIVE
================== */

@media(max-width:768px){

.tm-hero-header{

flex-direction:column;

align-items:flex-start;

}


.tm-table{

min-width:700px;

}


.tm-table-responsive{

overflow-x:auto;

}

}

</style>

@endsection