<?php

namespace App\Models;

use App\Models\MyClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'short_name', 'school_id', 'my_class_id'
    ];

    /**
     * Get the class that owns the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function myClass()
    {
        return $this->belongsTo(MyClass::class);
    }
}