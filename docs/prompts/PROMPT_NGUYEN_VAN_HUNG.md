# HƯỚNG DẪN & PROMPT THỰC HIỆN NHIỆM VỤ: NGUYỄN VĂN HƯNG
**Học phần:** Phát triển ứng dụng Web (PHP - Laravel - MySQL)  
**Đề tài:** Hệ Thống Quản Lý Bệnh Viện Tâm Anh  
**Thành viên thực hiện:** Nguyễn Văn Hưng  
**Vai trò:** Backend & Frontend Developer - Bệnh nhân & Tiếp nhận  
**Nhánh làm việc Git:** `feature/patients-and-reception`

---

## PROMPT DÀNH CHO ANTIGRAVITY (SAO CHÉP TOÀN BỘ NỘI DUNG DƯỚI ĐÂY)

```markdown
Tôi là Nguyễn Văn Hưng (Backend & Frontend Developer: Bệnh nhân & Tiếp nhận) trong đồ án "Hệ Thống Quản Lý Bệnh Viện Tâm Anh".

Kho lưu trữ GitHub của nhóm: https://github.com/quy0358/He-Thong-Quan-Ly-Benh-Vien-Tam-Anh.git
Tài liệu kế hoạch chi tiết: ke_hoach_trien_khai_benh_vien_tam_anh.md (Đặc biệt đọc kỹ Mục 7, Mục 8.3 và Mục 11).

Bạn hãy đóng vai trò là Senior Laravel Developer hỗ trợ tôi thực hiện toàn bộ các bước sau:

1. THIẾT LẬP MÔI TRƯỜNG DỰ ÁN CỤC BỘ:
   - Kiểm tra mã nguồn đã clone về máy, chuyển sang nhánh `develop` và cập nhật code mới nhất (`git pull origin develop`).
   - Tạo và chuyển sang nhánh làm việc cá nhân: `git checkout -b feature/patients-and-reception`.
   - Chạy `composer install`, cấu hình file `.env` kết nối MySQL (database: `db_benh_vien_tam_anh`), tạo APP_KEY (`php artisan key:generate`), chạy `php artisan storage:link`, và chạy `php artisan migrate --seed` để có sẵn dữ liệu mẫu.

2. HOÀN THÀNH TẤT CẢ NHIỆM VỤ CỦA NGUYỄN VĂN HƯNG (MỤC 8.3):
   - Quản lý Hồ sơ Bệnh nhân (Patient Profile Management):
     + Thuật toán sinh mã: Sử dụng và tối ưu hàm sinh mã hồ sơ bệnh nhân chuẩn hóa `BN-YYYYMMDD-XXXX` trong model `PatientProfile`.
     + Lập trình Controller `app/Http/Controllers/Receptionist/PatientController.php` (Resource CRUD):
       * `index`: Danh sách hồ sơ bệnh nhân có phân trang, bộ lọc tìm kiếm tức thì theo Họ tên, SĐT, Mã BN, Số CCCD, Thẻ BHYT.
       * `create` & `store`: Form tiếp nhận hồ sơ bệnh nhân mới tại quầy, có validation số điện thoại VN, ngày sinh, giới tính.
       * `edit` & `update`: Cập nhật thông tin hành chính, địa chỉ cư trú, bảo hiểm y tế.
       * `show`: Xem chi tiết hồ sơ bệnh nhân, hiển thị tiền sử các lần khám bệnh và đơn thuốc đã từng khám tại viện.
       * `destroy`: Ràng buộc an toàn - chặn xóa hồ sơ nếu bệnh nhân đã phát sinh lịch sử khám bệnh.
     + Tạo Form Request `StorePatientRequest.php` và `UpdatePatientRequest.php`.
     + Tạo bộ giao diện đầy đủ tại `resources/views/receptionist/patients/` (`index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`) kế thừa layout `layouts.admin`, sử dụng `<x-alert />`, `<x-page-header />`.
   - Nghiệp vụ Bàn tiếp nhận & Điều phối Lễ tân (Reception Desk):
     + Hoàn thiện và nâng cấp `app/Http/Controllers/Receptionist/ReceptionAppointmentController.php`:
       * Quản lý hàng đợi tiếp nhận trong ngày theo ca Sáng/Chiều.
       * Chức năng "Check-in": Xác nhận bệnh nhân đã có mặt tại viện, chuyển trạng thái lịch từ `pending` sang `confirmed` (Đã tiếp nhận - Chờ khám), sẵn sàng đẩy vào hàng đợi của Bác sĩ.
       * Chức năng "Tiếp nhận bệnh nhân vãng lai (Walk-in)": Bệnh nhân đến trực tiếp chưa đặt trước, chọn chuyên khoa/bác sĩ trực, kiểm tra ca khám còn slot thì tạo mới lịch khám và tự động check-in luôn.
       * Chức năng "Hủy lịch hẹn" có form popup ghi nhận lý do hủy (`cancellation_reason`).
       * Chức năng "Điều phối / Đổi ca / Đổi bác sĩ" trong trường hợp bác sĩ ban đầu có việc khẩn cấp.
     + Hoàn thiện bộ giao diện lễ tân tại `resources/views/receptionist/appointments/` và dashboard lễ tân nếu cần.

3. TUÂN THỦ NGUYÊN TẮC PHÒNG TRÁNH CONFLICT (MỤC 11.2):
   - Đặt các route của mình trong đúng khu vực: `// ====== MODULE 3: RECEPTION & PATIENTS (Nguyễn Văn Hưng) ======` trong file `routes/web.php`.
   - Phối hợp: Dùng Model `Appointment` của Quý và Model `Doctor` của Đạt mà không làm thay đổi cấu trúc cốt lõi.
   - Viết các Feature Test trong `tests/Feature/ReceptionPatientTest.php` để kiểm tra tra cứu bệnh nhân, check-in và chống xóa bệnh nhân có bệnh án.
   - Chạy `php artisan test` đảm bảo 100% test đều PASS.

4. BÀN GIAO VÀ PUSH GIT:
   - Commit toàn bộ thay đổi với thông điệp: `feat(reception-patients): [Nguyen Van Hung] hoan thanh quan ly ho so benh nhan, nghiep vu check-in va tiep don tai quay`.
   - Push nhánh `feature/patients-and-reception` lên GitHub và hướng dẫn tôi tạo Pull Request vào nhánh `develop`.
```
