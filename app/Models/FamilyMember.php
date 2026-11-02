<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    use HasFactory;

    public const REL_CHILD = 'child';
    public const REL_PARENT = 'parent';
    public const REL_SPOUSE = 'spouse';
    public const REL_SIBLING = 'sibling';
    public const REL_OTHER = 'other';

    protected $fillable = [
        'user_id',
        'patient_profile_id',
        'relationship',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->patientProfile?->full_name ?? 'N/A';
    }

    public function getDateOfBirthAttribute(): ?Carbon
    {
        $dob = $this->patientProfile?->dob ?? $this->patientProfile?->date_of_birth;
        return $dob ? Carbon::parse($dob) : null;
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : 0;
    }

    public function getGenderAttribute(): string
    {
        return $this->patientProfile?->gender ?? 'other';
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->patientProfile?->phone;
    }

    public function getHealthInsuranceCodeAttribute(): ?string
    {
        return $this->patientProfile?->insurance_code ?? $this->patientProfile?->health_insurance_code;
    }

    public function getMedicalNotesAttribute(): ?string
    {
        return $this->notes ?: $this->patientProfile?->medical_history;
    }

    public function getRelationshipLabelAttribute(): string
    {
        return match ($this->relationship) {
            self::REL_CHILD => 'Con cái',
            self::REL_PARENT => 'Cha / Mẹ',
            self::REL_SPOUSE => 'Vợ / Chồng',
            self::REL_SIBLING => 'Anh / Chị / Em',
            default => 'Người thân khác',
        };
    }

    public function getRelationshipNameAttribute(): string
    {
        return $this->relationship_label;
    }
}
