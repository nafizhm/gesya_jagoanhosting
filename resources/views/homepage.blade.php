<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        @php
        $konfigurasi = \App\Models\PengaturanProfil::first();
        @endphp

        <title>{{ $konfigurasi->nama_perusahaan ?? 'Template Aplikasi' }}</title>
        @php
        $logoIcon = \App\Models\PengaturanMedia::where('jenis_data', 'fav icon')->first();
        @endphp

        @if($logoIcon && $logoIcon->nama_file)
        <link rel="icon" href="{{ asset('config_media/' . $logoIcon->nama_file) }}">
        @endif
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">

        <link rel="stylesheet" href="{{ asset('templateHomepage/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('templateHomepage/css/animate.min.css') }}">
        <link rel="stylesheet" href="{{ asset('templateHomepage/css/magnific-popup.css') }}">
        <link rel="stylesheet" href="{{ asset('templateHomepage/css/dripicons.css') }}">
        <link rel="stylesheet" href="{{ asset('templateHomepage/css/slick.css') }}">
        <link rel="stylesheet" href="{{ asset('templateHomepage/css/default.css') }}">
        <link rel="stylesheet" href="{{ asset('templateHomepage/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('templateHomepage/css/responsive.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/css/fontawesome-iconpicker.min.css" />
    </head>
    <body>

  <header class="custom-navbar" id="customNavbar">
    <nav class="custom-navbar-container">
    <div class="custom-navbar-logo">
        @if($logo && $logo->nama_file)
            <img src="{{ asset('konten/' . $logo->nama_file) }}"
                style="max-width: 50px; height: auto;"
                alt="Logo">
        @else
            <img src="{{ asset('default/logo.png') }}"
                style="max-width: 50px; height: auto;"
                alt="Default Logo">
        @endif
    </div>


        <button class="custom-navbar-toggle" id="customNavbarToggle">
            ☰
        </button>

        <ul class="custom-navbar-menu" id="customNavbarMenu">
            @foreach($navItems as $item)
                <li>
                    <a href="{{ $item->url_item ? '#' . $item->url_item : '#' }}"
                       class="{{ request()->url() == url($item->artikel) ? 'custom-navbar-active' : '' }}">
                        {{ $item->judul }}
                    </a>
                </li>
            @endforeach

            <li>
                <a href="{{ route('login') }}"
                   class="{{ request()->routeIs('login') ? 'custom-navbar-active' : '' }}">
                    Login
                </a>
            </li>
        </ul>
    </nav>
