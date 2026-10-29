@extends('layouts.admin')

@section('title', 'Bàn Khám Bệnh - Bác Sĩ')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('doctor.queue') }}" class="text-decoration-none">Hàng đợi khám</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bàn khám bệnh</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">
            <i class="bi bi-stethoscope text-primary me-2"></i>Bàn Khám Bệnh & Lập Bệnh Án
        </h3>
    </div>
    <div>
        <a href="{{ route('doctor.queue') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại hàng đợi
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Cột trái: Thông tin Bệnh nhân & Tiền sử khám -->
    <div class="col-lg-4">
        <!-- Thông tin hành chính bệnh nhân -->
        <div class="card shadow-sm border-0 mb-4 bg-white sticky-top" style="top: 20px; z-index: 10;">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-person-vcard fs-5"></i> Thông Tin Hành Chính Bệnh Nhân
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fs-2" style="width: 54px; height: 54px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $appointment->patientProfile->full_name }}</h5>
                        <span class="badge bg-primary font-monospace">{{ $appointment->patientProfile->patient_code }}</span>
                    </div>
                </div>

                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Giới tính:</span>
                        <strong class="text-dark">{{ $appointment->patientProfile->gender_name }}</strong>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Ngày sinh / Tuổi:</span>
                        <strong class="text-dark">
                            {{ $appointment->patientProfile->dob ? $appointment->patientProfile->dob->format('d/m/Y') . ' (' . $appointment->patientProfile->dob->age . ' tuổi)' : '—' }}
                        </strong>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Số điện thoại:</span>
                        <strong class="text-dark">{{ $appointment->patientProfile->phone }}</strong>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Số CCCD / CMT:</span>
                        <span class="text-dark">{{ $appointment->patientProfile->identity_card ?? 'Chưa cập nhật' }}</span>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Mã BHYT:</span>
                        <span class="text-dark">{{ $appointment->patientProfile->insurance_code ?? 'Khám dịch vụ' }}</span>
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Địa chỉ:</span>
                        <span class="text-dark text-end" style="max-width: 60%;">{{ $appointment->patientProfile->address ?? '—' }}</span>
                    </li>
                </ul>

                <div class="p-3 bg-light rounded-3 border">
                    <div class="small fw-bold text-primary mb-1">
                        <i class="bi bi-calendar-check me-1"></i> Thông tin ca hẹn hiện tại:
                    </div>
                    <div class="small text-muted mb-1">
                        Mã hẹn: <strong class="text-dark font-monospace">{{ $appointment->appointment_code }}</strong>
                    </div>
                    <div class="small text-muted mb-1">
                        Ca trực: <strong>{{ $appointment->shift_name }}</strong> &bull; Ngày: {{ $appointment->appointment_date->format('d/m/Y') }}
                    </div>
                    <div class="small text-muted">
                        Chuyên khoa: <strong>{{ $appointment->specialty->name }}</strong>
                    </div>
                    @if($appointment->symptom_description)
                        <hr class="my-2">
                        <div class="small text-danger fw-semibold">
                            <i class="bi bi-exclamation-circle me-1"></i> Triệu chứng bệnh nhân khai báo:
                        </div>
                        <div class="small text-dark fst-italic mt-1 bg-white p-2 rounded border">
                            "{{ $appointment->symptom_description }}"
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tiền sử khám bệnh của bệnh nhân -->
        <div class="card shadow-sm border-0 mb-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history text-secondary me-2"></i>Tiền Sử Bệnh Án Trước Đây
                </h6>
                <span class="badge bg-secondary">{{ $pastRecords->count() }} lần</span>
            </div>
            <div class="card-body p-3">
                @forelse($pastRecords as $idx => $record)
                    <div class="border rounded-3 p-3 mb-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="font-monospace text-primary small">{{ $record->record_code }}</strong>
                            <small class="text-muted">{{ $record->created_at->format('d/m/Y') }}</small>
                        </div>
                        <div class="small mb-1">
                            <span class="text-muted">Chẩn đoán:</span>
                            <strong class="text-danger">{{ $record->diagnosis }}</strong>
                        </div>
                        <div class="small mb-2 text-muted">
                            Bác sĩ: {{ $record->doctor->full_name_with_title ?? 'Bác sĩ viện' }}
                        </div>
                        @if($record->prescriptions->isNotEmpty())
                            <div class="small text-muted">
                                <strong>Đơn thuốc ({{ $record->prescriptions->count() }} loại):</strong>
                                <ul class="mb-0 ps-3">
                                    @foreach($record->prescriptions->take(3) as $rx)
                                        <li>{{ $rx->medication_name }} ({{ $rx->dosage }}) - {{ $rx->frequency }}</li>
                                    @endforeach
                                    @if($record->prescriptions->count() > 3)
                                        <li class="fst-italic text-secondary">và {{ $record->prescriptions->count() - 3 }} loại khác...</li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                        <div class="mt-2 text-end">
                            <a href="{{ route('doctor.records.show', $record->id) }}" target="_blank" class="small text-decoration-none">
                                Xem chi tiết lần khám này <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-folder2-open fs-2 d-block mb-1 text-secondary opacity-50"></i>
                        Bệnh nhân chưa có lịch sử khám trước đây tại hệ thống.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Cột phải: Form Bàn khám & Đơn thuốc -->
    <div class="col-lg-8">
        <form action="{{ route('doctor.records.store') }}" method="POST" id="examineForm">
            @csrf
            <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

            <!-- Khối 1: Khám lâm sàng & Chẩn đoán -->
            <div class="card shadow-sm border-0 mb-4 bg-white">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">1</span>
                    <h5 class="fw-bold mb-0 text-dark">Khám Lâm Sàng & Kết Luận Chẩn Đoán</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">
                            Triệu chứng thực thể khi thăm khám <span class="text-danger">*</span>
                        </label>
                        <textarea name="symptoms" class="form-control @error('symptoms') is-invalid @enderror" rows="3" placeholder="Ghi nhận chi tiết: ho sốt, đau ngực, kết quả nghe tim phổi, thể trạng, v.v..." required>{{ old('symptoms', $appointment->symptom_description) }}</textarea>
                        @error('symptoms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">
                            Chẩn đoán bệnh xác định <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="diagnosis" class="form-control form-control-lg @error('diagnosis') is-invalid @enderror" placeholder="Ví dụ: Viêm họng cấp / Tăng huyết áp độ II / Viêm dạ dày..." value="{{ old('diagnosis') }}" required>
                        @error('diagnosis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">
                                Ghi chú sinh tồn / Cận lâm sàng (Tùy chọn)
                            </label>
                            <textarea name="clinical_notes" class="form-control @error('clinical_notes') is-invalid @enderror" rows="3" placeholder="Chỉ số: HA: 120/80 mmHg, Mạch: 75 l/p, SpO2: 99%, Thân nhiệt: 37°C...">{{ old('clinical_notes') }}</textarea>
                            @error('clinical_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">
                                Kế hoạch điều trị & Dặn dò chế độ sinh hoạt
                            </label>
                            <textarea name="treatment_plan" class="form-control @error('treatment_plan') is-invalid @enderror" rows="3" placeholder="Nghỉ ngơi, uống nhiều nước ấm, hạn chế đồ cay nóng, tập thể dục nhẹ...">{{ old('treatment_plan') }}</textarea>
                            @error('treatment_plan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">
                            Hẹn ngày tái khám (Nếu cần)
                        </label>
                        <input type="date" name="follow_up_date" class="form-control @error('follow_up_date') is-invalid @enderror" min="{{ date('Y-m-d') }}" value="{{ old('follow_up_date') }}">
                        <div class="form-text">Để trống nếu không chỉ định lịch hẹn tái khám.</div>
                        @error('follow_up_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Khối 2: Kê đơn thuốc ngoại trú (Dynamic Form Repeater) -->
            <div class="card shadow-sm border-0 mb-4 bg-white">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">2</span>
                        <h5 class="fw-bold mb-0 text-dark">Đơn Thuốc Điều Trị Ngoại Trú</h5>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addPrescriptionBtn">
                        <i class="bi bi-plus-circle me-1"></i> Thêm dòng thuốc
                    </button>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1 text-primary"></i>
                        Bác sĩ nhập danh sách thuốc điều trị. Có thể bấm <strong>"Thêm dòng thuốc"</strong> để kê nhiều loại thuốc trong một đơn.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="prescriptionTable">
                            <thead class="table-light text-center small">
                                <tr>
                                    <th style="width: 30%;">Tên thuốc & hàm lượng <span class="text-danger">*</span></th>
                                    <th style="width: 18%;">Liều lượng <span class="text-danger">*</span></th>
                                    <th style="width: 20%;">Tần suất dùng <span class="text-danger">*</span></th>
                                    <th style="width: 12%;">Số ngày <span class="text-danger">*</span></th>
                                    <th style="width: 15%;">Cách dùng</th>
                                    <th style="width: 50px;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="prescriptionTbody">
                                @php
                                    $oldPrescriptions = old('prescriptions', [
                                        ['medication_name' => '', 'dosage' => '', 'frequency' => '', 'duration_days' => 5, 'instructions' => '']
                                    ]);
                                @endphp

                                @foreach($oldPrescriptions as $index => $rx)
                                    <tr class="prescription-row">
                                        <td>
                                            <input type="text" name="prescriptions[{{ $index }}][medication_name]" class="form-control form-control-sm" placeholder="VD: Paracetamol 500mg" value="{{ $rx['medication_name'] ?? '' }}">
                                        </td>
                                        <td>
                                            <input type="text" name="prescriptions[{{ $index }}][dosage]" class="form-control form-control-sm" placeholder="VD: 1 viên" value="{{ $rx['dosage'] ?? '' }}">
                                        </td>
                                        <td>
                                            <input type="text" name="prescriptions[{{ $index }}][frequency]" class="form-control form-control-sm" placeholder="VD: 2 lần/ngày sau ăn" value="{{ $rx['frequency'] ?? '' }}">
                                        </td>
                                        <td>
                                            <input type="number" name="prescriptions[{{ $index }}][duration_days]" class="form-control form-control-sm text-center" min="1" max="365" placeholder="5" value="{{ $rx['duration_days'] ?? 5 }}">
                                        </td>
                                        <td>
                                            <input type="text" name="prescriptions[{{ $index }}][instructions]" class="form-control form-control-sm" placeholder="VD: Uống lúc no" value="{{ $rx['instructions'] ?? '' }}">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-rx-btn" title="Xóa dòng thuốc">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2 text-end">
                        <button type="button" class="btn btn-sm btn-light border text-primary" id="addPrescriptionBtnSecondary">
                            <i class="bi bi-plus-lg me-1"></i> Thêm thuốc khác
                        </button>
                    </div>
                </div>
            </div>

            <!-- Khối 3: Nút Lưu và Xác nhận -->
            <div class="card shadow-sm border-0 mb-4 bg-white">
                <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        Hệ thống sẽ thực hiện giao dịch tự động <strong>DB::transaction</strong> để lưu hồ sơ bệnh án, kê đơn thuốc và chuyển trạng thái ca khám sang <strong>Đã khám hoàn tất (Completed)</strong>.
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('doctor.queue') }}" class="btn btn-outline-secondary">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" id="submitExamineBtn">
                            <i class="bi bi-check2-circle me-1"></i> Lưu Bệnh Án & Hoàn Tất Khám
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('prescriptionTbody');
    const addBtn = document.getElementById('addPrescriptionBtn');
    const addBtnSecondary = document.getElementById('addPrescriptionBtnSecondary');
    const form = document.getElementById('examineForm');

    let rowIndex = {{ count($oldPrescriptions) }};

    function addRow() {
        const tr = document.createElement('tr');
        tr.className = 'prescription-row';
        tr.innerHTML = `
            <td>
                <input type="text" name="prescriptions[${rowIndex}][medication_name]" class="form-control form-control-sm" placeholder="VD: Amoxicillin 500mg" required>
            </td>
            <td>
                <input type="text" name="prescriptions[${rowIndex}][dosage]" class="form-control form-control-sm" placeholder="VD: 1 viên/lần" required>
            </td>
            <td>
                <input type="text" name="prescriptions[${rowIndex}][frequency]" class="form-control form-control-sm" placeholder="VD: 2 lần/ngày sáng-chiều" required>
            </td>
            <td>
                <input type="number" name="prescriptions[${rowIndex}][duration_days]" class="form-control form-control-sm text-center" min="1" max="365" value="5" required>
            </td>
            <td>
                <input type="text" name="prescriptions[${rowIndex}][instructions]" class="form-control form-control-sm" placeholder="VD: Uống sau ăn">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-rx-btn" title="Xóa dòng thuốc">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
    }

    if (addBtn) addBtn.addEventListener('click', addRow);
    if (addBtnSecondary) addBtnSecondary.addEventListener('click', addRow);

    // Xóa dòng thuốc khi bấm nút thùng rác
    tbody.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-rx-btn');
        if (btn) {
            const rows = tbody.querySelectorAll('.prescription-row');
            if (rows.length > 1) {
                btn.closest('tr').remove();
            } else {
                // Nếu chỉ còn 1 dòng, clear nội dung thay vì xóa
                const inputs = btn.closest('tr').querySelectorAll('input');
                inputs.forEach(input => input.value = '');
            }
        }
    });

    // Xác nhận trước khi hoàn tất lưu bệnh án
    if (form) {
        form.addEventListener('submit', function (e) {
            const submitBtn = document.getElementById('submitExamineBtn');
            if (!confirm('Bác sĩ có chắc chắn muốn lưu hồ sơ bệnh án và hoàn tất ca khám cho bệnh nhân này?')) {
                e.preventDefault();
                return false;
            }
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Đang lưu bệnh án...';
            }
        });
    }
});
</script>
@endpush

