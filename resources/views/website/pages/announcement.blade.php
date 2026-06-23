@extends('website.layout-detail')

@section('detail-content')

@section('title-detail')
    Pengumuman
@endsection

@section('desc-detail')
    Kanal Pengumuman Kantor Regional XIV BKN
@endsection

<section id="blog-posts-2" class="blog-posts-2 section">
    <div class="container">
        <div class="row gy-4">

            @foreach ($announcement as $item)
                <div class="col-lg-4">
                    <article class="position-relative h-100">

                        <div class="post-img position-relative overflow-hidden">
                            <img src="uploads/{{ $item->file }}" class="img-fluid" alt="">
                        </div>

                        <div class="meta d-flex align-items-end">
                            <span
                                class="post-date">{{ $item->created_at->locale('id')->translatedFormat('d F Y') }}</span>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person"></i> <span class="ps-2">Admin Web</span>
                            </div>
                            <span class="px-3 text-black-50"></span>

                        </div>

                        <div class="post-content d-flex flex-column">

                            <h3 class="post-title">{{ $item->title }}</h3>
                            <a href="/detail-announcement/{{ encrypt($item->id) }}"
                                class="readmore stretched-link"><span>Read More</span><i
                                    class="bi bi-arrow-right"></i></a>

                        </div>

                    </article>
                </div>
            @endforeach

            {{ $announcement->links() }}


        </div>
    </div>
</section>
@endsection
