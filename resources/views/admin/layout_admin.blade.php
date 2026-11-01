<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $konfigurasi = \App\Models\PengaturanProfil::first();
    @endphp

    <title>{{ $konfigurasi->nama_perusahaan ?? 'Template Aplikasi' }}</title>


    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $logo = \App\Models\PengaturanMedia::where('jenis_data', 'fav icon')->first();
    @endphp

    @if ($logo && $logo->nama_file)
        <link rel="icon" href="{{ asset('config_media/' . $logo->nama_file) }}">
    @endif

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/css/fontawesome-iconpicker.min.css" />
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('templates/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/summernote/summernote-bs4.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('templates/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/toastr/toastr.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">

    @stack('styles')

    <style>
        .modal-body {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">


    <div class="wrapper">

        <!-- Preloader -->
        {{-- <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ asset('templates/img/LOGO_ICON_BEASISWA.png') }}" alt="AdminLTELogo"
                height="60" width="60">
        </div> --}}


        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-light" style="background-color: #28a745">
            <ul class="navbar-nav d-flex align-items-center" style="margin-left: 5px;">
                <li class="nav-item">
                    <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto mr-3 d-flex align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#"
                        id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="fas fa-user-circle fa-2x mr-2 text-white"></i>
                        <span class="text-white">{{ Auth::user()->username }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#" onclick="logoutConfirm(event)">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>

        </nav>



        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            @php
                $logo = \App\Models\PengaturanMedia::where('jenis_data', 'logo website')->first();
            @endphp

            <a href="#"
                class="brand-link d-flex flex-column align-items-center justify-content-center text-center py-2"
                style="text-decoration: none;">
                <img src="{{ asset('config_media/' . ($logo->nama_file ?? 'default.png')) }}" alt="Logo Website"
                    class="brand-image">
                @php
                    $konfigurasi = \App\Models\PengaturanProfil::first();
                @endphp

                {{-- <span class="brand-text font-weight-light d-block">
                    <strong>{{ $konfigurasi->nama_perusahaan ?? 'Template Aplikasi' }}</strong>
                </span> --}}
            </a>


            <!-- Sidebar -->
            <div class="sidebar">

                <!-- Sidebar Search -->
                <div class="form-inline px-3 pb-2 mt-2">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Cari menu..."
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        @php
                            $menus = session('getmenus');
                        @endphp

                        @foreach ($menus as $menu)
                            @if ($menu->children->isEmpty())
                                <li class="nav-item">
                                    <a href="{{ route($menu->route_name) }}"
                                        class="nav-link {{ $menu->route_name === 'dashboard'
                                            ? (request()->routeIs('dashboard') || request()->routeIs('dashboard.*')
                                                ? 'active'
                                                : '')
                                            : (request()->routeIs($menu->route_name)
                                                ? 'active'
                                                : '') }}"
                                        @if (
                                            ($menu->route_name === 'dashboard' && (request()->routeIs('dashboard') || request()->routeIs('dashboard.*'))) ||
                                                ($menu->route_name !== 'dashboard' && request()->routeIs($menu->route_name))) style="background-color: #28a745; color: white;" @endif>
                                        <i class="nav-icon fas {{ $menu->icon }}"></i>
                                        <p>{{ $menu->title }}</p>
                                    </a>
                                </li>
                            @else
                                {{-- Menu dengan anak --}}
                                @php
                                    $isActive = $menu->children
                                        ->pluck('route_name')
                                        ->contains(fn($route) => request()->routeIs($route) || request()->routeIs(explode('.', $route)[0] . '.*'));
                                @endphp
                                <li class="nav-item {{ $isActive ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ $isActive ? 'active' : '' }}"
                                        @if ($isActive) style="background-color: #28a745; color: white;" @endif>
                                        <i class="nav-icon fas {{ $menu->icon }}"></i>
                                        <p>
                                            {{ $menu->title }}
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @foreach ($menu->children as $child)
                                            <li class="nav-item">
                                                <a href="{{ route($child->route_name) }}"
                                                    class="nav-link {{ request()->routeIs($child->route_name) || request()->routeIs(explode('.', $child->route_name)[0] . '.*') ? 'active' : '' }}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>{{ $child->title }}</p>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endforeach



                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST"
                            style="display: none;">
                            @csrf
                        </form>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        @yield('content')

        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <strong>Copyright &copy; {{ date('Y') }} <a href="#"
                    class="text-gray">{{ $konfigurasi->nama_perusahaan ?? 'Template Aplikasi' }}</a>.</strong>
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
    <!-- jQuery -->
    <script src="{{ asset('templates/plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('templates/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('templates/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('templates/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('templates/plugins/chart.js/Chart.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('templates/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('templates/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('templates/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('templates/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('templates/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('templates/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- Page specific script -->
    <!-- AdminLTE App -->
    <script src="{{ asset('templates/dist/js/adminlte.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('templates/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('templates/plugins/toastr/toastr.min.js') }}"></script>
    <!-- bs-custom-file-input -->
    <script src="{{ asset('templates/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/js/fontawesome-iconpicker.min.js">
    </script>

    <script>
        var audio = new Audio('{{ asset('audio/notification.ogg') }}');

        function formatNumber(value) {
            return value ? value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") : '';
        }

        function unformatNumber(value) {
            return value ? parseInt(value.replace(/\./g, '')) : 0;
        }

        $(document).on('input', '.format-number', function() {
            let input = $(this).val().replace(/[^\d]/g, '');
            let formatted = input.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            $(this).val(formatted);
        });
    </script>


    <!-- Page specific script -->
    <script>
        function logoutConfirm(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan keluar dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>

    <script>
        $(function() {
            bsCustomFileInput.init();
        });
    </script>

    @stack('scripts')
</body>

</html>
