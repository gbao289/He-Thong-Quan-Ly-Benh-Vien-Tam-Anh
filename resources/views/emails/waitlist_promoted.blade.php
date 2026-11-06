<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông Báo Thăng Hạng Lịch Khám Chính Thức</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
        <div style="background-color: #0d6efd; color: #ffffff; padding: 20px; text-align: center;">
            <h2 style="margin: 0; font-size: 20px;">BỆNH VIỆN ĐA KHOA TÂM ANH</h2>
            <p style="margin: 5px 0 0 0; font-size: 14px;">Chúc Mừng: Bạn Đã Có Lịch Khám Chính Thức!</p>
        </div>
        <div style="padding: 24px;">
            <p>Kính gửi Quý khách <strong>{{ $appointment->patientProfile->full_name }}</strong>,</p>
            <p>Hệ thống trân trọng thông báo: Do có bệnh nhân khác hủy hẹn, yêu cầu trong danh sách chờ của Quý khách đã được <strong>tự động thăng hạng thành lịch hẹn chính thức</strong>!</p>

            <div style="background-color: #e8f4fd; border-left: 4px solid #0d6efd; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0 0 8px 0;"><strong>Mã lịch hẹn chính thức:</strong> <span style="font-size: 18px; color: #0d6efd; font-weight: bold;">{{ $appointment->appointment_code }}</span></p>
                <p style="margin: 0 0 8px 0;"><strong>Bác sĩ:</strong> {{ $appointment->doctor->full_name_with_title }} (Phòng {{ $appointment->doctor->room_number }})</p>
                <p style="margin: 0 0 8px 0;"><strong>Chuyên khoa:</strong> {{ $appointment->specialty->name }}</p>
                <p style="margin: 0 0 8px 0;"><strong>Ngày khám:</strong> {{ $appointment->appointment_date->format('d/m/Y') }} ({{ $appointment->shift_name }})</p>
                <p style="margin: 0;"><strong>Trạng thái:</strong> <span style="color: #198754; font-weight: bold;">Đã tiếp nhận vào danh sách khám</span></p>
            </div>

            <p>Quý khách vui lòng có mặt tại Quầy Lễ Tân trước giờ khám 15 phút và xuất trình mã hẹn <strong>{{ $appointment->appointment_code }}</strong> để check-in nhận số thứ tự vào phòng khám.</p>
        </div>
        <div style="background-color: #f8fafc; padding: 15px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;">
            Hotline Hỗ Trợ 24/7: 1800 6858 | Email: cskh@tamanh.vn<br>
            Hệ thống Quản lý Bệnh viện Đa khoa Tâm Anh
        </div>
    </div>
</body>
</html>
