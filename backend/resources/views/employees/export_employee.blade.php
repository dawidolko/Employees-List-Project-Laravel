<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .details { width: 100%; border-collapse: collapse; }
        .details td, .details th { border: 1px solid #ddd; padding: 8px; }
        .details th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Szczegóły pracownika</h2>
    </div>
    <table class="details">
        <tr>
            <th>Employee Number</th>
            <td>{{ $employee->emp_no }}</td>
        </tr>
        <tr>
            <th>Imię</th>
            <td>{{ $employee->first_name }}</td>
        </tr>
        <tr>
            <th>Nazwisko</th>
            <td>{{ $employee->last_name }}</td>
        </tr>
        <tr>
            <th>Płeć</th>
            <td>{{ $employee->gender }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                @if($employee->deptEmps->contains('to_date', '9999-01-01'))
                    Current
                @else
                    Former
                @endif
            </td>
        </tr>
        <tr>
            <th>Departament</th>
            <td>{{ optional($employee->currentDepartment)->dept_name ?? 'Brak' }}</td>
        </tr>
        <tr>
            <th>Tytuł zawodowy</th>
            <td>{{ optional($employee->currentTitle)->title ?? 'Brak' }}</td>
        </tr>
        <tr>
            <th>Aktualna pensja</th>
            <td>
                @if(optional($employee->currentSalary)->salary)
                    {{ $employee->currentSalary->salary }}
                @elseif($employee->salaries->isNotEmpty())
                    @php
                        $lastSalary = $employee->salaries->sortByDesc('to_date')->first();
                    @endphp
                    {{ $lastSalary->salary }} ({{ $lastSalary->to_date }})
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <th>Suma wszystkich wypłat</th>
            <td>{{ $totalSalaries }}</td>
        </tr>
    </table>
</body>
</html>
