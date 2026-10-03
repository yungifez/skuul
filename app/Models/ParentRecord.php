<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParentRecord extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    /**
     * Get the user that owns the StudentRecord.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The students that belong to the ParentRecord.
     *
     * @return BelongsToMany<User, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * The linked students who are enrolled at the school being worked in.
     *
     * A guardian can have children at other schools. Those stay out of view.
     *
     * @return BelongsToMany<User, $this>
     */
    public function studentsInSchool(): BelongsToMany
    {
        return $this->students()->whereIn('users.id', StudentRecord::query()->inSchool()->select('user_id'));
    }
}
