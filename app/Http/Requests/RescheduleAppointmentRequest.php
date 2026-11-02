<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'shift' => ['required', 'in:morning,afternoon'],
            'reschedule_reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'appointment_date.required' => 'Vui lòng chọn ngày khám mới.',
            'appointment_date.date' => 'Ngày khám không đúng định dạng.',
            'appointment_date.after_or_equal' => 'Ngày khám mới phải từ hôm nay trở đi.',
            'shift.required' => 'Vui lòng chọn ca khám mới.',
            'shift.in' => 'Ca khám không hợp lệ (chỉ chấp nhận Sáng hoặc Chiều).',
            'reschedule_reason.max' => 'Lý do đổi lịch không được vượt quá 500 ký tự.',
        ];
    }
}
