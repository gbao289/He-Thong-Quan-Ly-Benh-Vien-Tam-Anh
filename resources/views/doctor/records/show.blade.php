@extends('layouts.admin')

@section('title', 'Chi Tiết Bệnh Án - ' . $record->record_code . ' - BV Tâm Anh')

@push('styles')
    <style>
        @media print {
            @page {
                margin: 15mm;
            }

            body {
                display: block !important;
                background: #fff !important;
            }

            .sidebar,
            .top-navbar,
            footer,
            .no-print,
            .main-content > main > .alert {
                display: none !important;
            }

            .main-content,
            .main-content > main {
                display: block !important;
                width: 100% !important;
                min-height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .main-content > main > .row {
                display: block !important;
                margin: 0 !important;
            }

            .printable-column {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
                padding: 0 !important;
            }

            .printable-card {
                width: 100% !important;
                margin: 0 !important;
                border: 0 !important;
                box-shadow: none !important;
            }

            .printable-card tr {
                break-inside: avoid;
            }
        }
    </style>
@endpush

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 no-print">
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
    <div class="col-lg-8 printable-column">
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
    <div class="col-lg-4 no-print">
        <!-- Thông tin ca khám -->
        <div class="card shadow-sm border-0 mb-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold text-primary mb-0">
                    <i class="bi bi-person-bounding-box me-1"></i> Thông Tin Bệnh Nhân
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="text-center pb-3 border-bottom mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center fs-2 mb-2" style="width: 58px; height: 58px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h5 class="fw-bold text-dark text-uppercase mb-0">{{ $record->patientProfile->full_name }}</h5>
                    <div class="text-muted small">
                        Mã BN: <strong class="font-monospace text-primary">{{ $record->patientProfile->patient_code }}</strong>
                    </div>
                </div>

                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Tuổi / Giới tính:</span>
                        <strong class="text-dark">{{ $record->patientProfile->age_text }} &bull; {{ $record->patientProfile->gender_name }}</strong>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Số điện thoại:</span>
                        <span class="font-monospace">{{ $record->patientProfile->phone }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Mã lịch hẹn:</span>
                        <span class="font-monospace fw-bold text-primary">{{ $record->appointment->appointment_code }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Bác sĩ khám:</span>
                        <strong class="text-dark">{{ $record->doctor ? $record->doctor->full_name_with_title : '--' }}</strong>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Thời gian khám:</span>
                        <span>{{ $record->created_at->format('d/m/Y H:i') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        @if($record->follow_up_date)
            <div class="alert alert-warning border shadow-sm p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-calendar-event fs-5 text-warning-emphasis"></i>
                    <strong class="text-warning-emphasis">Hẹn ngày tái khám:</strong>
                </div>
                <div class="fs-5 fw-bold text-dark">{{ $record->follow_up_date->format('d/m/Y') }}</div>
                <div class="small text-muted">Bệnh nhân đã được nhắc lịch trên cổng tra cứu y tế.</div>
            </div>
        @endif
    </div>

    <!-- Right Column: Medical Record Details & Prescriptions -->
    <div class="col-lg-8">
        <!-- Diagnosis Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-primary mb-0">
                    <i class="bi bi-clipboard2-pulse me-1"></i> Kết Luận Lâm Sàng & Phác Đồ Điều Trị
                </h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">
                    {{ $record->record_code }}
                </span>
            </div>
            <div class="card-body p-4">
                <div class="p-3 bg-light rounded border mb-3">
                    <div class="small text-muted fw-bold text-uppercase mb-1">Chẩn đoán kết luận của Bác sĩ:</div>
                    <div class="fs-5 fw-bold text-primary">{{ $record->diagnosis }}</div>
                </div>

                <div class="row g-3 mb-3 small">
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Triệu chứng lâm sàng ghi nhận:</strong>
                        <p class="text-dark bg-white p-2 rounded border">{{ $record->symptoms }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Ghi chú lâm sàng / Cận lâm sàng:</strong>
                        <p class="text-dark bg-white p-2 rounded border">{{ $record->clinical_notes ?: 'Không có ghi chú thêm.' }}</p>
                    </div>
                    @if($record->treatment_plan)
                        <div class="col-12">
                            <strong class="text-muted d-block mb-1">Kế hoạch điều trị & Dặn dò y khoa:</strong>
                            <p class="text-dark bg-white p-2 rounded border">{{ $record->treatment_plan }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Prescriptions Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-success mb-0">
                    <i class="bi bi-capsule text-success me-1"></i> Đơn Thuốc Điều Trị Ngoại Trú ({{ $record->prescriptions->count() }} loại thuốc)
                </h6>
                <a href="{{ route('doctor.records.print', $record) }}" target="_blank" class="btn btn-sm btn-outline-dark d-inline-flex align-items-center gap-1">
                    <i class="bi bi-printer"></i> In đơn thuốc
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th class="ps-3" style="width: 45px;">#</th>
                                <th>Tên thuốc</th>
                                <th>Liều lượng</th>
                                <th>Tần suất</th>
                                <th>Số ngày</th>
                                <th>Hướng dẫn / Lời dặn</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($record->prescriptions as $idx => $p)
                                <tr>
                                    <td class="ps-3 text-muted fw-bold">{{ $idx + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ $p->medication_name }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $p->dosage }}</span></td>
                                    <td>{{ $p->frequency }}</td>
                                    <td>{{ $p->duration_days }} ngày</td>
                                    <td class="text-secondary">{{ $p->instructions ?: 'Uống theo chỉ dẫn' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Không kê đơn thuốc ngoại trú cho ca khám này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Medical Attachments Card (X-Quang, MRI, Siêu Âm, Kết Quả Xét Nghiệm) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-info-emphasis mb-0">
                        <i class="bi bi-images me-1"></i> Hồ Sơ Cận Lâm Sàng & Tệp Đính Kèm ({{ $record->attachments->count() }} tệp)
                    </h6>
                    <small class="text-muted">Phim X-Quang, CT, MRI, Siêu âm và Phiếu xét nghiệm y khoa</small>
                </div>
                <button class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="collapse" data-bs-target="#uploadMoreCollapse">
                    <i class="bi bi-cloud-arrow-up"></i> Tải thêm tệp cận lâm sàng
                </button>
            </div>

            <!-- Upload More Collapse Form -->
            <div class="collapse border-bottom bg-light p-3" id="uploadMoreCollapse">
                <form action="{{ route('doctor.records.attachments.upload', $record) }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Chọn tệp bổ sung (JPG, PNG, WEBP, PDF tối đa 10MB):</label>
                        <input type="file" name="attachment" class="form-control form-control-sm" required accept="image/jpeg,image/png,image/webp,application/pdf">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Phân loại tài liệu:</label>
                        <select name="file_type" class="form-select form-select-sm">
                            <option value="x_ray">Phim X-Quang / CT / MRI / Siêu âm</option>
                            <option value="lab_result">Kết quả Xét nghiệm Máu / Sinh hóa</option>
                            <option value="prescription_scan">Scan đơn thuốc / Tài liệu cũ</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1">
                            <i class="bi bi-upload"></i> Lưu tệp mới
                        </button>
                    </div>
                </form>
            </div>

            <div class="card-body p-4">
                @if($record->attachments->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-folder2-open fs-2 d-block text-secondary opacity-50 mb-1"></i>
                        <span>Chưa có tệp phim X-quang hoặc kết quả xét nghiệm nào được đính kèm.</span>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($record->attachments as $att)
                            <div class="col-sm-6 col-md-4">
                                <div class="card h-100 border shadow-none bg-white">
                                    <div class="position-relative text-center bg-light rounded-top p-2" style="height: 160px; overflow: hidden;">
                                        @if($att->is_image)
                                            <img src="{{ $att->url }}" alt="{{ $att->original_name }}" class="w-100 h-100 object-fit-cover rounded cursor-pointer" onclick="openLightbox('{{ $att->url }}', '{{ addslashes($att->original_name) }}')" style="cursor: zoom-in;" title="Bấm để xem ảnh lớn">
                                            <span class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75">
                                                <i class="bi bi-zoom-in"></i> Phóng to
                                            </span>
                                        @else
                                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-danger">
                                                <i class="bi bi-file-earmark-pdf-fill fs-1"></i>
                                                <span class="small fw-semibold mt-1">Tài liệu PDF</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                @if($att->file_type === 'x_ray')
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">X-Quang / CĐHA</span>
                                                @elseif($att->file_type === 'lab_result')
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle small">Xét nghiệm</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border small">Scan / Tài liệu</span>
                                                @endif
                                                <small class="text-muted font-monospace">{{ $att->formatted_size }}</small>
                                            </div>
                                            <div class="small fw-bold text-dark text-truncate" title="{{ $att->original_name }}">
                                                {{ $att->original_name }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                Tải lên: {{ $att->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>

                                        <div class="pt-2 mt-2 border-top d-flex justify-content-between align-items-center">
                                            <a href="{{ route('attachments.download', $att) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" title="Tải xuống tệp an toàn">
                                                <i class="bi bi-download"></i> Tải về
                                            </a>
                                            <form action="{{ route('doctor.records.attachments.destroy', [$record, $att]) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tệp cận lâm sàng này khỏi bệnh án?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Xóa tệp">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
