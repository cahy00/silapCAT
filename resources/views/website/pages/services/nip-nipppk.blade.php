@extends('website.layout-detail')

@section('detail-content')

@section('title-detail')
    Penetapan NIP dan NI PPPK
@endsection

@section('desc-detail')
    Layanan Penetapan NIP dan NI PPPK
@endsection

<section id="blog-details" class="blog-details section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h3>Penetapan NIP dan NI PPPK</h3>
                <p style="text-align: justify">
                    Penetapan NIP bagi CPNS dan NI PPPK bagi Pegawai Pemerintah dengan Perjanjian Kerja merupakan proses
                    administrasi dilakukan oleh Instansi setelah peserta dinyatakan lulus seleksi.
                </p>
                <p style="text-align: justify">Usulan diajukan melalui sistem SIASN dengan melampirkan dokumen
                    persyaratan, kemudian diverifikasi
                    oleh Instansi dan Badan Kepegawaian Negara (BKN). Apabila persyaratan dinyatakan lengkap dan sah,
                    BKN akan menerbitkan Pertimbangan Teknis (Pertek) untuk NIP atau menetapkan NI PPPK, yang
                    selanjutnya menjadi dasar penerbitan Surat Keputusan (SK) pengangkatan dan penandatanganan
                    perjanjian kerja.
                </p>
                <p style="text-align: justify">
                    Proses ini dapat dipantau secara transparan melalui aplikasi <a
                        href="https://monitoring-siasn.bkn.go.id/" target="_blank">Monitoring Layanan
                        (MOLA) BKN</a>.
                </p>

                <h3 class="mt-4">Dasar Hukum</h3>
                <ul class="list-unstyled list-check">
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>UU No. 20 Tahun 2023 tentang Aparatur Sipil Negara</span>
                    </li>
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>PP No. 11 Tahun 2017 tentang Manajemen Pegawai Negeri Sipil sebagaimana telah diubah
                            dengan PP No. 17 Tahun 2020</span>
                    </li>
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>PP No. 49 Tahun 2018 Manajemen Pegawai Pemerintah dengan Perjanjian Kerja</span>
                    </li>
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>Permenpan 71 Tahun 2020 tentang Pemberian Kuasa Pengangkatan Pegawai Pemerintah dengan
                            Perjanjian Kerja</span>
                    </li>
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>Peraturan BKN No. 1 Tahun 2019 tentang Petunjuk Teknis Pengadaan Pegawai Pemerintah dengan
                            Perjanjian Kerja</span>
                    </li>
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>Peraturan BKN No. 18 Tahun 2020 Perubahan Atas Peraturan BKN No 1 Tahun 2019 Tentang
                            Petunjuk Teknis Pengadaan Pegawai Pemerintah Dengan Perjanjian Kerja</span>
                    </li>
                </ul>

            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <article class="article">

                    @foreach ($data as $item)
                        <h2 class="title">{{ $item->title }}</h2>

                        <div class="post-img position-relative overflow-hidden">
                            @if (!empty($item->thumbnail))
                                <img src="../uploads/{{ $item->thumbnail }}"
                                    alt="Primary subject related to the Pensiun service, 
                                possibly a civil servant or retirement documentation, shown in a professional setting with 
                                official documents or office background. If the image contains text, it is displayed clearly 
                                for informational purposes. The overall tone is formal and respectful, reflecting the importance of retirement 
                                services."
                                    class="img-fluid"
                                    style="min-height: 630px; max-height:630px; min-width:auto;max-width:auto">
                                <br>
                                <br>
                                <br>
                            @endif
                            <object data="../uploads/{{ $item->document }}" type="application/pdf" width="100%"
                                height="400px">
                                <p>Preview PDF tidak tersedia. <a href="../uploads/{{ $item->document }}">Download
                                        PDF</a>
                                </p>
                            </object>
                            <br>
                            <br>

                            <a href="{{$item->link}}"> <h4>Lihat Selengkapnya....</h4></a>

                        </div>
                    @endforeach
                </article>
            </div>
        </div>
    </div>
</section>
@endsection
