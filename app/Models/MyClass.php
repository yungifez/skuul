<?php

namespace App\Models;

use App\Models\ClassGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyClass extends Model
{
    use HasFactory;

    protected $fillable =['name', 'class_group_id'];

    /**
     * Get the classGroup that owns the MyClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class);
    }
}
