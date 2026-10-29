<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;

class AttachmentPolicy
{
    /**
     * Determine whether the user can download or view the attachment.
     */
    public function download(User $user, Attachment $attachment): bool
    {
        // 1. Quản trị viên (Admin) luôn có toàn quyền tải và xem tài liệu y tế
        if ($user->isAdmin()) {
            return true;
        }

        $record = $attachment->medicalRecord;
        if (!$record) {
            // Nếu tệp không gắn với bệnh án cụ thể, chỉ người upload hoặc admin được tải
            return $attachment->uploaded_by_user_id === $user->id;
        }

        // 2. Bác sĩ điều trị trực tiếp phụ trách ca khám
        if ($user->isDoctor() && $user->doctor) {
            if ($user->doctor->id === $record->doctor_id) {
                return true;
            }
        }

        // 3. Chính bệnh nhân sở hữu hồ sơ bệnh án này
        if ($user->isPatient() && $user->patientProfile) {
            if ($user->patientProfile->id === $record->patient_profile_id) {
                return true;
            }
        }

        // 4. Người trực tiếp tải lên tệp này
        if ($attachment->uploaded_by_user_id === $user->id) {
            return true;
        }

        return false;
    }
}

