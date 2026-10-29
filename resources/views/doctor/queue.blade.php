@extends('layouts.admin')

@section('title', 'Hàng Đợi Khám Bệnh - Bác Sĩ')

@section('content')
<x-page-header title="Hàng Đợi Khám Bệnh Hôm Nay" subtitle="Danh sách bệnh nhân tiếp nhận cần khám trong ca trực của Bác sĩ" />

@if(!$doctor)
    <div class="alert alert-warning p-4 shadow-sm border-0 rounded-3">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-exclamation-triangle-fill fs-2 text-warning"></i>
            <div>
                <h5 class="fw-bold mb-1">Chưa liên kết hồ sơ Bác sĩ</h5>
                <p class="mb-0 text-muted">Tài khoản hiện tại chưa được gán với bất kỳ Bác sĩ nào trong hệ thống. Vui lòng liên hệ Quản trị viên (Admin) để cấu hình phân quyền.</p>
            </div>
        </div>
    </div>
@else
    <!-- Doctor Summary Banner -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body p-3 p-md-4">
            <div class="row align-items-center g-3">
                <div class="col-auto">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fs-2" style="width: 60px; height: 60px;">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <h4 class="fw-bold mb-0 text-dark">{{ $doctor->full_name_with_title }}</h4>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                            Phòng khám {{ $doctor->room_number }}
                        </span>
                    </div>
                    <div class="d-flex flex-wrap gap-3 small text-muted">
                        <span><i class="bi bi-diagram-3 text-primary me-1"></i> Chuyên khoa: <strong class="text-dark">{{ $doctor->specialty->name ?? '—' }}</strong></span>
                        <span><i class="bi bi-calendar-event text-primary me-1"></i> Ngày lọc: <strong>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong></span>
                        @if($doctor->user)
                            <span><i class="bi bi-envelope text-primary me-1"></i> Email: {{ $doctor->user->email }}</span>
                        @endif
                    </div>
                </div>

                @if(Auth::user()->isAdmin() && isset($allDoctors) && $allDoctors->count() > 1)
                    <div class="col-12 col-md-auto border-top border-md-top-0 pt-2 pt-md-0">
                        <form method="GET" action="{{ route('doctor.queue') }}" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="date" value="{{ $date }}">
                            <label class="small text-muted text-nowrap fw-semibold">Đổi bác sĩ:</label>
                            <select name="doctor_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                @foreach($allDoctors as $doc)
                                    <option value="{{ $doc->id }}" {{ $doctor->id === $doc->id ? 'selected' : '' }}>
                                        {{ $doc->full_name_with_title }} ({{ $doc->specialty->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold">Tổng lượt ca trực</span>
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $counts['total'] }}</h3>
                    </div>
                    <div class="rounded-circle bg-light text-primary p-2 fs-4">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-primary small fw-semibold">Đã Check-in (Chờ khám)</span>
                        <h3 class="fw-bold mb-0 mt-1 text-primary">{{ $counts['confirmed'] }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 fs-4">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-success small fw-semibold">Đã hoàn tất khám</span>
                        <h3 class="fw-bold mb-0 mt-1 text-success">{{ $counts['completed'] }}</h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 fs-4">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-warning small fw-semibold">Chưa Check-in</span>
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $counts['pending'] }}</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 fs-4">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('doctor.queue') }}" class="row g-2 align-items-end">
                @if(request('doctor_id'))
                    <input type="hidden" name="doctor_id" value="{{ request('doctor_id') }}">
                @endif

                <div class="col-12 col-md-3">
                    <label class="form-label small text-muted fw-semibold mb-1">Ngày ca trực</label>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" onchange="this.form.submit()">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label small text-muted fw-semibold mb-1">Lọc theo trạng thái</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Đã Check-in (Chờ khám)</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Chờ tiếp nhận (Pending)</option>
                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Đã hoàn tất (Completed)</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Đã hủy (Cancelled)</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label small text-muted fw-semibold mb-1">Tìm kiếm bệnh nhân</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Họ tên, SĐT, mã BN, mã hẹn..." value="{{ $search }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-2 d-flex gap-1">
                    <a href="{{ route('doctor.queue') }}" class="btn btn-sm btn-outline-secondary w-100" title="Đặt lại bộ lọc">
                        <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Queue Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-list-check text-primary me-2"></i>Danh Sách Bệnh Nhân Ca Trực
            </h5>
            <span class="small text-muted d-none d-md-inline">Ưu tiên thứ tự: Đã Check-in &rarr; Chờ tiếp nhận &rarr; Đã khám xong</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 50px;">STT</th>
                            <th>Mã hẹn</th>
                            <th>Bệnh nhân</th>
                            <th>Ca & Giờ khám</th>
                            <th>Triệu chứng ghi nhận ban đầu</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-3" style="min-width: 150px;">Thao tác Bác sĩ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $idx => $app)
                            <tr class="{{ $app->status === 'confirmed' ? 'table-primary bg-opacity-25' : '' }}">
                                <td class="ps-3 text-muted fw-bold">{{ $idx + 1 }}</td>
                                <td>
                                    <span class="fw-bold font-monospace text-primary">{{ $app->appointment_code }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $app->patientProfile->full_name }}</div>
                                    <div class="small text-muted">
                                        <code>{{ $app->patientProfile->patient_code }}</code>
                                        &bull; {{ $app->patientProfile->gender_name }}
                                        &bull; {{ $app->patientProfile->dob ? $app->patientProfile->dob->age . ' tuổi' : '' }}
                                        &bull; SĐT: {{ $app->patientProfile->phone }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $app->shift_name }}</div>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>{{ $app->appointment_time ? substr($app->appointment_time, 0, 5) : 'Trong ca' }}
                                    </small>
                                </td>
                                <td class="small" style="max-width: 250px;">
                                    <div class="text-truncate" title="{{ $app->symptom_description }}">
                                        {{ $app->symptom_description ?: 'Không có mô tả triệu chứng ban đầu' }}
                                    </div>
                                </td>
                                <td>
                                    <x-status-badge :status="$app->status" />
                                </td>
                                <td class="text-end pe-3">
                                    @if($app->status === 'confirmed')
                                        <a href="{{ route('doctor.examine', $app->id) }}" class="btn btn-sm btn-primary shadow-sm d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-stethoscope"></i>
                                            <span>Vào khám</span>
                                        </a>
                                    @elseif($app->status === 'completed')
                                        @if($app->medicalRecord)
                                            <a href="{{ route('doctor.records.show', $app->medicalRecord->id) }}" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-journal-medical"></i>
                                                <span>Xem bệnh án</span>
                                            </a>
                                        @else
                                            <span class="badge bg-success text-white py-2 px-3">
                                                <i class="bi bi-check2-circle"></i> Đã hoàn tất
                                            </span>
                                        @endif
                                    @elseif($app->status === 'pending')
                                        <span class="badge bg-warning text-dark py-2 px-2" title="Bệnh nhân chưa làm thủ tục tại bàn lễ tân">
                                            <i class="bi bi-hourglass-split"></i> Chờ tiếp nhận
                                        </span>
                                    @else
                                        <span class="badge bg-secondary py-2 px-2">Đã hủy hẹn</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    Không có lượt khám nào phù hợp với bộ lọc trong ca trực này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
