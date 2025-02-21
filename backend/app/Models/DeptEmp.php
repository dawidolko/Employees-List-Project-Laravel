<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeptEmp extends Model
{
    public $timestamps = false;
    protected $table = 'dept_emp';
    public $incrementing = false;

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_no', 'emp_no');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_no', 'dept_no');
    }
}
