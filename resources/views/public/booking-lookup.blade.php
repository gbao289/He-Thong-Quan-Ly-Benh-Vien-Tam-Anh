@extends('layouts.master')

@section('title', 'Tra Cứu Lịch Hẹn Khám Bệnh - Bệnh Viện Tâm Anh')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="rounded-circle bg-light text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 58px; height: 58px;">
                    <i class="bi bi-search fs-2"></i>
                </div>
                <h3 class="fw-bold text-dark mb-2">TRA CỨU TIẾN TRÌNH LỊCH HẸN KHÁM</h3>
                <p class="text-muted mb-4 small">
                    Nhập <strong>Mã lịch hẹn</strong> (VD: <code>AP-20261017-0001</code>) hoặc <strong>Số điện thoại</strong> đã đăng ký để kiểm tra trạng thái ca khám.
                </p>

                <form method="GET" action="{{ route('booking.lookup') }}" class="row g-2 justify-content-center">
                    <div class="col-md-7">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-upc-scan text-muted"></i></span>
                            <input type="text" name="keyword" value="{{ $keyword ?? '' }}" class="form-control border-start-0" placeholder="Nhập mã lịch hẹn hoặc số điện thoại..." required autofocus>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                            <i class="bi bi-search me-1"></i> Tra cứu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($appointment))
            <!-- Single exact Appointment result -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center border-bottom">
                    <div>
                        <span class="text-muted small">Phiếu hẹn:</span>
                        <strong class="fs-5 text-primary ms-1 font-monospace">{{ $appointment->appointment_code }}</strong>
                    </div>
                    <div>
                        <x-status-badge :status="$appointment->status" />
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Họ và tên bệnh nhân:</span>
                            <strong class="fs-6">{{ $appointment->patientProfile->full_name }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Mã hồ sơ:</span>
                            <code>{{ $appointment->patientProfile->patient_code }}</code>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Bác sĩ khám:</span>
                            <span class="fw-semibold text-primary">{{ $appointment->doctor->full_name_with_title }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Chuyên khoa & Phòng khám:</span>
                            <span>{{ $appointment->specialty->name }} &bull; Phòng {{ $appointment->doctor->room_number }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Ngày & Ca khám:</span>
                            <strong>{{ $appointment->appointment_date->format('d/m/Y') }}</strong> - {{ $appointment->shift_name }}
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Lý do khám:</span>
                            <span>{{ $appointment->symptom_description }}</span>
                        </div>
                        @if($appointment->cancellation_reason)
                            <div class="col-12">
                                <div class="alert alert-danger py-2 small mb-0">
                                    <strong>Lý do hủy ca:</strong> {{ $appointment->cancellation_reason }}
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($appointment->medicalRecord)
                        <div class="p-3 bg-light rounded-3 mt-3 border">
                            <div class="d-flex align-items-center gap-2 mb-2 text-success fw-bold">
                                <i class="bi bi-file-earmark-medical-fill fs-5"></i>
                                <span>KẾT QUẢ KHÁM BỆNH & ĐƠN THUỐC</span>
                            </div>
                            <div class="row g-2 small mb-3">
                                <div class="col-md-6"><strong>Mã bệnh án:</strong> <code>{{ $appointment->medicalRecord->record_code }}</code></div>
                                <div class="col-md-6"><strong>Chẩn đoán chính:</strong> {{ $appointment->medicalRecord->diagnosis }}</div>
                                @if($appointment->medicalRecord->treatment_plan)
                                    <div class="col-12"><strong>Kế hoạch điều trị:</strong> {{ $appointment->medicalRecord->treatment_plan }}</div>
                                @endif
                                @if($appointment->medicalRecord->follow_up_date)
                                    <div class="col-12 text-primary"><strong>Hẹn tái khám:</strong> {{ $appointment->medicalRecord->follow_up_date->format('d/m/Y') }}</div>
                                @endif
                            </div>

                            @if($appointment->medicalRecord->prescriptions->isNotEmpty())
                                <div class="fw-semibold small mb-1">Đơn thuốc được kê:</div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white mb-0 small">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tên thuốc</th>
                                                <th>Liều dùng</th>
                                                <th>Tần suất</th>
                                                <th>Số ngày</th>
                                                <th>Cách dùng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($appointment->medicalRecord->prescriptions as $p)
                                                <tr>
                                                    <td class="fw-semibold">{{ $p->medication_name }}</td>
                                                    <td>{{ $p->dosage }}</td>
                                                    <td>{{ $p->frequency }}</td>
                                                    <td>{{ $p->duration_days }} ngày</td>
                                                    <td>{{ $p->instructions ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        @elseif(isset($appointments) && $appointments->isNotEmpty())
            <!-- List of appointments for phone search -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        Tìm thấy {{ $appointments->count() }} lịch hẹn liên quan đến số điện thoại <code>{{ $keyword }}</code>:
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Mã hẹn</th>
                                    <th>Bệnh nhân</th>
                                    <th>Chuyên khoa / Bác sĩ</th>
                                    <th>Thời gian khám</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-3">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $app)
                                    <tr>
                                        <td class="ps-3 fw-bold font-monospace text-primary">{{ $app->appointment_code }}</td>
                                        <td>{{ $app->patientProfile->full_name }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $app->doctor->full_name_with_title }}</div>
                                            <small class="text-muted">{{ $app->specialty->name }}</small>
                                        </td>
                                        <td>
                                            <div>{{ $app->appointment_date->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $app->shift_name }}</small>
                                        </td>
                                        <td>
                                            <x-status-badge :status="$app->status" />
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('booking.lookup', ['keyword' => $app->appointment_code]) }}" class="btn btn-sm btn-outline-primary">
                                                Xem
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @elseif($keyword)
            <div class="alert alert-warning text-center p-4 shadow-sm border-0">
                <i class="bi bi-search fs-1 d-block mb-2 text-warning"></i>
                <h5 class="fw-bold">Không tìm thấy thông tin lịch hẹn!</h5>
                <p class="text-muted mb-0 small">
                    Không có lịch hẹn nào trùng khớp với mã hoặc số điện thoại <strong>"{{ $keyword }}"</strong>. Vui lòng kiểm tra lại tính chính xác của dữ liệu.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
