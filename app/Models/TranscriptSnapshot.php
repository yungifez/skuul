<?php

namespace App\Models;

use App\Traits\InSchool;
use Database\Factories\TranscriptSnapshotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class TranscriptSnapshot extends Model
{
    /** @use HasFactory<TranscriptSnapshotFactory> */
    use HasFactory;

    use InSchool;

    public const UPDATED_AT = null;

    protected $fillable = ['school_id', 'student_record_id', 'revision', 'payload', 'reason', 'issued_at', 'issued_by'];

    protected $casts = ['revision' => 'integer', 'payload' => 'array', 'issued_at' => 'datetime', 'created_at' => 'datetime'];

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new RuntimeException('An issued transcript cannot be changed. Issue a new revision instead.');
        });
        static::deleting(function (): void {
            throw new RuntimeException('An issued transcript cannot be deleted.');
        });
    }
}
