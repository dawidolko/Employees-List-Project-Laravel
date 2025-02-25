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
            <p>
                <strong>Departament:</strong>
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
            </p>
            <p>
                <strong>Tytuł zawodowy:</strong>
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
            </p>
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
    
    <!-- Historia pensji -->
    <h3 class="mt-4">Historia pensji</h3>
    <table class="table table-bordered">
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

    <!-- Historia stanowiska -->
    <h3 class="mt-4">Historia stanowiska</h3>
    <table class="table table-bordered">
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

    <!-- Wykres pensji -->
    @php
        $salaries = $employee->salaries->sortBy('from_date');
        $labels = $salaries->map(function($s) { return $s->from_date; })->toArray();
        $dataPoints = $salaries->map(function($s) { return $s->salary; })->toArray();
        $chartConfig = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Pensja',
                        'data' => $dataPoints,
                        'fill' => false,
                        'borderColor' => 'blue'
                    ]
                ]
            ],
            'options' => [
                'title' => [
                    'display' => true,
                    'text' => 'Historia pensji'
                ]
            ]
        ];
        $chartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig));
    @endphp

    <h3 class="mt-4">Wykres pensji</h3>
    <img src="{{ $chartUrl }}" alt="Wykres pensji" style="max-width: 100%;">
    
    <a href="{{ route('employees.index') }}" class="btn btn-secondary mt-3">Powrót do listy</a>
</div>
@endsection
