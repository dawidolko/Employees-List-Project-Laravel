<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'dept_no';
    public $incrementing = false;
    public $timestamps = false;
    protected $table = 'departments';

    public function deptEmps()
    {
        return $this->hasMany(DeptEmp::class, 'dept_no', 'dept_no');
    }
}
