<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .details, .history { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details td, .details th, .history td, .history th { border: 1px solid #ddd; padding: 8px; }
        .details th, .history th { background-color: #f2f2f2; }
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
            <td>
                @if(optional($employee->currentDepartment)->dept_name)
                    {{ $employee->currentDepartment->dept_name }}
                @elseif($employee->deptEmps->isNotEmpty())
                    @php
                        $lastDept = $employee->deptEmps->sortByDesc('to_date')->first();
                    @endphp
                    {{ optional($lastDept->department)->dept_name ?? 'Brak' }}
                @else
                    Brak
                @endif
            </td>
        </tr>
        <tr>
            <th>Tytuł zawodowy</th>
            <td>
                @if(optional($employee->currentTitle)->title)
                    {{ $employee->currentTitle->title }}
                @elseif($employee->titles->isNotEmpty())
                    @php
                        $lastTitle = $employee->titles->sortByDesc('to_date')->first();
                    @endphp
                    {{ $lastTitle->title }}
                @else
                    Brak
                @endif
            </td>
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
    
    <h3>Historia pensji</h3>
    <table class="history">
        <thead>
            <tr>
                <th>Od</th>
                <th>Do</th>
                <th>Pensja</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employee->salaries->sortBy('from_date') as $salary)
            <tr>
                <td>{{ $salary->from_date }}</td>
                <td>{{ $salary->to_date }}</td>
                <td>{{ $salary->salary }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <h3>Historia stanowiska</h3>
    <table class="history">
        <thead>
            <tr>
                <th>Od</th>
                <th>Do</th>
                <th>Stanowisko</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employee->titles->sortBy('from_date') as $title)
            <tr>
                <td>{{ $title->from_date }}</td>
                <td>{{ $title->to_date }}</td>
                <td>{{ $title->title }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
