@extends('layouts.admin')

@section('title', 'Bàn Tiếp Nhận & Check-in - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="Bàn Tiếp Nhận & Check-in Ngoại Trú" subtitle="Quản lý và tiếp đón bệnh nhân đến viện theo lịch hẹn hoặc đăng ký tại quầy">
    <x-slot:actions>
        <a href="{{ route('booking.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-person-plus-fill"></i> Đăng ký ca khám tại quầy
        </a>
    </x-slot:actions>
</x-page-header>

<!-- Filter Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reception.appointments.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="small text-muted fw-bold d-block">Ngày tiếp nhận:</label>
                <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="small text-muted fw-bold d-block">Trạng thái:</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Chờ tiếp nhận (Chưa check-in)</option>
                    <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Đã tiếp nhận (Đang chờ khám)</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Đã khám hoàn tất</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Đã hủy ca</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="small text-muted fw-bold d-block">Tra cứu nhanh:</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0" placeholder="Mã hẹn, Mã BN, Họ tên hoặc SĐT...">
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-filter"></i> Lọc
                </button>
                <a href="{{ route('reception.appointments.index') }}" class="btn btn-outline-secondary btn-sm" title="Đặt lại">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Appointments Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Mã hẹn</th>
                        <th>Bệnh nhân</th>
                        <th>Chuyên khoa</th>
                        <th>Bác sĩ phụ trách</th>
                        <th>Ca & Giờ hẹn</th>
                        <th>Triệu chứng</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-3" style="width: 170px;">Thao tác Tiếp nhận</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $app)
                        <tr>
                            <td class="ps-3 fw-bold font-monospace text-primary">
                                {{ $app->appointment_code }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $app->patientProfile->full_name }}</div>
                                <div class="small text-muted">
                                    <span>Mã: <code>{{ $app->patientProfile->patient_code }}</code></span> &bull; 
                                    <span>SĐT: {{ $app->patientProfile->phone }}</span>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $app->specialty->name }}</span></td>
                            <td>
                                <div class="fw-medium">{{ $app->doctor->full_name_with_title }}</div>
                                <small class="text-muted">Phòng {{ $app->doctor->room_number }}</small>
                            </td>
                            <td>
                                <div>{{ $app->shift_name }}</div>
                                <small class="text-muted">{{ $app->appointment_time ? substr($app->appointment_time, 0, 5) : 'Trong ca' }}</small>
                            </td>
                            <td class="small text-truncate" style="max-width: 180px;" title="{{ $app->symptom_description }}">
                                {{ $app->symptom_description }}
                            </td>
                            <td>
                                <x-status-badge :status="$app->status" />
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    @if($app->status === 'pending')
                                        <form method="POST" action="{{ route('reception.appointments.checkin', $app) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success d-inline-flex align-items-center gap-1" title="Check-in tiếp đón">
                                                <i class="bi bi-check2-circle"></i> Check-in
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-outline-danger" title="Hủy lịch" data-bs-toggle="modal" data-bs-target="#cancelModal-{{ $app->id }}">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    @elseif($app->status === 'confirmed')
                                        <span class="badge bg-primary text-white">Đã Check-in</span>
                                    @elseif($app->status === 'completed')
                                        <span class="badge bg-success text-white">Đã khám</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Đã hủy</span>
                                    @endif
                                </div>

                                <!-- Cancel Modal -->
                                @if($app->status === 'pending')
                                    <div class="modal fade" id="cancelModal-{{ $app->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <form method="POST" action="{{ route('reception.appointments.cancel', $app) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold text-danger">Hủy lịch hẹn: {{ $app->appointment_code }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="small text-muted mb-2">Bệnh nhân: <strong>{{ $app->patientProfile->full_name }}</strong></p>
                                                        <label class="form-label fw-semibold small">Lý do hủy lịch khám: <span class="text-danger">*</span></label>
                                                        <textarea name="cancellation_reason" class="form-control" rows="3" required placeholder="Bệnh nhân vắng mặt, đổi giờ khám..."></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-danger btn-sm">Xác nhận hủy</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-check fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Không có lịch hẹn nào trong ngày hoặc phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($appointments->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $appointments->links() }}
        </div>
    @endif
</div>
@endsection
