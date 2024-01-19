<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffUnitGoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'staff_unit_goals';
    protected $fillable = [
        'staff_id',
        'unit_id',
        'goal_id'
    ];

    public function staff() {
        return $this->belongsTo(Staff::class);
    }

    public function goal() {
        return $this->belongsTo(Goal::class);
    }

    public function Unit() {
        return $this->belongsTo(Unit::class);
    }
}
