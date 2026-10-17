<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatientProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_code',
        'full_name',
        'dob',
        'gender',
        'phone',
        'email',
        'address',
        'identity_card',
        'insurance_code',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function getGenderNameAttribute(): string
    {
        return match ($this->gender) {
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
            default => 'Chưa xác định',
        };
    }

    public static function generatePatientCode(): string
    {
        $today = Carbon::now()->format('Ymd');
        $prefix = "BN-{$today}-";

        $latest = self::where('patient_code', 'LIKE', "{$prefix}%")
            ->orderBy('patient_code', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->patient_code, -4);
            $nextNumber = str_pad((string)($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "{$prefix}{$nextNumber}";
    }
}
