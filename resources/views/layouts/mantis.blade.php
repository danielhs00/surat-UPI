<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->
<head>
    <title>@yield('title', 'Template Mantis')</title>

    {{-- [Meta] --}}
    @include('components.mantis.meta')

    {{-- [CSS/Links] --}}
    @include('components.mantis.links')
</head>
<!-- [Head] end -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <!-- [ Sidebar Menu ] start -->
    @include('components.mantis.sidebar')
    <!-- [ Sidebar Menu ] end -->

    <!-- [ Header Topbar ] start -->
    @include('components.mantis.header')
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">

            <!-- [ breadcrumb ] start -->
            @include('components.mantis.breadcrumbs')
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                @if (session('success'))
                    <div class="">
                        <div class="alert alert-success" id="success-alert" role="alert">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->

    @include('components.mantis.footer')

    <!-- [Page Specific JS] start -->
    <script src="{{ asset('dist/assets/js/plugins/apexcharts.min.js') }}"></script>
    <script src="{{ asset('dist/assets/js/pages/dashboard-default.js') }}"></script>
    <!-- [Page Specific JS] end -->

    <!-- Required Js -->
    <script src="{{ asset('dist/assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('dist/assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('dist/assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('dist/assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('dist/assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('dist/assets/js/plugins/feather.min.js') }}"></script>

    <script>layout_change('light');</script>
    <script>change_box_container('false');</script>
    <script>layout_rtl_change('false');</script>
    <script>preset_change("preset-1");</script>
    <script>font_change("Public-Sans");</script>

    @stack('scripts')
</body>
</html>