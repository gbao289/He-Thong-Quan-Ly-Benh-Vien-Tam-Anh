@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển Quản Trị - Bệnh Viện Tâm Anh')

@section('content')
<x-page-header title="Bảng Điều Khiển Quản Trị Trung Tâm" subtitle="Tổng quan giám sát tiếp nhận, điều phối, khám chữa bệnh ngoại trú & hồ sơ y tế" />

<!-- Hàng 1: Thẻ KPI thống kê chính -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-primary text-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold">Lịch Khám Hôm Nay</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['appointments_today'] }}</h2>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-calendar2-day fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-white-50 small d-flex justify-content-between">
                <span>{{ $stats['appointments_pending'] }} ca chờ check-in</span>
                <span>Tháng này: <strong>{{ $stats['appointments_month'] }}</strong></span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-info text-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold">Đã Check-in (Hàng Đợi)</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['appointments_confirmed'] }}</h2>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-person-check fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-white-50 small">
                <span>Đang chờ bác sĩ gọi vào phòng khám</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-success text-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold">Đã Khám Hoàn Tất</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['appointments_completed'] }}</h2>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-check2-circle fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-white-50 small d-flex justify-content-between">
                <span>{{ $stats['total_records'] }} hồ sơ bệnh án</span>
                <span>Tỷ lệ xong: <strong>{{ $stats['completion_rate'] }}%</strong></span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 bg-dark text-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white-50 small text-uppercase fw-bold">Đơn Thuốc & Tệp Y Tế</span>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['total_prescriptions'] }}</h2>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-capsule-pill fs-3 text-warning"></i>
                </div>
            </div>
            <div class="mt-2 text-white-50 small d-flex justify-content-between">
                <span>{{ $stats['total_attachments'] }} tài liệu đính kèm</span>
                <span>{{ $stats['total_doctors'] }} Bác sĩ</span>
            </div>
        </div>
    </div>
</div>

