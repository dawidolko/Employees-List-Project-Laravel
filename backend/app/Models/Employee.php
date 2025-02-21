<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $primaryKey = 'emp_no';
    public $incrementing = false;
    public $timestamps = false;
    protected $table = 'employees';

    public function deptEmps()
    {
        return $this->hasMany(DeptEmp::class, 'emp_no', 'emp_no');
    }

    public function currentDeptEmp()
    {
        return $this->hasOne(DeptEmp::class, 'emp_no', 'emp_no')->where('to_date', '9999-01-01');
    }

    public function currentDepartment()
    {
        return $this->hasOneThrough(
            Department::class,
            DeptEmp::class,
            'emp_no', 
            'dept_no',
            'emp_no', 
            'dept_no' 
        )->where('dept_emp.to_date', '9999-01-01');
    }

    public function titles()
    {
        return $this->hasMany(Title::class, 'emp_no', 'emp_no');
    }

    public function currentTitle()
    {
        return $this->hasOne(Title::class, 'emp_no', 'emp_no')->where('to_date', '9999-01-01');
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class, 'emp_no', 'emp_no');
    }

    public function currentSalary()
    {
        return $this->hasOne(Salary::class, 'emp_no', 'emp_no')->where('to_date', '9999-01-01');
    }
}
