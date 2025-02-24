@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Szczegóły pracownika</h1>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ $employee->first_name }} {{ $employee->last_name }}</span>
            <a href="{{ route('employees.export.employee.pdf', $employee->emp_no) }}" class="btn btn-success">
                Export PDF
            </a>
        </div>
        <div class="card-body">
            <p><strong>Employee Number:</strong> {{ $employee->emp_no }}</p>
            <p><strong>Imię:</strong> {{ $employee->first_name }}</p>
            <p><strong>Nazwisko:</strong> {{ $employee->last_name }}</p>
            <p><strong>Płeć:</strong> {{ $employee->gender }}</p>
            <p>
                <strong>Status:</strong>
                @if($employee->deptEmps->contains('to_date', '9999-01-01'))
                    Current
                @else
                    Former
                @endif
            </p>
            <p><strong>Departament:</strong> {{ optional($employee->currentDepartment)->dept_name ?? 'Brak' }}</p>
            <p><strong>Tytuł zawodowy:</strong> {{ optional($employee->currentTitle)->title ?? 'Brak' }}</p>
            <p>
                <strong>Aktualna pensja:</strong>
                @if(optional($employee->currentSalary)->salary)
                    {{ $employee->currentSalary->salary }}
                @elseif($employee->salaries->isNotEmpty())
                    @php
                        $lastSalary = $employee->salaries->sortByDesc('to_date')->first();
                    @endphp
                    {{ $lastSalary->salary }} <small>(ostatnia, {{ $lastSalary->to_date }})</small>
                @else
                    -
                @endif
            </p>
            <p><strong>Suma wszystkich wypłat:</strong> {{ $totalSalaries }}</p>
        </div>
    </div>
    <a href="{{ route('employees.index') }}" class="btn btn-secondary mt-3">Powrót do listy</a>
</div>
@endsection