<!-- Hàng 2: Biểu đồ thống kê Chart.js -->
<div class="row g-4 mb-4">
    <!-- Biểu đồ 1: Xu hướng lịch khám 7 ngày gần nhất -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 bg-white h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-graph-up text-primary me-2"></i>Xu Hướng Tiếp Nhận Bệnh Nhân (7 Ngày Gần Đây)
                </h6>
                <span class="badge bg-primary bg-opacity-10 text-primary">Biểu đồ lượt khám</span>
            </div>
            <div class="card-body p-3">
                <div style="height: 270px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ 2: Tỷ lệ phân bổ trạng thái ca khám -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 bg-white h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-pie-chart text-success me-2"></i>Trạng Thái Lịch Khám
                </h6>
                <span class="badge bg-secondary bg-opacity-10 text-secondary">Toàn viện</span>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-center">
                <div style="height: 230px; position: relative;">
                    <canvas id="statusDoughnutChart"></canvas>
                </div>
                <div class="d-flex justify-content-around small text-center mt-3 pt-2 border-top">
                    <div>
                        <span class="badge rounded-circle bg-primary p-1"> </span>
                        <div class="text-muted">Chờ khám</div>
                        <strong>{{ $statusChartData['confirmed'] }}</strong>
                    </div>
                    <div>
                        <span class="badge rounded-circle bg-success p-1"> </span>
                        <div class="text-muted">Đã xong</div>
                        <strong>{{ $statusChartData['completed'] }}</strong>
                    </div>
                    <div>
                        <span class="badge rounded-circle bg-warning p-1"> </span>
                        <div class="text-muted">Chờ TN</div>
                        <strong>{{ $statusChartData['pending'] }}</strong>
                    </div>
                    <div>
                        <span class="badge rounded-circle bg-danger p-1"> </span>
                        <div class="text-muted">Hủy</div>
                        <strong>{{ $statusChartData['cancelled'] }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hàng 3: Phím tắt điều hướng nhanh -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.users.index') }}" class="card text-decoration-none shadow-sm border-0 p-3 bg-white hover-shadow transition">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 fs-4">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Quản lý Tài Khoản</div>
                    <small class="text-muted">{{ $stats['total_users'] }} tài khoản hệ thống</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3">
        <a href="{{ url('/admin/specialties') }}" class="card text-decoration-none shadow-sm border-0 p-3 bg-white hover-shadow transition">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 fs-4">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Chuyên Khoa Y Tế</div>
                    <small class="text-muted">{{ $stats['total_specialties'] }} chuyên khoa</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3">
        <a href="{{ route('reception.appointments.index') }}" class="card text-decoration-none shadow-sm border-0 p-3 bg-white hover-shadow transition">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 fs-4">
                    <i class="bi bi-clipboard2-check-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Bàn Tiếp Nhận</div>
                    <small class="text-muted">Check-in quầy Lễ tân</small>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3">
        <a href="{{ route('doctor.queue') }}" class="card text-decoration-none shadow-sm border-0 p-3 bg-white hover-shadow transition">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 fs-4">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Hàng Đợi Bác Sĩ</div>
                    <small class="text-muted">Vào bàn khám bệnh</small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Hàng 4: Hai bảng dữ liệu gần đây -->
<div class="row g-4">
    <!-- Bảng 1: Lịch hẹn gần đây -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history text-primary me-2"></i>Lịch Hẹn Đặt Gần Đây
                </h6>
                <a href="{{ route('reception.appointments.index') }}" class="small text-decoration-none">
                    Xem tất cả <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Mã hẹn</th>
                                <th>Bệnh nhân</th>
                                <th>Chuyên khoa / Bác sĩ</th>
                                <th>Ngày khám</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAppointments as $app)
                                <tr>
                                    <td class="ps-3 fw-bold font-monospace text-primary">
                                        {{ $app->appointment_code }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $app->patientProfile->full_name }}</div>
                                        <span class="text-muted">{{ $app->patientProfile->phone }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $app->specialty->name }}</div>
                                        <small class="text-muted">{{ $app->doctor->full_name_with_title }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $app->appointment_date->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $app->shift_name }}</small>
                                    </td>
                                    <td>
                                        <x-status-badge :status="$app->status" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Chưa có lịch hẹn nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng 2: Hồ sơ bệnh án mới lập -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-journal-check text-success me-2"></i>Bệnh Án Vừa Hoàn Tất
                </h6>
                <span class="badge bg-success bg-opacity-10 text-success">{{ $stats['total_records'] }} hồ sơ</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Mã bệnh án</th>
                                <th>Bệnh nhân</th>
                                <th>Chẩn đoán</th>
                                <th class="text-end pe-3">Xem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRecords as $rec)
                                <tr>
                                    <td class="ps-3 font-monospace fw-bold text-success">
                                        {{ $rec->record_code }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $rec->patientProfile->full_name }}</div>
                                        <small class="text-muted">{{ $rec->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border text-truncate" style="max-width: 140px;" title="{{ $rec->diagnosis }}">
                                            {{ $rec->diagnosis }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('doctor.records.show', $rec->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Chưa có ca khám nào hoàn tất hôm nay.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Biểu đồ đường xu hướng 7 ngày
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($trend7Days->pluck('label')) !!},
                datasets: [{
                    label: 'Số ca đặt khám',
                    data: {!! json_encode($trend7Days->pluck('count')) !!},
                    backgroundColor: 'rgba(2, 132, 199, 0.75)',
                    borderColor: '#0284c7',
                    borderWidth: 1.5,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // 2. Biểu đồ tròn Doughnut phân bổ trạng thái
    const statusCtx = document.getElementById('statusDoughnutChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Chờ khám (Confirmed)', 'Đã khám (Completed)', 'Chờ tiếp nhận (Pending)', 'Đã hủy (Cancelled)'],
                datasets: [{
                    data: [
                        {{ $statusChartData['confirmed'] }},
                        {{ $statusChartData['completed'] }},
                        {{ $statusChartData['pending'] }},
                        {{ $statusChartData['cancelled'] }}
                    ],
                    backgroundColor: [
                        '#0284c7', // Primary
                        '#16a34a', // Success
                        '#eab308', // Warning
                        '#dc2626'  // Danger
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '68%'
            }
        });
    }
});
</script>
@endpush
