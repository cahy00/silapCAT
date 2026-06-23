@extends('website.layout-detail')

@section('detail-content')

@section('title-detail')
    Kenaikan Pangkat
@endsection

@section('desc-detail')
    Layanan Kenaikan Pangkat
@endsection

<section id="blog-details" class="blog-details section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-12">
                <h3>Kenaikan Pangkat Reguler</h3>
                <p style="text-align: justify">
                    Kenaikan pangkat ini merupakan penghargaan bagi PNS yang tidak menduduki jabatan fungsional tertentu
                    atau jabatan struktural. Kenaikan ini diberikan dengan ketentuan tidak melampaui pangkat atasan
                    langsungnya, sekurang-kurangnya telah 4 (empat) tahun dalam pangkat terakhir, serta setiap unsur
                    penilaian prestasi kerja sekurang-kurangnya bernilai baik dalam 2 (dua) tahun terakhir. Selain itu,
                    kenaikan pangkat juga diberikan dalam batas jenjang pendidikan terakhir yang dimiliki.
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
                        <span>Keputusan Kepala BKN No. 12 Tahun 2002 Tentang Ketentuan Pelaksanaan PP No. 99 Tahun
                            2000 tentang Kenaikan Pangkat PNS Sebagaimana Telah Diubah Dengan PP No 12 Tahun 2002</span>
                    </li>
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>Peraturan Kepala BKN No. 25 Tahun 2012 tentang Pedoman Pemberian Persetujuan Teknis
                            Kenaikan Pangkat Reguler Pegawai Negeri Sipil Untuk Menjadi Pembina Tingkat I Golongan Ruang
                            IV/b Ke Bawah</span>
                    </li>
                </ul>

            </div>

            <div class="col-lg-12">
                <h3>Kenaikan Pangkat Pilihan Jabatan Fungsional Tertentu</h3>
                <p style="text-align: justify">
                    Kenaikan pangkat ini adalah penghargaan bagi PNS yang menduduki jabatan fungsional, di mana
                    penilaiannya menggunakan angka kredit. Kenaikan ini dapat diberikan apabila PNS telah
                    sekurang-kurangnya 2 (dua) tahun dalam pangkat terakhir, memenuhi angka kredit yang ditentukan,
                    serta setiap unsur penilaian prestasi kerja sekurang-kurangnya bernilai baik dalam 2 (dua) tahun
                    terakhir.
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
                        <span>Permenpan-RB No. 13 Tahun 2019 tentang Pengusulan, Penetapan, dan Pembinaan Jabatan
                            Fungsional PNS</span>
                    </li>
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>Keputusan Kepala BKN No. 12 Tahun 2002 Tentang Ketentuan Pelaksanaan PP No. 99 Tahun 2000
                            tentang Kenaikan Pangkat PNS Sebagaimana Telah Diubah Dengan PP No. 12 Tahun 2002</span>
                    </li>
                </ul>

            </div>

            <div class="col-lg-12">
                <h3>Kenaikan Pangkat Pilihan Jabatan Struktural</h3>
                <p style="text-align: justify">
                    Kenaikan pangkat ini adalah penghargaan dan kepercayaan yang diberikan kepada PNS yang menduduki
                    jabatan struktural. Kenaikan ini dapat diberikan apabila PNS telah 1 (satu) tahun dalam pangkat
                    terakhir, 1 (satu) tahun dalam jabatan struktural terhitung sejak tanggal pelantikan, serta setiap
                    unsur penilaian prestasi kerja bernilai baik dalam 2 (dua) tahun terakhir.
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
                        <span>Keputusan Kepala BKN No. 13 Tahun 2002 Tentang Ketentuan Pelaksanaan PP No. 100 Tahun 2002
                            Tentang Pengangkatan PNS Dalam Jabatan Struktural Sebagaimana Telah Diubah Dengan PP No. 13
                            Tahun 2002</span>
                    </li>
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>SE Menteri Pendayagunaan Aparatur Negara dan Reformasi Birokrasi No.
                            B/79/M.SM.02.03/2018 Tanggal 14 Agustus 2018 Perihal Pengisian Jabatan Pimpinan Tinggi (JPT)
                            Madya (Eselon I.a dan Eselon I.b) dan JPT Pratama (Eselon II.a dan Eselon II.b)</span>
                    </li>
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>SE Kepala BKN No. K.26-30/V.152-5/99 Tanggal 21 Agustus 2018 Perihal Pengisian Jabatan
                            Administrator (Eselon III.a dan III.b) dan Jabatan Pengawas (Eselon IV.a dan Eselon
                            IV.b)</span>
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
