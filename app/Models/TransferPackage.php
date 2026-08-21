<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * The copy of a student's records that one school handed to another.
 *
 * The package is written once. The receiving school reads it as a snapshot
 * labelled with where it came from, and never as its own record.
 *
 * @property array<string, mixed> $payload
 * @property array<int, string> $categories
 */
class TransferPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_sharing_request_id',
        'source_school_id',
        'destination_school_id',
        'student_record_id',
        'categories',
        'payload',
        'built_by',
        'received_at',
        'received_by',
        'received_student_record_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'categories' => 'array',
        'payload' => 'array',
        'received_at' => 'datetime',
    ];

    /**
     * Keep the handed-over copy exactly as it was handed over.
     *
     * Only the fields that record the receipt may be written afterwards.
     */
    protected static function booted(): void
    {
        static::updating(function (self $package): void {
            $allowed = ['received_at', 'received_by', 'received_student_record_id', 'updated_at'];

            if (array_diff(array_keys($package->getDirty()), $allowed) !== []) {
                throw new RuntimeException('A transfer package cannot be changed after it is built.');
            }
        });

        static::deleting(function (): void {
            throw new RuntimeException('A transfer package cannot be deleted.');
        });
    }

    /**
     * Check if the destination school took it in.
     */
    public function wasReceived(): bool
    {
        return $this->received_at !== null;
    }

    /**
     * Get the request that allowed it.
     *
     * @return BelongsTo<DataSharingRequest, $this>
     */
    public function dataSharingRequest(): BelongsTo
    {
        return $this->belongsTo(DataSharingRequest::class);
    }

    /**
     * Get the enrollment it describes.
     *
     * @return BelongsTo<StudentRecord, $this>
     */
    public function studentRecord(): BelongsTo
    {
        return $this->belongsTo(StudentRecord::class);
    }

    /**
     * Get the school it came from.
     *
     * @return BelongsTo<School, $this>
     */
    public function sourceSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'source_school_id');
    }

    /**
     * Get the school it went to.
     *
     * @return BelongsTo<School, $this>
     */
    public function destinationSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'destination_school_id');
    }
}
