@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển Quản Trị - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="Bảng Điều Khiển Quản Trị" subtitle="Tổng quan hệ thống tiếp nhận, điều phối và khám chữa bệnh ngoại trú" />

<!-- KPI Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-primary text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold">Tổng Lịch Khám Hôm Nay</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['appointments_today'] }}</h2>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-calendar2-day fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-white-50 small">
                <span>{{ $stats['appointments_pending'] }} ca đang chờ tiếp nhận</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-info text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold">Đã Check-in (Chờ khám)</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['appointments_confirmed'] }}</h2>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-person-check fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-white-50 small">
                <span>Đang trong hàng đợi các phòng khám</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-success text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold">Đã Khám Hoàn Tất</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['appointments_completed'] }}</h2>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-check-circle-fill fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-white-50 small">
                <span>Đã lập bệnh án & kê đơn thuốc</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-dark text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold">Đội Ngũ Bác Sĩ</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['total_doctors'] }}</h2>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-heart-pulse-fill fs-3 text-danger"></i>
                </div>
            </div>
            <div class="mt-2 text-white-50 small">
                <span>Thuộc {{ $stats['total_specialties'] }} chuyên khoa y tế</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Link Shortcuts -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="{{ route('admin.users.index') }}" class="card text-decoration-none shadow-sm border-0 p-3 hover-shadow">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light text-primary p-2">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Quản lý Tài khoản</div>
                    <small class="text-muted">{{ $stats['total_users'] }} người dùng</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('booking.create') }}" class="card text-decoration-none shadow-sm border-0 p-3 hover-shadow">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light text-success p-2">
                    <i class="bi bi-calendar-plus fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Đặt lịch mới</div>
                    <small class="text-muted">Cổng điều phối nhanh</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('reception.appointments.index') }}" class="card text-decoration-none shadow-sm border-0 p-3 hover-shadow">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light text-info p-2">
                    <i class="bi bi-clipboard2-check-fill fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Bàn tiếp nhận</div>
                    <small class="text-muted">Check-in tại quầy</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('booking.lookup') }}" class="card text-decoration-none shadow-sm border-0 p-3 hover-shadow">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light text-warning p-2">
                    <i class="bi bi-search fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Tra cứu lịch hẹn</div>
                    <small class="text-muted">Kiểm tra mã AP</small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Recent Appointments Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="fw-bold mb-0 text-dark">Các Lịch Hẹn Đặt Gần Đây</h5>
        <a href="{{ route('reception.appointments.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
            Xem toàn bộ lịch hẹn <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Mã lịch hẹn</th>
                        <th>Bệnh nhân</th>
                        <th>Chuyên khoa</th>
                        <th>Bác sĩ</th>
                        <th>Ngày khám</th>
                        <th>Ca khám</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAppointments as $app)
                        <tr>
                            <td class="ps-3 fw-bold font-monospace text-primary">
                                {{ $app->appointment_code }}
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $app->patientProfile->full_name }}</div>
                                <small class="text-muted">{{ $app->patientProfile->phone }}</small>
                            </td>
                            <td>{{ $app->specialty->name }}</td>
                            <td>
                                <div>{{ $app->doctor->full_name_with_title }}</div>
                                <small class="text-muted">Phòng {{ $app->doctor->room_number }}</small>
                            </td>
                            <td>{{ $app->appointment_date->format('d/m/Y') }}</td>
                            <td>{{ $app->shift_name }}</td>
                            <td>
                                <x-status-badge :status="$app->status" />
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('booking.lookup', ['keyword' => $app->appointment_code]) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Chưa có lịch hẹn nào được ghi nhận.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