</header>



  <main>
    <section id="{{ $beranda->url_item ?? 'home' }}" class="slider-area fix p-relative">
            <div class="slider-active">
                @foreach($sliders as $slider)
                    @php
                        $path = public_path('konten/' . $slider->nama_file);
                        $imageUrl = file_exists($path) && !is_dir($path)
                            ? asset('konten/' . $slider->nama_file)
                            : asset('default/content1.png');
                    @endphp
                    <div class="single-slider slider-bg d-flex align-items-center"
                        style="background-image:url('{{ $imageUrl }}')">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="slider-content s-slider-content text-left">
                                        <p style="font-size: 10px; color: white; letter-spacing: 10px; line-height: 1;" data-animation="fadeInUp" data-delay=".4s">
                                            {!! nl2br(e($slider->judul)) !!}
                                        </p>
                                        <h1 style="color: white; font-size: 50px;" data-animation="fadeInUp" data-delay=".4s">
                                            {!! nl2br(e($slider->artikel)) !!}
                                        </h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>


        <section id="{{ $aboutUs->url_item ?? 'about' }}" class="about-area about-p pt-120 pb-120 p-relative">
            <div class="container">
                <div class="row align-items-center">

                    <div class="col-lg-6">
                        <div class="s-about-img p-relative" style="position: relative;">
                            @if ($aboutUs && $aboutUs->nama_file)
                                <img src="{{ asset('konten/' . $aboutUs->nama_file) }}" alt="About Image" class="img-fluid">
                            @else
                                <img src="{{ asset('default/content.png') }}" alt="Default Image" class="img-fluid">
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="about-content s-about-content pl-30">
                            <div class="about-title second-atitle">
                                <span>About Us</span>
                                <div class="about-title-flex">
                                    <h2>{{ $aboutUs->judul ?? 'Judul tidak tersedia' }}</h2>
                                    @if ($aboutUs && $aboutUs->icon)
                                        <i class="{{ $aboutUs->icon }}"></i>
                                    @endif
                                </div>
                            </div>
                            <p>{!! $aboutUs->artikel ?? 'Konten belum tersedia.' !!}</p>
                            <a href="#" class="btn">Lebih Lanjut</a>
                        </div>
                    </div>

                </div>
            </div>
        </section>



        <section id="{{ $product->url_item ?? 'produk' }}"
            class="py-4 py-md-5 px-md-5"
            style="background-color: #0f2c2c;">
        <div class="row g-4">
            @foreach($primaryContent as $item)
                @php
                    $path = public_path('konten/' . $item->nama_file);
                    $imageUrl = file_exists($path) && !is_dir($path)
                        ? asset('konten/' . $item->nama_file)
                        : asset('default/content.png');
                @endphp

            <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $item->judul }}"
                            style="object-fit: contain; width: auto; height: auto; max-height: 400px;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $item->judul }}</h5>
                            <p class="card-text">{{ $item->artikel }}</p>
                            <a href="#" class="btn btn-primary mt-auto">Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>


        <section id="{{ $fasilitas->url_item ?? 'fasilitas' }}" class="services-area services-bg services-two pt-120 pb-90">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-8 col-lg-10">
                            <div class="section-title text-center pl-40 pr-40 mb-80 wow fadeInDown" data-animation="fadeInDown" data-delay=".2s">
                                <span>Our Facilities</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        @foreach ($facilities as $facility)
                            <div class="col-lg-4 col-md-6 mb-30">
                                <div class="s-single-services wow fadeInUp" data-animation="fadeInUp" data-delay=".2s">
                                    <div class="services-ico2">
                                        <i class="{{ $facility->icon ?? 'fas fa-building' }}"></i>
                                    </div>
                                    <div class="second-services-content2">
                                        <h5>{{ $facility->judul ?? 'No Title' }}</h5>
                                        <p>{{ \Illuminate\Support\Str::limit($facility->artikel ?? 'Deskripsi belum tersedia.', 120) }}</p>
                                        <a href="#">Read More</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </section>


          <section id="{{ $secondaryContent->url_item ?? 'marketing' }}" class="py-5 px-3" style="background-color: #0f2c2c;">
            <div class="container">
               <div class="row mb-4">
                    <div class="col-12 text-center">
                        <h1 class="text-white">{{ $secondaryContent->judul ?? 'Marketing' }}</h1>
                        <p class="text-light">{{ $secondaryContent->artikel ?? '' }}</p>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach($marketing as $m)
                        @php
                            $fotoPath = public_path('photo/' . $m->foto);
                            $fotoUrl = file_exists($fotoPath) && !is_dir($fotoPath)
                                ? asset('photo/' . $m->foto)
                                : asset('default/content1.png');
                        @endphp

                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <img src="{{ $fotoUrl }}" class="card-img-top" alt="{{ $m->nama_marketing }}"
                                    style="object-fit: cover; width: 100%; height: 250px;">
                             <div class="card-body">
                                    <h5 class="card-title">{{ $m->nama_marketing }}</h5>

                                    <p class="card-text mb-1">
                                        <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>
                                        <span>{{ $m->alamat }}</span>
                                    </p>

                                    <p class="card-text">
                                        <i class="fas fa-phone" style="margin-right: 8px;"></i>
                                        <span>{{ $m->no_telp }}</span>
                                    </p>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        <section class="py-5">
        <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold">Site Plain</h2>
                <p class="text-muted">Klik tombol di bawah untuk mengunduh file.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a href="#" class="btn btn-primary btn-lg" download>
                    <i class="fas fa-download me-2"></i> Unduh Sekarang
                </a>
            </div>
        </div>
        </div>
        </section>



    </main>
   <footer id="{{ $tertiaryContent->url_item ?? 'contact' }}" class="footer-bg footer-p pt-100 pb-80" style="background-color: #0f2c2c">
    <div class="footer-top pb-30">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-xl-6 col-lg-6 col-sm-6">
                    <div class="footer-widget mb-30">
                        <div class="custom-navbar-logo mb-35">
                            @if($logo && $logo->nama_file)
                                <img src="{{ asset('konten/' . $logo->nama_file) }}">
                            @else
                                <img src="{{ asset('default/logo.png') }}" alt="Default Logo">
                            @endif
                        </div>

                        {{-- Google Maps Embed --}}
                        <div class="footer-map mb-30">
                            <iframe
                                src="https://www.google.com/maps?q=1.067222,103.954389&hl=es;z=14&amp;output=embed"
                                width="100%"
                                height="250"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>

                @foreach($footer as $footerItem)
                    @if($footerItem->judul && $footerItem->artikel)
                        <div class="col-xl-6 col-lg-3 col-sm-6">
                            <div class="footer-widget mb-30">
                                <div class="f-widget-title">
                                    <h5>{{ $footerItem->judul }}</h5>
                                </div>
                                <div class="footer-link">
                                    {!! nl2br(e($footerItem->artikel)) !!}
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

             @if($tertiaryContent && $tertiaryContent->judul && $tertiaryContent->artikel)
                <div class="col-12">
                    <div class="footer-widget mb-30 text-end">
                        <div class="f-widget-title">
                            <h5 class="bg-info text-white d-inline-block px-3 py-2 rounded">
                                <i class="fas fa-phone-alt me-2"></i> {{ $tertiaryContent->judul }}
                            </h5>
                            <p class="text-white mt-2">{{ $tertiaryContent->artikel }}</p>

                            <a href="#" class="btn btn-info mt-2">
                                <i class="fas fa-envelope me-1"></i> Kontak Kami
                            </a>
                        </div>
                    </div>
                </div>
            @endif


            </div>
        </div>
    </div>
    <div class="copyright-wrap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @php
                    $konfigurasi = \App\Models\PengaturanProfil::first();
                    @endphp
                    <div class="copyright-text text-center">
                        <p>&copy; 2025 {{ $konfigurasi->nama_perusahaan ?? 'Template Aplikasi' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>



<script src="{{ asset('templateHomepage/js/vendor/modernizr-3.5.0.min.js') }}"></script>
<script src="{{ asset('templateHomepage/js/vendor/jquery-1.12.4.min.js') }}"></script>
{{-- <script src="{{ asset('templateHomepage/js/popper.min.js') }}"></script> --}}
<script src="{{ asset('templateHomepage/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('templateHomepage/js/one-page-nav-min.js') }}"></script>
<script src="{{ asset('templateHomepage/js/slick.min.js') }}"></script>
<script src="{{ asset('templateHomepage/js/ajax-form.js') }}"></script>
<script src="{{ asset('templateHomepage/js/paroller.js') }}"></script>
<script src="{{ asset('templateHomepage/js/wow.min.js') }}"></script>
<script src="{{ asset('templateHomepage/js/js_isotope.pkgd.min.js') }}"></script>
<script src="{{ asset('templateHomepage/js/imagesloaded.min.js') }}"></script>
<script src="{{ asset('templateHomepage/js/parallax.min.js') }}"></script>
<script src="{{ asset('templateHomepage/js/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('templateHomepage/js/jquery.counterup.min.js') }}"></script>
<script src="{{ asset('templateHomepage/js/jquery.scrollUp.min.js') }}"></script>
{{-- <script src="{{ asset('templateHomepage/js/parallax-scroll.js') }}"></script> --}}
<script src="{{ asset('templateHomepage/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('templateHomepage/js/element-in-view.js') }}"></script>
<script src="{{ asset('templateHomepage/js/main.js') }}"></script>
</body>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});
</script>



<script>
 document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('customNavbarToggle');
        const menu = document.getElementById('customNavbarMenu');
        const navbar = document.getElementById('customNavbar');

        toggleBtn.addEventListener('click', function () {
            menu.classList.toggle('active');
        });

        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    });
</script>



</html>
