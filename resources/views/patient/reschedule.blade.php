@extends('layouts.master')

@section('title', 'Đổi Lịch Hẹn Khám - ' . $appointment->appointment_code)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar2-range me-2"></i>Đổi Lịch Hẹn Khám Bệnh Trực Tuyến</h5>
                </div>

                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger py-2 small mb-3">
                            @foreach($errors->all() as $err)
                                <div><i class="bi bi-exclamation-circle me-1"></i>{{ $err }}</div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Current Appointment Info Box -->
                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <span class="badge bg-secondary text-uppercase mb-2">Thông tin lịch hẹn hiện tại</span>
                        <div class="row g-2 small">
                            <div class="col-6"><strong>Mã lịch hẹn:</strong> <span class="font-monospace text-primary fw-bold">{{ $appointment->appointment_code }}</span></div>
                            <div class="col-6"><strong>Bệnh nhân:</strong> {{ $appointment->patientProfile->full_name }}</div>
                            <div class="col-6"><strong>Bác sĩ:</strong> {{ $appointment->doctor->user->name }}</div>
                            <div class="col-6"><strong>Chuyên khoa:</strong> {{ $appointment->specialty->name }}</div>
                            <div class="col-12 text-danger mt-1">
                                <strong>Lịch hiện tại:</strong> Ngày {{ $appointment->appointment_date->format('d/m/Y') }} (Ca: {{ $appointment->shift == 'morning' ? 'Sáng' : 'Chiều' }})
                            </div>
                        </div>
                    </div>

                    <!-- Reschedule Form -->
                    <form action="{{ route('patient.appointments.reschedule.process', $appointment) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn ngày khám mới: <span class="text-danger">*</span></label>
                            <input type="date" name="appointment_date" class="form-control form-control-lg" min="{{ date('Y-m-d') }}" value="{{ old('appointment_date') }}" required>
                            <small class="text-muted">Chỉ có thể chọn ngày trong tương lai khi bác sĩ có lịch công tác.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn ca khám mới: <span class="text-danger">*</span></label>
                            <select name="shift" class="form-select form-select-lg" required>
                                <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>Ca Sáng (07:30 - 11:30)</option>
                                <option value="afternoon" {{ old('shift') == 'afternoon' ? 'selected' : '' }}>Ca Chiều (13:30 - 17:00)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Lý do thay đổi lịch khám (tùy chọn):</label>
                            <textarea name="reschedule_reason" class="form-control" rows="3" placeholder="Ví dụ: Bận việc đột xuất, đổi kế hoạch công tác...">{{ old('reschedule_reason') }}</textarea>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('patient.history') }}" class="btn btn-outline-secondary px-3">Hủy bỏ</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4" onclick="return confirm('Bạn có chắc chắn muốn đổi sang ngày khám mới này không?');">
                                <i class="bi bi-check2-circle me-1"></i>Xác nhận đổi lịch
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
