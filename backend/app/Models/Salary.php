<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    public $timestamps = false;
    protected $table = 'salaries';
    public $incrementing = false;

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_no', 'emp_no');
    }
}
