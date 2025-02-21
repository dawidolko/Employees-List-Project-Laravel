<!DOCTYPE html>
<html lang="en">
    @include('layouts.head')
<body class="d-flex flex-column min-vh-100 bg-dark text-light">
    @include('layouts.navbar')
    <main class="flex-grow-1">
        @yield('content')
    </main>
    @include('layouts.footer')
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <div id="exportToast" class="toast align-items-center text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastBody"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @yield('scripts')
</body>
</html>
