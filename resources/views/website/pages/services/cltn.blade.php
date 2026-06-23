@extends('website.layout-detail')

@section('detail-content')

@section('title-detail')
    Cuti diluar Tanggungan Negara
@endsection

@section('desc-detail')
    Layanan Usulan Cuti diluar Tanggungan Negara
@endsection

<section id="blog-details" class="blog-details section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h3>Cuti diluar Tanggungan Negara</h3>
                <p style="text-align: justify">
                    Cuti di Luar Tanggungan Negara adalah cuti yang diberikan kepada PNS karena alasan pribadi dan
                    mendesak setelah memenuhi persyaratan.
                </p>
                <p style="text-align: justify">
                    PNS yang telah bekerja paling singkat 5 tahun secara terus-menerus karena
                    alasan pribadi dan mendesak dapat diberikan cuti di luar tanggungan negara. Cuti di luar tanggungan
                    negara dapat diberikan untuk paling lama 3 tahun.
                </p>
                <p style="text-align: justify">
                    Jangka waktu CLTN dapat diperpanjang paling lama 1 tahun. Permintaan/permohonan perpanjangan CLTN
                    harus sudah diajukan paling lambat 3 bulan sebelum CLTN berakhir.
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
                        <span>Peraturan Presiden No. 58 Tahun 2013 tentang Badan Kepegawaian Negara</span>
                    </li>
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>Peraturan BKN No. 24 Tahun 2017 tentang Tata Cara Pemberian Cuti PNS sebagaimana telah
                            diubah dengan Peraturan BKN No. 7 Tahun 2021</span>
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
