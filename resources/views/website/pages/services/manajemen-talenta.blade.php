@extends('website.layout-detail')

@section('detail-content')

@section('title-detail')
    Manajemen Talenta
@endsection

@section('desc-detail')
    Layanan Manajemen Talenta
@endsection

<section id="blog-details" class="blog-details section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h3>Manajemen Talenta</h3>
                <p style="text-align: justify">
                    Manajemen Talenta ASN merupakan sistem manajemen karier Aparatur Sipil Negara yang mencakup tahapan
                    akuisisi, pengembangan, retensi, dan penempatan talenta berdasarkan potensi dan kinerja tertinggi.
                </p>

                <h3 class="mt-4">Dasar Hukum</h3>
                <ul class="list-unstyled list-check">
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>UU No. 20 Tahun 2023 tentang Aparatur Sipil Negara</span>
                    </li>
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>Keppres No. 21 Tahun 2021 tentang Gugus Tugas Manajemen Talenta Nasional;</span>
                    </li>
                    <li class="d-flex mb-2 align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2"></i>
                        <span>Permenpan RB No. 3 Tahun 2020 tentang Manajemen Talenta Aparatur Sipil Negara.</span>
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
