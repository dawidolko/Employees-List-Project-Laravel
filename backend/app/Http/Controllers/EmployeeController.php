<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status'); 
        $gender = $request->get('gender'); 
        $salaryMin = $request->get('salary_min');
        $salaryMax = $request->get('salary_max');
        $departmentId = $request->get('department');

        $departments = Department::orderBy('dept_name')->get();

        $query = Employee::query()
            ->with(['currentTitle', 'currentSalary', 'currentDepartment', 'deptEmps']);

        if ($gender) {
            $query->where('gender', $gender);
        }

        if ($status == 'current') {
            $query->whereHas('deptEmps', function($q) {
                $q->where('to_date', '9999-01-01');
            });
        } elseif ($status == 'former') {
            $query->whereDoesntHave('deptEmps', function($q) {
                $q->where('to_date', '9999-01-01');
            });
        }

        if ($salaryMin !== null) {
            $query->whereHas('currentSalary', function($q) use ($salaryMin) {
                $q->where('salary', '>=', $salaryMin);
            });
        }
        if ($salaryMax !== null) {
            $query->whereHas('currentSalary', function($q) use ($salaryMax) {
                $q->where('salary', '<=', $salaryMax);
            });
        }

        if ($departmentId) {
            $query->whereHas('currentDepartment', function($q) use ($departmentId) {
                $q->where('departments.dept_no', $departmentId);
            });
        }

        $employees = $query->paginate(15);

        return view('employees.index', compact(
            'employees',
            'status',
            'gender',
            'salaryMin',
            'salaryMax',
            'departmentId',
            'departments'
        ));
    }

    public function exportPdf(Request $request)
    {
        try {
            $status = $request->get('status');
            $gender = $request->get('gender');
            $salaryMin = $request->get('salary_min');
            $salaryMax = $request->get('salary_max');
            $departmentId = $request->get('department');

            $query = Employee::query()
                ->with(['currentTitle', 'currentSalary', 'currentDepartment', 'salaries']);

            if ($gender) {
                $query->where('gender', $gender);
            }

            if ($status == 'current') {
                $query->whereHas('deptEmps', function($q) {
                    $q->where('to_date', '9999-01-01');
                });
            } elseif ($status == 'former') {
                $query->whereDoesntHave('deptEmps', function($q) {
                    $q->where('to_date', '9999-01-01');
                });
            }

            if ($salaryMin !== null) {
                $query->whereHas('currentSalary', function($q) use ($salaryMin) {
                    $q->where('salary', '>=', $salaryMin);
                });
            }
            if ($salaryMax !== null) {
                $query->whereHas('currentSalary', function($q) use ($salaryMax) {
                    $q->where('salary', '<=', $salaryMax);
                });
            }

            if ($departmentId) {
                $query->whereHas('currentDepartment', function($q) use ($departmentId) {
                    $q->where('departments.dept_no', $departmentId);
                });
            }

            $employees = $query->get();

            ini_set('memory_limit', '4096M');

            $data = ['employees' => $employees];

            $pdf = PDF::loadView('employees.export', $data)
                        ->setPaper('A4', 'landscape');

            $filename = 'employees_export_' . date('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

    public function exportCsv(Request $request)
    {
        try {
            $status = $request->get('status');
            $gender = $request->get('gender');
            $salaryMin = $request->get('salary_min');
            $salaryMax = $request->get('salary_max');
            $departmentId = $request->get('department');

            $query = Employee::query()
                ->with(['currentTitle', 'currentSalary', 'currentDepartment', 'salaries']);

            if ($gender) {
                $query->where('gender', $gender);
            }

            if ($status == 'current') {
                $query->whereHas('deptEmps', function($q) {
                    $q->where('to_date', '9999-01-01');
                });
            } elseif ($status == 'former') {
                $query->whereDoesntHave('deptEmps', function($q) {
                    $q->where('to_date', '9999-01-01');
                });
            }

            if ($salaryMin !== null) {
                $query->whereHas('currentSalary', function($q) use ($salaryMin) {
                    $q->where('salary', '>=', $salaryMin);
                });
            }
            if ($salaryMax !== null) {
                $query->whereHas('currentSalary', function($q) use ($salaryMax) {
                    $q->where('salary', '<=', $salaryMax);
                });
            }

            if ($departmentId) {
                $query->whereHas('currentDepartment', function($q) use ($departmentId) {
                    $q->where('departments.dept_no', $departmentId);
                });
            }

            $employees = $query->get();
            $csv = "First Name,Last Name,Department,Title,Current Salary,Total Salaries\n";
            foreach ($employees as $employee) {
                $firstName = $employee->first_name;
                $lastName = $employee->last_name;
                $deptName = optional($employee->currentDepartment)->dept_name ?? '';
                $title = optional($employee->currentTitle)->title ?? '';
                $currentSalary = optional($employee->currentSalary)->salary ?? 0;
                $totalSalaries = $employee->salaries->sum('salary');

                $csv .= "\"$firstName\",\"$lastName\",\"$deptName\",\"$title\",$currentSalary,$totalSalaries\n";
            }
            
            $filename = 'employees_export_' . date('Y-m-d_H-i-s') . '.csv';
            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename=\"$filename\"");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export CSV: ' . $e->getMessage());
        }
    }
}
