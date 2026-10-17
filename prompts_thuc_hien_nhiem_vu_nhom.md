# HƯỚNG DẪN PROMPTS ANTIGRAVITY CHO CÁC THÀNH VIÊN NHÓM
## Đồ án: Hệ Thống Quản Lý Bệnh Viện Tâm Anh (PHP - Laravel - MySQL)

> **Kho lưu trữ chính thức (GitHub):** [https://github.com/quy0358/He-Thong-Quan-Ly-Benh-Vien-Tam-Anh](https://github.com/quy0358/He-Thong-Quan-Ly-Benh-Vien-Tam-Anh)  
> **Kế hoạch triển khai chi tiết:** [ke_hoach_trien_khai_benh_vien_tam_anh.md](./ke_hoach_trien_khai_benh_vien_tam_anh.md)  
> **Người biên soạn:** Nguyễn Thành Quý (Technical Lead & System Architect)

---

## 📌 HƯỚNG DẪN SỬ DỤNG DÀNH CHO CÁC BẠN THÀNH VIÊN

1. **Chuẩn bị môi trường máy cá nhân:**
   - Đã cài đặt XAMPP (chạy Apache và MySQL) với PHP 8.2+ và Composer.
   - Bật các extension PHP trong `php.ini` nếu chưa bật: `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`, `zip`.
2. **Lấy mã nguồn dự án:**
   ```bash
   git clone https://github.com/quy0358/He-Thong-Quan-Ly-Benh-Vien-Tam-Anh.git
   cd He-Thong-Quan-Ly-Benh-Vien-Tam-Anh
   ```
3. **Mở dự án bằng Antigravity:**
   - Mở thư mục dự án trong IDE hỗ trợ Antigravity.
   - Chọn đúng **Prompt** theo tên của mình bên dưới, sao chép toàn bộ nội dung trong khung code block và dán vào cửa sổ chat của Antigravity.
   - Bấm gửi và để Antigravity tự động triển khai toàn bộ các nhiệm vụ, viết test, kiểm tra chạy thử và đẩy code lên nhánh tính năng trên GitHub.

---

## 👨‍💻 1. PROMPT DÀNH CHO: NGUYỄN VĂN ĐẠT (Chuyên khoa & Bác sĩ)

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

---

## 👨‍💻 2. PROMPT DÀNH CHO: NGUYỄN VĂN HƯNG (Hồ sơ Bệnh nhân & Quầy Lễ tân)

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

---

## 👨‍💻 3. PROMPT DÀNH CHO: VÕ LÊ GIA BẢO (Khám bệnh, Bệnh án, File an toàn & Báo cáo)

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

---

## 🧭 4. QUY TRÌNH HỢP NHẤT MÃ NGUỒN CỦA TECH LEAD (QUÝ)

Sau khi mỗi thành viên chạy xong prompt và tạo Pull Request (PR) trên GitHub:
1. Tech Lead truy cập tab **Pull Requests** trên GitHub.
2. Kiểm tra xem PR có bị conflict hay không.
3. Bấm **Merge Pull Request** vào nhánh `develop`.
4. Khi cả 3 thành viên đã hoàn tất và merge vào `develop`:
   ```bash
   git checkout main
   git pull origin main
   git merge develop
   git push origin main
   ```
5. Đóng băng mã nguồn (Code Freeze), chuẩn bị slide và chạy thử kịch bản demo 10 phút để sẵn sàng bảo vệ đồ án đạt điểm tối đa!
