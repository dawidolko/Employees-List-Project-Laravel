<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    public $timestamps = false;
    protected $table = 'titles';
    public $incrementing = false;

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_no', 'emp_no');
    }
}
