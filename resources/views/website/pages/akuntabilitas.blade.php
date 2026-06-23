@extends('website.layout-detail')

@section('detail-content')

@section('title-detail')
    Akuntabilitas
@endsection

@section('desc-detail')
    Kanal Akuntabilitas Kantor Regional XIV BKN
@endsection

<section id="blog-posts-2" class="blog-posts-2 section">
    <div class="container">
        <div class="row gy-4">

            @foreach ($akuntabilitas as $item)
                <div class="col-lg-4">
                    <article class="position-relative h-100">

                        <div class="post-img position-relative overflow-hidden">
                            {{-- <img src="uploads/{{$item->file}}" class="img-fluid" alt=""> --}}
                            {{-- <iframe src="uploads/{{$item->file}}" frameborder="0" width="100%" height="250px"></iframe> --}}
                            <object data="uploads/{{ $item->file }}" type="application/pdf" width="100%"
                                height="250px">
                                <p>Preview PDF tidak tersedia. <a href="uploads/{{ $item->file }}">Download PDF</a>
                                </p>
                            </object>
                        </div>

                        <div class="meta d-flex align-items-end">
                            <span
                                class="post-date">{{ $item->created_at->locale('id')->translatedFormat('d F Y') }}</span>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person"></i> <span class="ps-2">Admin Web</span>
                            </div>

                        </div>


                        <div class="post-content d-flex flex-column">

                            <h3 class="post-title">{{ $item->title }}</h3>
                            <p class="opacity-50">
                                <span>Dokumen Publik</span>
                                <br>
                                <span>Tahun Anggaran {{ $item->year }}</span>


                            </p>
                            <a href="uploads/{{ $item->file }}" target="_blank"
                                class="readmore stretched-link"><span>Read More</span><i
                                    class="bi bi-arrow-right"></i></a>

                        </div>

                    </article>
                </div>
            @endforeach

            {{ $akuntabilitas->links() }}


        </div>
    </div>
</section>
@endsection
