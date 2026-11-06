@extends('layouts.master')

@section('title', 'Bảo Mật 2 Lớp (2FA) - Bệnh Viện Tâm Anh')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock-fill me-2"></i>Xác Thực 2 Bước (Two-Factor Authentication - 2FA)</h5>
                    @if($isEnabled)
                        <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i>ĐANG BẬT BẢO VỆ</span>
                    @else
                        <span class="badge bg-warning text-dark px-3 py-2"><i class="bi bi-exclamation-triangle-fill me-1"></i>CHƯA KÍCH HOẠT</span>
                    @endif
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

                    @if(!$isEnabled)
                        <div class="alert alert-info border-start border-4 border-info">
                            <h6 class="fw-bold mb-1"><i class="bi bi-info-circle-fill me-1"></i>Hướng dẫn kích hoạt bảo mật 2 lớp:</h6>
                            <p class="mb-0 small text-muted">
                                Xác thực 2 bước tăng cường lớp bảo vệ tối đa cho tài khoản của bạn. Sau khi kích hoạt, mỗi lần đăng nhập bạn cần nhập thêm mã OTP 6 chữ số từ ứng dụng như <strong>Google Authenticator</strong> hoặc <strong>Microsoft Authenticator</strong>.
                            </p>
                        </div>

                        <div class="row my-4 align-items-center">
                            <div class="col-md-5 text-center mb-3 mb-md-0">
                                <div class="p-3 bg-white border rounded shadow-sm d-inline-block">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qrUrl) }}" alt="TOTP QR Code" class="img-fluid" style="width: 180px; height: 180px;">
                                </div>
                                <div class="mt-2 text-muted small">Quét mã bằng Google Authenticator</div>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-bold small text-muted">HOẶC NHẬP MÃ THỦ CÔNG (SECRET KEY):</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control font-monospace fw-bold bg-light" value="{{ $secret }}" readonly id="secretKeyInput">
                                    <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText('{{ $secret }}'); alert('Đã sao chép Secret Key!');">
                                        <i class="bi bi-clipboard"></i> Sao chép
                                    </button>
                                </div>

                                <form action="{{ route('two-factor.confirm') }}" method="POST">
                                    @csrf
                                    <label class="form-label fw-bold">Nhập mã xác nhận 6 chữ số từ ứng dụng:</label>
                                    <div class="d-flex gap-2">
                                        <input type="text" name="code" class="form-control form-control-lg text-center font-monospace tracking-widest fw-bold" placeholder="000000" maxlength="6" required pattern="[0-9]{6}" autocomplete="off">
                                        <button type="submit" class="btn btn-success px-4 fw-bold">
                                            <i class="bi bi-check2-circle me-1"></i>Kích hoạt 2FA
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1">Mã xác thực thay đổi sau mỗi 30 giây.</small>
                                </form>
                            </div>
                        </div>

                        @if(!empty($recoveryCodes))
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-bold text-danger mb-2"><i class="bi bi-key-fill me-1"></i>Mã khôi phục dự phòng (Recovery Codes):</h6>
                                <p class="small text-muted mb-2">Lưu lại các mã này ở nơi an toàn. Mỗi mã chỉ dùng được 01 lần khi bạn bị mất quyền truy cập thiết bị xác thực:</p>
                                <div class="row g-2">
                                    @foreach($recoveryCodes as $rc)
                                        <div class="col-6 col-md-3">
                                            <div class="p-2 bg-light border rounded text-center font-monospace small fw-bold text-secondary">
                                                {{ $rc }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    @else
                        <div class="p-4 bg-light rounded-3 text-center mb-4">
                            <i class="bi bi-shield-check text-success display-4 mb-2"></i>
                            <h5 class="fw-bold text-success mb-1">Tài khoản của bạn đang được bảo vệ an toàn với 2FA</h5>
                            <p class="text-muted small mb-0">Thời điểm xác nhận kích hoạt: {{ $user->two_factor_confirmed_at?->format('H:i d/m/Y') }}</p>
                        </div>

                        @if(!empty($recoveryCodes))
                            <div class="mb-4">
                                <h6 class="fw-bold mb-2"><i class="bi bi-key-fill text-primary me-1"></i>Mã khôi phục khẩn cấp còn lại:</h6>
                                <div class="row g-2">
                                    @foreach($recoveryCodes as $rc)
                                        <div class="col-6 col-md-3">
                                            <div class="p-2 bg-light border rounded text-center font-monospace small fw-bold text-secondary">
                                                {{ $rc }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="border-top pt-4">
                            <h6 class="fw-bold text-danger mb-2">Hủy kích hoạt xác thực 2 bước</h6>
                            <p class="small text-muted">Nếu bạn tắt 2FA, tài khoản của bạn sẽ chỉ được bảo vệ bởi mật khẩu đơn lẻ.</p>
                            <form action="{{ route('two-factor.disable') }}" method="POST" class="row g-2 align-items-center" onsubmit="return confirm('Bạn có chắc chắn muốn hủy kích hoạt 2FA không?');">
                                @csrf
                                @method('DELETE')
                                <div class="col-md-6">
                                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu hiện tại để xác nhận" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-x-circle me-1"></i>Hủy kích hoạt 2FA
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
