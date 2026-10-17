# HƯỚNG DẪN & PROMPT THỰC HIỆN NHIỆM VỤ: VÕ LÊ GIA BẢO
**Học phần:** Phát triển ứng dụng Web (PHP - Laravel - MySQL)  
**Đề tài:** Hệ Thống Quản Lý Bệnh Viện Tâm Anh  
**Thành viên thực hiện:** Võ Lê Gia Bảo  
**Vai trò:** Backend & Frontend Developer - Khám bệnh, Hồ sơ y tế & Báo cáo  
**Nhánh làm việc Git:** `feature/examinations-and-reports`

---

## PROMPT DÀNH CHO ANTIGRAVITY (SAO CHÉP TOÀN BỘ NỘI DUNG DƯỚI ĐÂY)

```markdown
Tôi là Võ Lê Gia Bảo (Backend & Frontend Developer: Khám bệnh, Hồ sơ y tế & Báo cáo) trong đồ án "Hệ Thống Quản Lý Bệnh Viện Tâm Anh".

Kho lưu trữ GitHub của nhóm: https://github.com/quy0358/He-Thong-Quan-Ly-Benh-Vien-Tam-Anh.git
Tài liệu kế hoạch chi tiết: ke_hoach_trien_khai_benh_vien_tam_anh.md (Đặc biệt đọc kỹ Mục 7, Mục 8.4, Mục 12 và Mục 14.1).

Bạn hãy đóng vai trò là Senior Laravel Developer hỗ trợ tôi thực hiện toàn bộ các bước sau:

1. THIẾT LẬP MÔI TRƯỜNG DỰ ÁN CỤC BỘ:
   - Kiểm tra mã nguồn đã clone về máy, chuyển sang nhánh `develop` và cập nhật code mới nhất (`git pull origin develop`).
   - Tạo và chuyển sang nhánh làm việc cá nhân: `git checkout -b feature/examinations-and-reports`.
   - Chạy `composer install`, cấu hình file `.env` kết nối MySQL (database: `db_benh_vien_tam_anh`), tạo APP_KEY (`php artisan key:generate`), chạy `php artisan storage:link`, và chạy `php artisan migrate --seed` để có sẵn dữ liệu mẫu.

2. HOÀN THÀNH TẤT CẢ NHIỆM VỤ CỦA VÕ LÊ GIA BẢO (MỤC 8.4):
   - Bàn khám Bác sĩ (Doctor Consultation & Queue):
     + Nâng cấp màn hình `app/Http/Controllers/Doctor/DoctorQueueController.php` và view `resources/views/doctor/queue.blade.php`: hiển thị danh sách bệnh nhân đã Check-in (`confirmed`) hôm nay theo đúng `doctor_id` của tài khoản bác sĩ đang đăng nhập. Có nút bấm "Vào khám".
   - Lập Bệnh án & Kê đơn thuốc mẫu (Medical Records & Prescriptions):
     + Lập trình `app/Http/Controllers/Doctor/MedicalRecordController.php`:
       * `create`: Mở giao diện khám bệnh theo `appointment_id`. Hiển thị thông tin hành chính của bệnh nhân, tiền sử bệnh án các lần khám trước, lý do/triệu chứng ban đầu.
       * `store`: Form Request validation (`StoreMedicalRecordRequest.php`) ghi nhận: Triệu chứng thực thể `symptoms`, Chẩn đoán bệnh `diagnosis`, Ghi chú sinh tồn `clinical_notes`, Kế hoạch điều trị `treatment_plan`, Ngày hẹn tái khám `follow_up_date`.
       * Kê đơn thuốc động (Dynamic Prescription Repeater bằng Javascript): Thêm/xóa nhiều dòng thuốc gồm Tên thuốc `medication_name`, Liều lượng `dosage`, Tần suất `frequency`, Số ngày `duration_days`, Hướng dẫn `instructions`.
       * Thực thi trong `DB::transaction()`: tạo bản ghi trong `medical_records` (tự động sinh mã `MR-YYYYMMDD-XXXX`), insert danh sách `prescriptions`, và chuyển trạng thái lịch khám `appointments` sang `completed`.
     + Tạo giao diện bàn khám tại `resources/views/doctor/examine.blade.php` và `resources/views/doctor/records/show.blade.php`.
   - Upload & Download Tệp Y tế Bảo Mật (Secure File Storage & Policy):
     + Lập trình `app/Http/Controllers/Medical/AttachmentController.php`:
       * Upload: Kiểm tra chặt chẽ mime-type (`jpg, jpeg, png, pdf`), dung lượng tối đa 3MB (`max:3072`). Lưu file vào thư mục private `storage/app/private/medical_records/` (hash tên file an toàn bằng `Str::random(40)`), lưu metadata vào bảng `attachments`.
       * Download an toàn: Viết `app/Policies/AttachmentPolicy.php` hoặc kiểm tra quyền trong controller: Chỉ cho phép tải nếu `auth()->id()` là chính bệnh nhân sở hữu hồ sơ, hoặc là bác sĩ điều trị, hoặc là Admin. Nếu người lạ hoặc bệnh nhân khác truy cập link thì chặn ngay bằng HTTP 403 Forbidden.
   - Dashboard Quản trị & Báo cáo Thống kê:
     + Nâng cấp `app/Http/Controllers/Admin/AdminDashboardController.php` và `resources/views/admin/dashboard.blade.php`: bổ sung biểu đồ trạng thái ca khám hoặc thống kê phân bổ khám theo chuyên khoa bằng Chart.js (tùy chọn nâng cao) hoặc các thẻ widget thống kê trực quan.

3. TUÂN THỦ NGUYÊN TẮC PHÒNG TRÁNH CONFLICT (MỤC 11.2):
   - Đặt các route của mình trong đúng khu vực: `// ====== MODULE 4: EXAMINATION & RECORDS (Võ Lê Gia Bảo) ======` trong file `routes/web.php`.
   - Viết các Feature Test trong `tests/Feature/MedicalRecordTest.php` để kiểm tra lưu bệnh án, kê đơn và kiểm thử kịch bản bảo mật: Bệnh nhân B cố tình tải file của Bệnh nhân A bị trả về 403.
   - Chạy `php artisan test` đảm bảo toàn bộ test case (cũ và mới) đều PASS 100%.

4. BÀN GIAO VÀ PUSH GIT:
   - Commit toàn bộ thay đổi với thông điệp: `feat(examination-records): [Vo Le Gia Bao] hoan thanh ban kham bac si, ke don thuoc, upload file bao mat va dashboard`.
   - Push nhánh `feature/examinations-and-reports` lên GitHub và hướng dẫn tôi tạo Pull Request vào nhánh `develop`.
```
