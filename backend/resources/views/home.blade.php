<!DOCTYPE html>
<html lang="en">
@include('layouts.head')
<body class="bg-dark">
    @include('layouts.navbar')
    @include('layouts.slider')

    <div class="container mt-4">
        <h2 class="text-white text-center">Welcome to the Employee Management System</h2>
        <p class="text-white text-center">Manage your employee records efficiently.</p>
    </div>

    <div style="position: fixed; bottom: 0; width: 100%;">
        @include('layouts.footer')
    </div>
</body>
</html>
