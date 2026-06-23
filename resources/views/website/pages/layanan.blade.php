@extends('website.page')

@section('detail-content')

@section('title-detail')
    Layanan
@endsection

@section('desc-detail')
    Layanan Kantor Regional XIV BKN
@endsection

<section id="services" class="services section">
    <div class="container">
        <div class="row gy-4">
            {{-- Penetapan NIP dan NI PPPK --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        {{-- <i class="bi bi-palette"></i> --}}
                        <img src="{{ asset('icons/tapnip.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Penetapan NIP dan NI PPPK</h3>
                    <p>Penetapan NIP dan NI PPPK oleh Instansi setelah peserta dinyatakan lulus seleksi.</p>
                    <a href="/layanan/penetapan-nip-nipppk" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- CLTN --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/cltn.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Cuti diluar Tanggungan Negara</h3>
                    <p>Pemberian cuti diluar tanggungan negara kepada PNS karena alasan pribadi dan mendesak</p>
                    <a href="/layanan/cltn" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- KP --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/kenaikan-pangkat.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Kenaikan Pangkat</h3>
                    <p>Penghargaan bagi PNS baik yang menduduki jabatan fungsional umum atau tertentu dan jabatan
                        struktural.</p>
                    <a href="/layanan/kenaikan-pangkat" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- PMK --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/pmk.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Peninjauan Masa Kerja</h3>
                    <p>Penghitungan masa kerja yang dimiliki oleh PNS sebelum diangkat menjadi CPNS.</p>
                    <a href="layanan/peninjauan-masa-kerja" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- Mutasi --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/mutasi.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Mutasi</h3>
                    <p>Layanan pengelolaan perpindahan pegawai yang diproses melalui Kantor Regional XIV BKN Manokwari.
                    </p>
                    <a href="layanan/mutasi" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- PGA --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/pencantuman-gelar.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Pencantuman Gelar</h3>
                    <p>Pengakuan resmi bagi PNS atas gelar akademik atau vokasi yang telah diperoleh.</p>
                    <a href="layanan/pencantuman-gelar" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- Pensiun --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/pensiun.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Pensiun</h3>
                    <p>Pensiun yang diberikan kepada PNS yang telah mencapai batas usia tertentu sebagai bentuk
                        penghargaan atas pengabdian serta jaminan di hari tua.</p>
                    <a href="layanan/pensiun" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- Pensiun Janda/Duda --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/pensiun-janda-duda.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Pensiun Janda/ Duda</h3>
                    <p>Pensiun yang diberikan kepada janda atau duda dari PNS yang meninggal dunia, tewas, atau hilang.
                    </p>
                    <a href="layanan/pensiun-janda-duda" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- Pengaktifan Kembali --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/aktif.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Pengaktifan Kembali PNS</h3>
                    <p>Pengembalian status aktif PNS yang sebelumnya diberhentikan sementara.</p>
                    <a href="layanan/pengaktifan-pns" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- Pengangkatan CPNS lebih 1 Tahun --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/cpns.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Pengangkatan CPNS Lebih 1 Tahun</h3>
                    <p>Pengangkatan CPNS melewati masa percobaan 1 tahun.</p>
                    <a href="layanan/pengangkatan-cpns" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- Peremajaan Data --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/peremajaan.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Peremajaan Data</h3>
                    <p>Pemutakhiran data PNS secara elektronik oleh pengelola kepegawaian sesuai kewenangan Kantor
                        Regional</p>
                    <a href="layanan/peremajaan-data" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- CAT --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/cat.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Fasilitasi Seleksi Metode CAT</h3>
                    <p>Seleksi berbasis komputer</p>
                    <a href="layanan/fasilitasi-seleksi-metode-cat" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- Manajemen Talenta --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/talenta.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Manajemen Talenta</h3>
                    <p>Sistem pengelolaan karier ASN berdasarkan potensi.</p>
                    <a href="layanan/manajemen-talenta" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- Pembinaan Manajemen --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/manajemen-asn.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Pembinaan Manajemen ASN</h3>
                    <p>Pembinaan manajemen ASN yang berkelanjutan</p>
                    <a href="layanan/pembinaan-manajemen-asn" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- Statistik Kepegawaian --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/statistik.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Statistik Kepegawaian</h3>
                    <p>Informasi tentang statistik pegawai di wilayah kerja Kantor Regional XIV BKN Manokwari</p>
                    <a href="layanan/statistik-kepegawaian" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
            {{-- Konsultasi Kepegawaian --}}
            <div class="col-lg-3 col-md-4">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="{{ asset('icons/konsultasi.svg') }}" alt="Menu Icon">
                    </div>
                    <h3>Konsultasi Kepegawaian</h3>
                    <p>Layanan konsultasi seputar permasalahan kepegawaian.</p>
                    <a href="kontak" class="service-link">
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
