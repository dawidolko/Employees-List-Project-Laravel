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
        $request->validate([
            'status'      => 'nullable|in:current,former',
            'gender'      => 'nullable|in:M,F',
            'salary_min'  => 'nullable|numeric|min:0',
            'salary_max'  => 'nullable|numeric|min:0',
            'department'  => 'nullable|exists:departments,dept_no',
        ], [
            'status.in'          => 'Wybrany status nie istnieje.',
            'gender.in'          => 'Wybrana płeć nie istnieje.',
            'salary_min.numeric' => 'Minimalna pensja musi być liczbą.',
            'salary_min.min'     => 'Minimalna pensja musi być co najmniej 0.',
            'salary_max.numeric' => 'Maksymalna pensja musi być liczbą.',
            'salary_max.min'     => 'Maksymalna pensja musi być co najmniej 0.',
            'department.exists'  => 'Wybrany dział nie istnieje.',
        ]);

        $status       = $request->get('status');
        $gender       = $request->get('gender');
        $salaryMin    = $request->get('salary_min');
        $salaryMax    = $request->get('salary_max');
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
            // Dla byłych pracowników ładujemy historię pensji oraz stanowisk
            $query->with(['salaries', 'titles']);
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

    public function show(Request $request, $emp_no)
    {
        $employee = Employee::with([
            'currentTitle', 
            'currentSalary', 
            'currentDepartment', 
            'deptEmps', 
            'salaries', 
            'titles'
        ])->findOrFail($emp_no);

        $totalSalaries = $employee->salaries->sum('salary');

        return view('employees.show', compact('employee', 'totalSalaries'));
    }

    public function exportEmployeePdf(Request $request, $emp_no)
    {
        $employee = Employee::with([
            'currentTitle', 
            'currentSalary', 
            'currentDepartment', 
            'deptEmps', 
            'salaries',
            'titles'
        ])->findOrFail($emp_no);

        $totalSalaries = $employee->salaries->sum('salary');

        $data = [
            'employee'      => $employee,
            'totalSalaries' => $totalSalaries
        ];

        ini_set('memory_limit', '4096M');

        $pdf = PDF::loadView('employees.export_employee', $data)
                  ->setPaper('A4', 'portrait');

        $filename = 'employee_export_' . $employee->emp_no . '_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportPdf(Request $request)
    {
        $request->merge([
            'department' => $request->input('department') === '' ? null : $request->input('department')
        ]);

        $request->validate([
            'status'      => 'nullable|in:current,former',
            'gender'      => 'nullable|in:M,F',
            'salary_min'  => 'nullable|numeric|min:0',
            'salary_max'  => 'nullable|numeric|min:0',
            'department'  => 'sometimes|nullable|exists:departments,dept_no',
        ], [
            'status.in'          => 'Wybrany status nie istnieje.',
            'gender.in'          => 'Wybrana płeć nie istnieje.',
            'salary_min.numeric' => 'Minimalna pensja musi być liczbą.',
            'salary_min.min'     => 'Minimalna pensja musi być co najmniej 0.',
            'salary_max.numeric' => 'Maksymalna pensja musi być liczbą.',
            'salary_max.min'     => 'Maksymalna pensja musi być co najmniej 0.',
            'department.exists'  => 'Wybrany dział nie istnieje.',
        ]);

        try {
            $status       = $request->get('status');
            $gender       = $request->get('gender');
            $salaryMin    = $request->get('salary_min');
            $salaryMax    = $request->get('salary_max');
            $departmentId = $request->get('department');
            $selectedIds  = $request->get('selected_ids');
            $allSelected  = $request->has('all_selected');

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

            if ($allSelected) {
                $deselectedIds = $request->get('deselected_ids');
                if ($deselectedIds) {
                    $query->whereNotIn('emp_no', $deselectedIds);
                }
            } else {
                if ($selectedIds) {
                    $query->whereIn('emp_no', $selectedIds);
                }
            }

            $employees = $query->get();

            if ($employees->count() > 1000) {
                return redirect()->back()->with('error', 'Export PDF może generować raporty max 1000 rekordowe.');
            }

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
            $status       = $request->get('status');
            $gender       = $request->get('gender');
            $salaryMin    = $request->get('salary_min');
            $salaryMax    = $request->get('salary_max');
            $departmentId = $request->get('department');
            $selectedIds  = $request->get('selected_ids');
            $allSelected  = $request->has('all_selected');

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

            if ($allSelected) {
                $deselectedIds = $request->get('deselected_ids');
                if ($deselectedIds) {
                    $query->whereNotIn('emp_no', $deselectedIds);
                }
            } else {
                if ($selectedIds) {
                    $query->whereIn('emp_no', $selectedIds);
                }
            }

            $employees = $query->get();
            $csv = "First Name,Last Name,Department,Title,Current Salary,Total Salaries\n";
            foreach ($employees as $employee) {
                $firstName   = $employee->first_name;
                $lastName    = $employee->last_name;
                $deptName    = optional($employee->currentDepartment)->dept_name ?? '';
                $title       = optional($employee->currentTitle)->title ?? '';
                $currentSalary = optional($employee->currentSalary)->salary;
                if (!$currentSalary && $employee->salaries->isNotEmpty()) {
                    $lastSalary = $employee->salaries->sortByDesc('to_date')->first();
                    $currentSalary = $lastSalary->salary;
                }
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

    public function exportPdfCheck(Request $request)
    {
        $request->merge([
            'department' => $request->input('department') === '' ? null : $request->input('department')
        ]);

        $request->validate([
            'status'      => 'nullable|in:current,former',
            'gender'      => 'nullable|in:M,F',
            'salary_min'  => 'nullable|numeric|min:0',
            'salary_max'  => 'nullable|numeric|min:0',
            'department'  => 'sometimes|nullable|exists:departments,dept_no',
        ], [
            'status.in'          => 'Wybrany status nie istnieje.',
            'gender.in'          => 'Wybrana płeć nie istnieje.',
            'salary_min.numeric' => 'Minimalna pensja musi być liczbą.',
            'salary_min.min'     => 'Minimalna pensja musi być co najmniej 0.',
            'salary_max.numeric' => 'Maksymalna pensja musi być liczbą.',
            'salary_max.min'     => 'Maksymalna pensja musi być co najmniej 0.',
            'department.exists'  => 'Wybrany dział nie istnieje.',
        ]);

        $status       = $request->get('status');
        $gender       = $request->get('gender');
        $salaryMin    = $request->get('salary_min');
        $salaryMax    = $request->get('salary_max');
        $departmentId = $request->get('department');
        $selectedIds  = $request->get('selected_ids');
        $allSelected  = $request->has('all_selected');

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
        if ($allSelected) {
            $deselectedIds = $request->get('deselected_ids');
            if ($deselectedIds) {
                $query->whereNotIn('emp_no', $deselectedIds);
            }
        } else {
            if ($selectedIds) {
                $query->whereIn('emp_no', $selectedIds);
            }
        }

        $employees = $query->get();

        if ($employees->count() > 1000) {
            return response()->json(['message' => 'Export PDF może generować raporty max 1000 rekordowe.'], 400);
        }

        return response()->json(['success' => true]);
    }
}
