@extends('layouts.master')

@section('title', 'Xác Thực 2 Bước (2FA) - Bệnh Viện Tâm Anh')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <div class="rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center mb-2 shadow" style="width: 54px; height: 54px;">
                        <i class="bi bi-shield-lock-fill fs-3"></i>
                    </div>
                    <h4 class="fw-bold mb-1">XÁC THỰC BẢO MẬT 2 LỚP</h4>
                    <p class="mb-0 small text-white-50">Nhập mã OTP từ ứng dụng hoặc mã khôi phục</p>
                </div>

                <div class="card-body p-4">
                    @if(session('info'))
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-info-circle me-1"></i>{{ session('info') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger py-2 small mb-3">
                            @foreach($errors->all() as $error)
                                <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('two-factor.verify') }}" method="POST" id="totpForm">
                        @csrf
                        <div id="totpGroup">
                            <label class="form-label fw-bold text-dark">Mã xác thực TOTP (6 chữ số):</label>
                            <input type="text" name="code" class="form-control form-control-lg text-center font-monospace tracking-widest fw-bold mb-3" placeholder="------" maxlength="6" autofocus pattern="[0-9]{6}" autocomplete="one-time-code">
                            <p class="small text-muted mb-3 text-center">Mở ứng dụng Google/Microsoft Authenticator để lấy mã 6 chữ số.</p>
                        </div>

                        <div id="recoveryGroup" style="display: none;">
                            <label class="form-label fw-bold text-dark">Mã khôi phục khẩn cấp:</label>
                            <input type="text" name="recovery_code" class="form-control font-monospace mb-3" placeholder="xxxx-xxxx">
                            <p class="small text-muted mb-3 text-center">Sử dụng một trong các mã khôi phục dự phòng đã lưu.</p>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-3">
                            <i class="bi bi-check2-circle me-1"></i>Xác nhận & Đăng nhập
                        </button>

                        <div class="text-center">
                            <button type="button" class="btn btn-link btn-sm text-decoration-none" id="toggleRecoveryBtn" onclick="toggleRecovery();">
                                Sử dụng mã khôi phục dự phòng
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer bg-light text-center py-3">
                    <a href="{{ route('login') }}" class="small text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Quay lại trang Đăng nhập
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let isRecovery = false;
    function toggleRecovery() {
        isRecovery = !isRecovery;
        const totpGroup = document.getElementById('totpGroup');
        const recoveryGroup = document.getElementById('recoveryGroup');
        const btn = document.getElementById('toggleRecoveryBtn');

        if (isRecovery) {
            totpGroup.style.display = 'none';
            recoveryGroup.style.display = 'block';
            btn.innerText = 'Sử dụng mã ứng dụng xác thực (TOTP)';
        } else {
            totpGroup.style.display = 'block';
            recoveryGroup.style.display = 'none';
            btn.innerText = 'Sử dụng mã khôi phục dự phòng';
        }
    }
</script>
@endsection
