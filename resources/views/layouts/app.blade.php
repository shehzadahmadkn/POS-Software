<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default" data-bs-theme="light" data-layout-mode="light">

<head>
    <meta charset="utf-8" />
    <title>{{ \App\Models\Setting::getSetting('logo_text') ?: config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    
    @stack('styles')
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('layouts.topbar')
        
        @include('layouts.sidebar')

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    {{ $slot }}

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © POS Software.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by Themesbrand
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script>
        (function() {
            function updateThemeIcon() {
                var currentTheme = document.documentElement.getAttribute('data-bs-theme') || localStorage.getItem('data-bs-theme') || 'light';
                var iconBtn = document.querySelector('.light-dark-mode i');
                if (iconBtn) {
                    if (currentTheme === 'dark') {
                        iconBtn.classList.remove('bx-moon');
                        iconBtn.classList.add('bx-sun');
                    } else {
                        iconBtn.classList.remove('bx-sun');
                        iconBtn.classList.add('bx-moon');
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                updateThemeIcon();
                var toggleBtn = document.querySelector('.light-dark-mode');
                if (toggleBtn) {
                    toggleBtn.addEventListener('click', function() {
                        setTimeout(function() {
                            var currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                            localStorage.setItem('data-bs-theme', currentTheme);
                            sessionStorage.setItem('data-bs-theme', currentTheme);
                            updateThemeIcon();
                        }, 50);
                    });
                }
            });
        })();
    </script>

    @stack('scripts')
</body>

</html>
