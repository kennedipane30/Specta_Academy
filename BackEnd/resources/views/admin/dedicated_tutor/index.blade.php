@extends('layouts.spekta')
@section('title', 'Tutor Request Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold" style="color: #990000;">
                <i class="fas fa-tasks me-2"></i>Dedicated Tutor Requests
            </h4>
        </div>
    </div>

    {{-- Alert Status Berhasil --}}
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Student</th>
                        <th>Topic</th>
                        <th>Schedule</th>
                        <th>Teacher Assignment</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tutors as $t)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $t->student->user->name ?? 'N/A' }}</div>
                            <small class="text-muted">NISN: {{ $t->student->national_id_number }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $t->material->title ?? 'General Topic' }}</span>
                            <br><small class="text-danger fw-bold">{{ $t->material->material_name }}</small>
                        </td>
                        <td>
                            <div class="small"><i class="far fa-calendar me-1"></i> {{ $t->date }}</div>
                            <div class="small text-muted"><i class="far fa-clock me-1"></i> {{ substr($t->time, 0, 5) }}</div>
                        </td>
                        <td>
                            @if($t->status == 'pending')
                                <form action="{{ route('admin.tutor.update', $t->dedicated_tutor_id) }}" method="POST" id="form-approve-{{ $t->dedicated_tutor_id }}">
                                    @csrf
                                    <select name="teacher_id" class="form-select form-select-sm" required>
                                        <option value="" disabled selected>Select assigned teacher...</option>

                                        {{-- ✨ LOGIKA FILTER: Hanya tampilkan guru yang ditugaskan di subjek ini --}}
                                        @php
                                            $qualifiedTeachers = \App\Models\TeacherAssignment::where('subject_name', $t->material->material_name)
                                                ->where('class_id', $t->material->class_id)
                                                ->with('user')
                                                ->get();
                                        @endphp

                                        @foreach($qualifiedTeachers as $assign)
                                            <option value="{{ $assign->user->usersID }}">{{ $assign->user->name }}</option>
                                        @endforeach

                                        @if($qualifiedTeachers->isEmpty())
                                            <option value="" disabled>No teacher assigned to this subject yet</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="status" value="confirmed">
                                </form>
                            @else
                                <span class="fw-bold text-primary">{{ $t->teacher->name ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($t->status == 'pending')
                                <span class="badge bg-warning text-dark px-3">PENDING</span>
                            @elseif($t->status == 'confirmed')
                                <span class="badge bg-success px-3">CONFIRMED</span>
                            @else
                                <span class="badge bg-danger px-3">REJECTED</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if($t->status == 'pending')
                                <div class="btn-group">
                                    <button type="submit" form="form-approve-{{ $t->dedicated_tutor_id }}" class="btn btn-sm btn-success">Approve</button>

                                    <form action="{{ route('admin.tutor.update', $t->dedicated_tutor_id) }}" method="POST" class="ms-1">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Reject this request?')">Reject</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted small italic">Processed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted italic">No tutor requests available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
