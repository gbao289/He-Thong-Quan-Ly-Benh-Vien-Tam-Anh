@extends('layouts.master')

@section('title', 'Quản Lý Phiên Đăng Nhập & Thiết Bị - Bệnh Viện Tâm Anh')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-display me-2"></i>Quản Lý Phiên Thiết Bị & Đăng Xuất Từ Xa</h5>
                    <span class="badge bg-primary">Bảo mật tài khoản</span>
                </div>

                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-octagon-fill me-2"></i>
                            @foreach($errors->all() as $err)
                                <div>{{ $err }}</div>
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <h6 class="fw-bold mb-3 text-secondary">THIẾT BỊ ĐANG SỬ DỤNG (PHIÊN HIỆN TẠI):</h6>
                    <div class="p-3 border rounded bg-light d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-success-subtle text-success p-3">
                                <i class="bi bi-laptop fs-3"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <span>Thiết bị này</span>
                                    <span class="badge bg-success">Đang hoạt động</span>
                                </div>
                                <div class="small text-muted font-monospace mt-1">
                                    IP: {{ $currentSession['ip_address'] }} &bull; Thời gian: {{ $currentSession['last_active'] }}
                                </div>
                                <div class="small text-secondary text-truncate" style="max-width: 450px;" title="{{ $currentSession['user_agent'] }}">
                                    {{ $currentSession['user_agent'] }}
                                </div>
                            </div>
                        </div>
                        <i class="bi bi-shield-check text-success fs-3"></i>
                    </div>

                    <div class="border-top pt-4">
                        <h6 class="fw-bold text-danger mb-2"><i class="bi bi-box-arrow-right me-1"></i>Đăng xuất khỏi tất cả các thiết bị khác</h6>
                        <p class="small text-muted mb-3">
                            Nếu bạn nghi ngờ tài khoản của mình đã đăng nhập trên máy tính công cộng, thiết bị lạ hoặc ai đó biết mật khẩu, bạn có thể buộc đăng xuất tất cả các phiên làm việc khác từ xa ngay lập tức.
                        </p>

                        <form action="{{ route('sessions.logout-others') }}" method="POST" class="p-3 border border-danger-subtle rounded bg-danger-subtle bg-opacity-10">
                            @csrf
                            <label class="form-label fw-bold small text-dark">Nhập mật khẩu hiện tại của bạn để xác nhận hành động:</label>
                            <div class="row g-2">
                                <div class="col-md-7">
                                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu tài khoản" required>
                                </div>
                                <div class="col-md-5">
                                    <button type="submit" class="btn btn-danger w-100 fw-bold" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất khỏi tất cả các thiết bị khác không?');">
                                        <i class="bi bi-shield-x me-1"></i>Đăng xuất thiết bị khác
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
