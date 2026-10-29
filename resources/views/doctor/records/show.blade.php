@extends('layouts.admin')

@section('title', 'Hồ Sơ Bệnh Án ' . $record->record_code)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                @if(Auth::user()->isDoctor() || Auth::user()->isAdmin())
                    <li class="breadcrumb-item"><a href="{{ route('doctor.queue') }}" class="text-decoration-none">Hàng đợi khám</a></li>
                @else
                    <li class="breadcrumb-item"><a href="{{ route('patient.history') }}" class="text-decoration-none">Lịch sử khám bệnh</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">Chi tiết bệnh án</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">
            <i class="bi bi-file-earmark-medical text-primary me-2"></i>Hồ Sơ Bệnh Án & Đơn Thuốc
        </h3>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-1" onclick="window.print()">
            <i class="bi bi-printer"></i> In bệnh án / đơn thuốc
        </button>
        @if(Auth::user()->isDoctor() || Auth::user()->isAdmin())
            <a href="{{ route('doctor.queue') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Quay lại ca trực
            </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Cột trái: Thông tin chính Bệnh án & Đơn thuốc -->
    <div class="col-lg-8">
        <!-- Khối phiếu khám bệnh viện -->
        <div class="card shadow-sm border-0 mb-4 bg-white printable-card">
            <!-- Header chuẩn bệnh viện Tâm Anh -->
            <div class="card-header bg-white p-4 border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fs-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-hospital"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-primary mb-0 text-uppercase">Bệnh Viện Đa Khoa Tâm Anh</h5>
                                <small class="text-muted">Hệ thống Khám chữa bệnh Ngoại trú Chất lượng cao</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <div class="small text-muted">Mã số hồ sơ bệnh án:</div>
                        <h4 class="fw-bold font-monospace text-dark mb-0">{{ $record->record_code }}</h4>
                        <small class="text-muted">Ngày khám: {{ $record->created_at->format('d/m/Y - H:i') }}</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Thông tin hành chính -->
                <div class="bg-light p-3 rounded-3 mb-4 border">
                    <h6 class="fw-bold text-dark text-uppercase small mb-3 border-bottom pb-2">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>I. Thông Tin Hành Chính Bệnh Nhân
                    </h6>
                    <div class="row g-2 small">
                        <div class="col-md-6">
                            <span class="text-muted">Họ và tên:</span>
                            <strong class="text-dark fs-6">{{ $record->patientProfile->full_name }}</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted">Mã bệnh nhân:</span>
                            <strong class="text-primary font-monospace">{{ $record->patientProfile->patient_code }}</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted">Giới tính:</span>
                            <strong class="text-dark">{{ $record->patientProfile->gender_name }}</strong>
                        </div>

                        <div class="col-md-6">
                            <span class="text-muted">Ngày sinh / Tuổi:</span>
                            <strong class="text-dark">
                                {{ $record->patientProfile->dob ? $record->patientProfile->dob->format('d/m/Y') . ' (' . $record->patientProfile->dob->age . ' tuổi)' : '—' }}
                            </strong>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted">Số điện thoại:</span>
                            <strong class="text-dark">{{ $record->patientProfile->phone }}</strong>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted">Số BHYT:</span>
                            <span class="text-dark">{{ $record->patientProfile->insurance_code ?? 'Khám dịch vụ' }}</span>
                        </div>

                        <div class="col-12 mt-2">
                            <span class="text-muted">Địa chỉ:</span>
                            <span class="text-dark">{{ $record->patientProfile->address ?? 'Chưa ghi nhận' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Kết quả khám & chẩn đoán -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark text-uppercase small mb-3 border-bottom pb-2">
                        <i class="bi bi-clipboard2-pulse text-primary me-2"></i>II. Kết Quả Khám & Chẩn Đoán Xác Định
                    </h6>

                    <div class="mb-3">
                        <div class="small text-muted fw-bold mb-1">Triệu chứng lâm sàng khi tiếp nhận:</div>
                        <div class="p-3 bg-light rounded border text-dark">
                            {{ $record->symptoms }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="small text-muted fw-bold mb-1">Chẩn đoán xác định của Bác sĩ:</div>
                        <div class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded text-danger fw-bold fs-5">
                            <i class="bi bi-bookmark-check-fill me-2"></i>{{ $record->diagnosis }}
                        </div>
                    </div>

                    @if($record->clinical_notes)
                        <div class="mb-3">
                            <div class="small text-muted fw-bold mb-1">Chỉ số sinh tồn & Ghi chú cận lâm sàng:</div>
                            <div class="p-3 bg-light rounded border text-dark">
                                {{ $record->clinical_notes }}
                            </div>
                        </div>
                    @endif

                    @if($record->treatment_plan)
                        <div class="mb-3">
                            <div class="small text-muted fw-bold mb-1">Kế hoạch điều trị & Lời dặn Bác sĩ:</div>
                            <div class="p-3 bg-light rounded border text-dark">
                                {{ $record->treatment_plan }}
                            </div>
                        </div>
                    @endif

                    @if($record->follow_up_date)
                        <div class="alert alert-info border-0 d-flex align-items-center gap-2 mb-0">
                            <i class="bi bi-calendar-event fs-4 text-info"></i>
                            <div>
                                <strong>Hẹn lịch tái khám:</strong> Ngày
                                <span class="badge bg-primary fs-6">{{ $record->follow_up_date->format('d/m/Y') }}</span>
                                (Bệnh nhân vui lòng mang theo đơn thuốc và hồ sơ bệnh án này khi đi tái khám).
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Đơn thuốc chi tiết -->
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <h6 class="fw-bold text-dark text-uppercase small mb-0">
                            <i class="bi bi-capsule-pill text-primary me-2"></i>III. Đơn Thuốc Ngoại Trú (Rx)
                        </h6>
                        <span class="badge bg-secondary">{{ $record->prescriptions->count() }} loại thuốc</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light small text-center">
                                <tr>
                                    <th style="width: 45px;">STT</th>
                                    <th>Tên thuốc & Hàm lượng</th>
                                    <th>Liều dùng</th>
                                    <th>Tần suất</th>
                                    <th>Số ngày</th>
                                    <th>Cách dùng / Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($record->prescriptions as $idx => $rx)
                                    <tr>
                                        <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                                        <td>
                                            <strong class="text-dark">{{ $rx->medication_name }}</strong>
                                        </td>
                                        <td class="text-center">{{ $rx->dosage }}</td>
                                        <td>{{ $rx->frequency }}</td>
                                        <td class="text-center fw-bold text-primary">{{ $rx->duration_days }} ngày</td>
                                        <td class="small">{{ $rx->instructions ?? 'Theo chỉ định' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">
                                            Không có chỉ định kê đơn thuốc trong ca khám này.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Chữ ký bác sĩ -->
                <div class="row mt-5 pt-3">
                    <div class="col-6 text-center">
                        <div class="small text-muted mb-5">BỆNH NHÂN / NGƯỜI ĐẠI DIỆN</div>
                        <div class="fw-bold text-dark">{{ $record->patientProfile->full_name }}</div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="small text-muted mb-1">Ngày {{ $record->created_at->format('d') }} tháng {{ $record->created_at->format('m') }} năm {{ $record->created_at->format('Y') }}</div>
                        <div class="small text-muted mb-5 fw-bold">BÁC SĨ ĐIỀU TRỊ</div>
                        <div class="fw-bold text-primary">{{ $record->doctor->full_name_with_title }}</div>
                        <small class="text-muted">{{ $record->appointment->specialty->name ?? '' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột phải: Quản lý File y tế đính kèm & Thao tác -->
    <div class="col-lg-4">
        <!-- Thông tin ca khám -->
        <div class="card shadow-sm border-0 mb-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-info-circle text-primary me-2"></i>Thông Tin Bác Sĩ & Ca Khám
                </h6>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Mã ca khám:</span>
                        <strong class="font-monospace text-primary">{{ $record->appointment->appointment_code }}</strong>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Bác sĩ:</span>
                        <strong class="text-dark">{{ $record->doctor->full_name_with_title }}</strong>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Chuyên khoa:</span>
                        <strong class="text-dark">{{ $record->appointment->specialty->name }}</strong>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Phòng khám:</span>
                        <span class="badge bg-light text-dark border">Phòng {{ $record->doctor->room_number }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Trạng thái ca:</span>
                        <span class="badge bg-success">Đã khám hoàn tất</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Tệp tài liệu y tế đính kèm (Attachments) -->
        <div class="card shadow-sm border-0 mb-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-paperclip text-primary me-2"></i>Tài Liệu Y Tế Bảo Mật
                </h6>
                <span class="badge bg-primary">{{ $record->attachments->count() }} tệp</span>
            </div>
            <div class="card-body p-3">
                <p class="text-muted small mb-3">
                    <i class="bi bi-shield-lock text-success me-1"></i>
                    Tệp tin lưu trong phân vùng <code>storage/app/private</code>. Chỉ Bác sĩ phụ trách, Admin và chính Bệnh nhân mới được cấp quyền tải file.
                </p>

                <!-- Danh sách tệp đã tải lên -->
                @forelse($record->attachments as $att)
                    <div class="border rounded-3 p-3 mb-2 bg-light d-flex align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <div class="rounded bg-white p-2 border text-primary fs-4">
                                @if(str_contains($att->file_mime, 'pdf'))
                                    <i class="bi bi-file-earmark-pdf text-danger"></i>
                                @else
                                    <i class="bi bi-file-earmark-image text-primary"></i>
                                @endif
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-semibold text-truncate small text-dark" title="{{ $att->original_name }}">
                                    {{ $att->original_name }}
                                </div>
                                <div class="small text-muted">
                                    <span class="badge bg-secondary bg-opacity-25 text-dark border-0">
                                        @if($att->file_type === 'lab_result') Kết quả xét nghiệm
                                        @elseif($att->file_type === 'x_ray') Chụp X-Quang / CT
                                        @elseif($att->file_type === 'prescription_scan') Đơn thuốc scan
                                        @else Tài liệu y tế @endif
                                    </span>
                                    &bull; {{ number_format($att->file_size / 1024, 1) }} KB
                                </div>
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('attachments.download', $att->id) }}" class="btn btn-sm btn-outline-primary" title="Tải xuống an toàn">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">
                        <i class="bi bi-files fs-2 d-block mb-1 text-secondary opacity-50"></i>
                        Chưa có tài liệu đính kèm nào (xét nghiệm, X-quang,...)
                    </div>
                @endforelse

                <!-- Form Upload tệp mới (Chỉ cho Doctor hoặc Admin) -->
                @if(Auth::user()->isDoctor() || Auth::user()->isAdmin())
                    <hr class="my-3">
                    <h6 class="fw-bold small text-dark mb-2">
                        <i class="bi bi-cloud-arrow-up text-primary me-1"></i> Tải Lên Tệp Y Tế Mới:
                    </h6>

                    <form action="{{ route('records.attachments.upload', $record->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small text-muted mb-1">Loại tài liệu</label>
                            <select name="file_type" class="form-select form-select-sm" required>
                                <option value="lab_result">Kết quả xét nghiệm</option>
                                <option value="x_ray">Chụp X-quang / CT / Siêu âm</option>
                                <option value="prescription_scan">Bản chụp đơn thuốc / Phiếu cũ</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Chọn tệp (PDF, JPG, PNG &le; 3MB)</label>
                            <input type="file" name="file" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-upload me-1"></i> Đính kèm tệp bảo mật
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

