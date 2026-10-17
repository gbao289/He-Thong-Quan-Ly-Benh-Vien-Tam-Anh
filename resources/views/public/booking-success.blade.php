@extends('layouts.master')

@section('title', 'Đặt Lịch Thành Công - Mã hẹn: ' . $appointment->appointment_code)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="card-header bg-success text-white text-center py-4">
                <div class="rounded-circle bg-white text-success d-inline-flex align-items-center justify-content-center mb-2 shadow" style="width: 58px; height: 58px;">
                    <i class="bi bi-check-lg fs-2"></i>
                </div>
                <h3 class="fw-bold mb-1">ĐẶT LỊCH KHÁM THÀNH CÔNG!</h3>
                <p class="mb-0 text-white-50">Hệ thống đã tiếp nhận yêu cầu khám bệnh của Quý khách.</p>
            </div>

            <div class="card-body p-4 p-md-5">
                <!-- Appointment Code Highlight Box -->
                <div class="p-3 bg-light border border-2 border-primary rounded-3 text-center mb-4">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Mã tra cứu lịch hẹn của Quý khách</span>
                    <div class="d-flex align-items-center justify-content-center gap-2 my-2">
                        <span class="fs-2 fw-bold text-primary font-monospace" id="appointmentCodeText">{{ $appointment->appointment_code }}</span>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2" onclick="navigator.clipboard.writeText('{{ $appointment->appointment_code }}'); alert('Đã sao chép mã hẹn!');" title="Sao chép">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <small class="text-danger">
                        <i class="bi bi-exclamation-circle-fill me-1"></i> Quý khách vui lòng chụp lại màn hình hoặc ghi nhớ mã số này để làm thủ tục tiếp nhận tại quầy.
                    </small>
                </div>

                <!-- Summary Info Table -->
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Chi tiết phiếu hẹn</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered mb-0 align-middle">
                        <tbody>
                            <tr>
                                <th class="bg-light text-muted" style="width: 35%;">Họ tên bệnh nhân:</th>
                                <td class="fw-bold">{{ $appointment->patientProfile->full_name }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Mã số hồ sơ y tế:</th>
                                <td><code>{{ $appointment->patientProfile->patient_code }}</code></td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Số điện thoại:</th>
                                <td>{{ $appointment->patientProfile->phone }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Chuyên khoa khám:</th>
                                <td><span class="badge bg-info text-dark">{{ $appointment->specialty->name }}</span></td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Bác sĩ phụ trách:</th>
                                <td class="fw-semibold text-primary">{{ $appointment->doctor->full_name_with_title }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Phòng khám dự kiến:</th>
                                <td>{{ $appointment->doctor->room_number }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Thời gian khám:</th>
                                <td>
                                    <strong>{{ $appointment->appointment_date->format('d/m/Y') }}</strong>
                                    &bull; {{ $appointment->shift_name }}
                                    @if($appointment->appointment_time)
                                        (khoảng {{ substr($appointment->appointment_time, 0, 5) }})
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Triệu chứng ban đầu:</th>
                                <td>{{ $appointment->symptom_description }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Trạng thái hiện tại:</th>
                                <td>
                                    <x-status-badge :status="$appointment->status" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Next steps guide -->
                <div class="alert alert-warning d-flex align-items-start gap-3 mb-4">
                    <i class="bi bi-signpost-2-fill fs-3 text-warning"></i>
                    <div class="small">
                        <strong>Hướng dẫn khi đến viện:</strong><br>
                        1. Quý khách vui lòng có mặt tại <strong>Quầy Lễ tân Bệnh viện Tâm Anh</strong> trước giờ khám 15 phút.<br>
                        2. Cung cấp <strong>Mã hẹn ({{ $appointment->appointment_code }})</strong> hoặc <strong>Số điện thoại</strong> để nhân viên check-in và cấp số thứ tự vào phòng khám.<br>
                        3. Đem theo CCCD/Thẻ BHYT và hồ sơ xét nghiệm trước đó (nếu có).
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 border-top">
                    <a href="{{ route('booking.lookup', ['keyword' => $appointment->appointment_code]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i> Tra cứu trạng thái phiếu hẹn
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print();">
                            <i class="bi bi-printer me-1"></i> In phiếu hẹn
                        </button>
                        <a href="{{ url('/') }}" class="btn btn-primary">
                            <i class="bi bi-house me-1"></i> Về Trang chủ
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
