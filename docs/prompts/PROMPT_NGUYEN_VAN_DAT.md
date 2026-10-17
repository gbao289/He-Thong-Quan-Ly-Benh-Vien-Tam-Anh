# HƯỚNG DẪN & PROMPT THỰC HIỆN NHIỆM VỤ: NGUYỄN VĂN ĐẠT
**Học phần:** Phát triển ứng dụng Web (PHP - Laravel - MySQL)  
**Đề tài:** Hệ Thống Quản Lý Bệnh Viện Tâm Anh  
**Thành viên thực hiện:** Nguyễn Văn Đạt  
**Vai trò:** Backend & Frontend Developer - Chuyên khoa & Bác sĩ  
**Nhánh làm việc Git:** `feature/doctors-and-specialties`

---

## PROMPT DÀNH CHO ANTIGRAVITY (SAO CHÉP TOÀN BỘ NỘI DUNG DƯỚI ĐÂY)

```markdown
Tôi là Nguyễn Văn Đạt (Backend & Frontend Developer: Chuyên khoa & Bác sĩ) trong đồ án "Hệ Thống Quản Lý Bệnh Viện Tâm Anh".

Kho lưu trữ GitHub của nhóm: https://github.com/quy0358/He-Thong-Quan-Ly-Benh-Vien-Tam-Anh.git
Tài liệu kế hoạch chi tiết: ke_hoach_trien_khai_benh_vien_tam_anh.md (Đặc biệt đọc kỹ Mục 7, Mục 8.2 và Mục 11).

Bạn hãy đóng vai trò là Senior Laravel Developer hỗ trợ tôi thực hiện toàn bộ các bước sau:

1. THIẾT LẬP MÔI TRƯỜNG DỰ ÁN CỤC BỘ:
   - Kiểm tra mã nguồn đã clone về máy, chuyển sang nhánh `develop` và cập nhật code mới nhất (`git pull origin develop`).
   - Tạo và chuyển sang nhánh làm việc cá nhân: `git checkout -b feature/doctors-and-specialties`.
   - Chạy `composer install`, cấu hình file `.env` kết nối MySQL (database: `db_benh_vien_tam_anh`), tạo APP_KEY (`php artisan key:generate`), chạy `php artisan storage:link`, và chạy `php artisan migrate --seed` để có sẵn dữ liệu mẫu.

2. HOÀN THÀNH TẤT CẢ NHIỆM VỤ CỦA NGUYỄN VĂN ĐẠT (MỤC 8.2):
   - Quản lý Chuyên khoa (Admin):
     + Lập trình hoàn chỉnh `app/Http/Controllers/Admin/SpecialtyController.php` (Resource CRUD): danh sách có tìm kiếm, thêm mới, sửa, bật/tắt trạng thái hoạt động `is_active`, tự động tạo `slug` chuẩn SEO từ tên chuyên khoa. Kiểm tra ràng buộc: không cho xóa chuyên khoa nếu đang có bác sĩ trực thuộc.
     + Tạo bộ giao diện đầy đủ tại `resources/views/admin/specialties/` (`index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`) kế thừa layout `layouts.admin`, sử dụng `<x-alert />`, `<x-page-header />` và Bootstrap 5.3.
     + Tạo Form Request `app/Http/Requests/StoreSpecialtyRequest.php` và `UpdateSpecialtyRequest.php`.
   - Quản lý Bác sĩ & Lịch làm việc (Admin):
     + Lập trình `app/Http/Controllers/Admin/DoctorManagementController.php`: hiển thị danh sách bác sĩ kèm chuyên khoa, tạo tài khoản bác sĩ mới (hoặc liên kết User), phân công phòng khám `room_number`, thiết lập học vị `qualification`, giá khám `consultation_fee`.
     + Cấu hình lịch trực tuần (`doctor_schedules`): giao diện trực quan cho phép thêm/sửa ca trực từ Thứ Hai đến Chủ Nhật (Sáng / Chiều / max_patients) cho từng bác sĩ.
     + Tạo bộ view tương ứng tại `resources/views/admin/doctors/` (`index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`, `schedule.blade.php`).
     + Tạo Form Request `StoreDoctorRequest.php`.
   - Giao diện Công chúng (Public Portal):
     + Lập trình `app/Http/Controllers/Public/DoctorPublicController.php` và các view:
       * `resources/views/public/doctors/index.blade.php`: danh bạ bác sĩ với bộ lọc tìm kiếm theo tên và chuyên khoa, hiển thị học vị, phòng khám, giá khám, nút "Đặt lịch khám".
       * `resources/views/public/doctors/show.blade.php`: trang chi tiết lý lịch chuyên môn, tiểu sử `bio`, chuyên khoa và lịch trực các ngày trong tuần của bác sĩ.
     + Cập nhật bổ sung vào `resources/views/public/home.blade.php` danh sách chuyên khoa tiêu biểu và bác sĩ nổi bật nếu cần hoàn thiện thêm.
   - API Nội bộ:
     + Đảm bảo route/API lấy danh sách bác sĩ theo chuyên khoa `booking.doctors_by_specialty` hoạt động đồng bộ với form đặt lịch của Quý.

3. TUÂN THỦ NGUYÊN TẮC PHÒNG TRÁNH CONFLICT (MỤC 11.2):
   - Đặt các route của mình trong đúng khu vực: `// ====== MODULE 2: SPECIALTIES & DOCTORS (Nguyễn Vãn Đạt) ======` trong file `routes/web.php`.
   - Tuyệt đối không sửa đổi file layout chung hoặc file code của thành viên khác.
   - Viết các Feature Test bổ sung trong `tests/Feature/DoctorSpecialtyTest.php` để kiểm tra CRUD Chuyên khoa & Bác sĩ.
   - Chạy `php artisan test` đảm bảo 100% test đều PASS.

4. BÀN GIAO VÀ PUSH GIT:
   - Commit toàn bộ thay đổi với thông điệp: `feat(specialties-doctors): [Nguyen Van Dat] hoan thanh CRUD chuyen khoa, bac si, lich truc va danh ba cong khai`.
   - Push nhánh `feature/doctors-and-specialties` lên GitHub và hướng dẫn tôi tạo Pull Request vào nhánh `develop`.
```
