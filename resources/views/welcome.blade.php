<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Silapcat v2.0 - Sistem Laporan Pelaksanaan CAT</title>
  <meta name="description" content="Sistem Informasi Laporan Pelaksanaan CAT (Silapcat) v2.0 - Memudahkan pelaporan dan monitoring pelaksanaan seleksi CAT.">
  <meta name="keywords" content="CAT, BKN, Seleksi, CPNS, PPPK, Silapcat">

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

  <style>
    .hero {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url("{{ asset('assets/img/hero-bg.jpg') }}") center center no-repeat;
        background-size: cover;
    }
  </style>
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="/" class="logo d-flex align-items-center">
        <h1 class="sitename">Silapcat v2.0</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="#about">Tentang</a></li>
          <li><a href="#features">Fitur</a></li>
          <li><a href="#contact">Kontak</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      @if (Route::has('login'))
        <div class="d-flex align-items-center">
            @auth
                <a class="btn-getstarted" href="{{ url('/admin') }}">Dashboard Admin</a>
            @else
                <a class="btn-getstarted" href="{{ route('login') }}">Log In</a>
            @endauth
        </div>
      @endif

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section">
      <div class="container">
        <div class="row gy-4 justify-content-center" data-aos="zoom-in" data-aos-delay="100">
          <div class="col-xl-6 col-lg-8 text-center">
            <h1>Sistem Laporan Pelaksanaan CAT</h1>
            <p>Digitalisasi monitoring dan pelaporan kegiatan seleksi berbasis Computer Assisted Test (CAT).</p>
            <div class="d-flex justify-content-center gap-3">
              <a href="{{ url('/admin') }}" class="btn-get-started">Kelola Kegiatan</a>
              <a href="#about" class="btn-watch-video d-flex align-items-center"><i class="bi bi-info-circle"></i><span>Pelajari Selengkapnya</span></a>
            </div>
          </div>
        </div>
      </div>
    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4">
          <div class="col-lg-6 order-1 order-lg-2">
            <img src="{{ asset('assets/img/about.jpg') }}" class="img-fluid rounded-4" alt="">
          </div>
          <div class="col-lg-6 order-2 order-lg-1 content">
            <h3>Solusi Terintegrasi Pelaksanaan Seleksi</h3>
            <p class="fst-italic">
              Silapcat hadir untuk mempermudah koordinasi antara pusat, instansi, dan titik lokasi dalam setiap tahapan seleksi CAT.
            </p>
            <ul>
              <li><i class="bi bi-check2-all"></i> <span>Monitoring Real-time status kegiatan.</span></li>
              <li><i class="bi bi-check2-all"></i> <span>Manajemen inventaris (PC & Ruangan) di setiap lokasi.</span></li>
              <li><i class="bi bi-check2-all"></i> <span>Pelaporan terpusat dan aman.</span></li>
            </ul>
            <p>
              Dengan arsitektur v2.0 yang lebih modern, kami menjamin integritas data dan kemudahan akses bagi seluruh pemangku kepentingan.
            </p>
          </div>
        </div>
      </div>
    </section><!-- /About Section -->

    <!-- Features Section -->
    <section id="features" class="features section bg-light">
      <div class="container section-title" data-aos="fade-up">
        <h2>Fitur Utama</h2>
        <p>Teknologi yang kami gunakan dirancang khusus untuk kebutuhan operasional seleksi CAT skala besar.</p>
      </div>

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="features-item">
              <i class="bi bi-briefcase" style="color: #ffbb2c;"></i>
              <h3><a href="" class="stretched-link">Manajemen Pengadaan</a></h3>
            </div>
          </div>
          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="200">
            <div class="features-item">
              <i class="bi bi-map" style="color: #5578ff;"></i>
              <h3><a href="" class="stretched-link">Pemetaan Lokasi</a></h3>
            </div>
          </div>
          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="300">
            <div class="features-item">
              <i class="bi bi-person-badge" style="color: #e80368;"></i>
              <h3><a href="" class="stretched-link">Penugasan Pegawai</a></h3>
            </div>
          </div>
          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="400">
            <div class="features-item">
              <i class="bi bi-file-earmark-pdf" style="color: #e361ff;"></i>
              <h3><a href="" class="stretched-link">Arsip Dokumen Digital</a></h3>
            </div>
          </div>
        </div>
      </div>
    </section><!-- /Features Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Kontak Kami</h2>
        <p>Butuh bantuan atau informasi lebih lanjut mengenai Silapcat v2.0?</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4">
          <div class="col-lg-12">
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
              <i class="bi bi-geo-alt flex-shrink-0"></i>
              <div>
                <h3>Alamat</h3>
                <p>Kantor Pusat BKN, Jakarta Timur, Indonesia</p>
              </div>
            </div>
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-telephone flex-shrink-0"></i>
              <div>
                <h3>Telepon</h3>
                <p>+62 21 1234 5678</p>
              </div>
            </div>
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-envelope flex-shrink-0"></i>
              <div>
                <h3>Email</h3>
                <p>info@bkn.go.id</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section><!-- /Contact Section -->

  </main>

  <footer id="footer" class="footer">
    <div class="container copyright text-center mt-4">
      <p>&copy; <span>Copyright</span> <strong class="px-1 sitename">Silapcat v2.0</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        Dikelola oleh Tim IT BKN
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>

  <script>
    AOS.init();
  </script>

</body>

</html>
