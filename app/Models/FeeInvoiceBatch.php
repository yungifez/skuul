<?php

namespace App\Models;

use App\Traits\InSchool;
use Illuminate\Database\Eloquent\Model;

class FeeInvoiceBatch extends Model
{
    use InSchool;

    protected $fillable = [
        'school_id',
        'idempotency_key',
    ];
}
