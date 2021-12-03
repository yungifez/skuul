<?php

namespace App\Models;

use App\Models\User;
use App\Models\MyClass;
use App\Models\ClassGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'code', 'initials'
    ];

    public function classGroups()
    {
        return $this->hasMany(ClassGroup::class);
    }

    /**
     * Get all of the users for the School
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all of the MyClasses for the School
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function myClasses()
    {
        return $this->hasManyThrough(MyClass::class, ClassGroup::class);
    }
}
