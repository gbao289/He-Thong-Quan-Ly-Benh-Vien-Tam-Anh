@extends('layouts.master')

@section('title', 'Thông Báo Bảo Trì Cổng Đặt Lịch - Bệnh Viện Tâm Anh')

@section('content')
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 p-4 p-md-5">
                <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-tools fs-1"></i>
                </div>
                <h3 class="fw-bold text-dark mb-2">HỆ THỐNG ĐẶT LỊCH ĐANG BẢO TRÌ ĐỊNH KỲ</h3>
                <p class="text-secondary mb-4">
                    {{ $message ?? 'Cổng đặt lịch khám trực tuyến đang được nâng cấp hạ tầng để phục vụ Quý khách tốt hơn. Chúng tôi sẽ sớm quay trở lại.' }}
                </p>

                <div class="p-3 bg-light rounded-3 border mb-4 text-start">
                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-telephone-fill me-2"></i>Kênh Hỗ Trợ Đặt Lịch Khẩn Cấp:</h6>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li class="mb-1"><strong>Tổng đài CSKH:</strong> <span class="fs-6 fw-bold text-danger">1800 6858</span> (Miễn phí cước)</li>
                        <li class="mb-1"><strong>Giờ làm việc:</strong> Thứ Hai – Chủ Nhật (07:00 – 19:00)</li>
                        <li><strong>Địa chỉ:</strong> 108 Hoàng Như Tiếp, P. Bồ Đề, Q. Long Biên, TP. Hà Nội</li>
                    </ul>
                </div>

                <div>
                    <a href="{{ url('/') }}" class="btn btn-outline-primary px-4">
                        <i class="bi bi-house me-1"></i>Quay lại Trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
