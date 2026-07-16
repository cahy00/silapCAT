<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Buat Short Link - Silapcat v2.0</title>
  <meta name="description" content="Sistem Laporan Pelaksanaan CAT - Buat Short Link Public">

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
  
  <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), url("{{ asset('assets/img/hero-bg.jpg') }}") center center no-repeat;
        background-size: cover;
        background-attachment: fixed;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .main-content {
        flex: 1;
        display: flex;
        align-items: center;
        padding: 40px 0;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.2);
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #0d6efd;
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }
    .form-control {
        border-left: none;
    }
  </style>
</head>

<body>

  <!-- Top Navigation (Simplified) -->
  <nav class="navbar navbar-dark py-3">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-4" href="/">
        <i class="bi bi-link-45deg fs-2 text-primary"></i>
        <span>Silapcat v2.0</span>
      </a>
      <a href="/" class="btn btn-outline-light rounded-pill px-4">Kembali ke Beranda</a>
    </div>
  </nav>

  <main class="main-content">
    <div class="container">
      <div class="row justify-content-center" data-aos="zoom-in" data-aos-duration="800">
        <div class="col-12 col-md-10 col-lg-8 col-xl-6">
          
          <div class="text-center mb-5">
            <h1 class="fw-bold text-white mb-3">Buat Short Link Baru</h1>
            <p class="text-white-50 fs-5">Sederhanakan tautan panjang Anda menjadi URL pendek yang mudah diingat.</p>
          </div>

          <div class="glass-card p-4 p-md-5">
              @if (session('success'))
                  <div class="alert alert-success rounded-4 border-0 mb-4 p-4 shadow-sm">
                      <h5 class="alert-heading fw-bold mb-2"><i class="bi bi-check-circle-fill me-2"></i>Berhasil Dibuat!</h5>
                      <p class="text-success mb-3">{{ session('success') }}</p>
                      
                      <div class="text-center mb-4">
                          <div class="bg-white d-inline-block p-3 rounded-4 shadow-sm">
                              {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate(session('short_url')) !!}
                          </div>
                          <div class="text-muted small mt-2">Scan QR Code ini untuk membuka link</div>
                      </div>

                      <div class="d-flex align-items-center gap-2">
                          <input type="text" class="form-control form-control-lg bg-white" value="{{ session('short_url') }}" id="resultUrl" readonly>
                          <button class="btn btn-primary btn-lg px-4" onclick="copyUrl()"><i class="bi bi-copy"></i> Salin</button>
                      </div>
                      <div class="mt-3">
                          <a href="{{ session('short_url') }}" target="_blank" class="btn btn-outline-primary w-100 rounded-pill"><i class="bi bi-box-arrow-up-right me-2"></i> Buka Link</a>
                      </div>
                  </div>
              @endif

              <form action="{{ route('public.shortlink.store') }}" method="POST">
                  @csrf

                  {{-- Pesan error rate limiting --}}
                  @if ($errors->has('throttle') || session('status') === 'throttled')
                      <div class="alert alert-warning rounded-4 border-0 mb-4 p-4">
                          <i class="bi bi-shield-exclamation me-2"></i>Terlalu banyak percobaan. Silakan tunggu sebentar.
                      </div>
                  @endif

                  {{-- Honeypot: field tersembunyi untuk menangkap bot --}}
                  <div style="position:absolute;left:-9999px;" aria-hidden="true">
                      <label for="website">Leave this empty</label>
                      <input type="text" name="website" id="website" tabindex="-1" autocomplete="off" value="">
                  </div>
                  
                  <div class="mb-4">
                      <label for="destination_url" class="form-label fw-bold text-dark">URL Tujuan <span class="text-danger">*</span></label>
                      <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                          <span class="input-group-text text-muted"><i class="bi bi-link-45deg"></i></span>
                          <input type="url" name="destination_url" id="destination_url" class="form-control @error('destination_url') is-invalid @enderror" placeholder="https://contoh.com/halaman-panjang" value="{{ old('destination_url') }}" required>
                      </div>
                      @error('destination_url')
                          <div class="text-danger small mt-2 fw-medium"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                      @enderror
                  </div>

                  <div class="mb-5">
                      <label for="short_code" class="form-label fw-bold text-dark">Kustom Kode (Opsional)</label>
                      <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                          <span class="input-group-text text-muted">{{ rtrim(url('/'), '/') }}/</span>
                          <input type="text" name="short_code" id="short_code" class="form-control @error('short_code') is-invalid @enderror" placeholder="kode-custom" value="{{ old('short_code') }}">
                      </div>
                      @error('short_code')
                          <div class="text-danger small mt-2 fw-medium"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                      @enderror
                      <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i>Kosongkan jika ingin dibuatkan otomatis. Hanya huruf, angka, strip, underscore.</div>
                  </div>

                  <div class="d-grid">
                      <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm" style="padding: 14px;">
                          Buat Short Link <i class="bi bi-arrow-right ms-2"></i>
                      </button>
                  </div>
              </form>
          </div>

        </div>
      </div>
    </div>
  </main>

  <footer class="py-4 mt-auto text-center">
    <div class="container">
      <p class="mb-0 text-white-50">&copy; <strong>Silapcat v2.0</strong>. All Rights Reserved</p>
    </div>
  </footer>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  
  <script>
    AOS.init();
    
    function copyUrl() {
        var copyText = document.getElementById("resultUrl");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value).then(function() {
            var btn = event.currentTarget;
            var originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check-lg"></i> Disalin!';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
            }, 2000);
        });
    }
  </script>
</body>
</html>
