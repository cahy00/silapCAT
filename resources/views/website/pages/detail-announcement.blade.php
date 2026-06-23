@extends('website.layout-detail')

@section('detail-content')

@section('title-detail')
    Pengumuman
@endsection

@section('desc-detail')
    Kanal Pengumuman Kantor Regional XIV BKN
@endsection

<section id="blog-details" class="blog-details section">
    <div class="container">
        <article class="article">

            <div class="post-img">
                <img src="../uploads/{{ $announcement->file }}" alt="" class="img-fluid"
                    style="min-height: 630px; max-height:630px; min-width:900px;max-width:900px">
            </div>

            <h2 class="title">{{ $announcement->title }}</h2>

            <div class="meta-top">
                <ul>
                    <li class="d-flex align-items-center"><i class="bi bi-person"></i> Admin Web</li>
                    <li class="d-flex align-items-center"><i class="bi bi-clock"></i>
                        {{ $announcement->created_at->locale('id')->translatedFormat('d F Y') }}</li>
                </ul>
            </div><!-- End meta top -->

            <div class="content">{!! $announcement->content !!}</div><!-- End post content -->

            <div class="meta-bottom">
                <i class="bi bi-folder"></i>
                <ul class="cats">
                    <li><a href="#">Business</a></li>
                </ul>

                <i class="bi bi-tags"></i>
                <ul class="tags">
                    <li><a href="#">Creative</a></li>
                    <li><a href="#">Tips</a></li>
                    <li><a href="#">Marketing</a></li>
                </ul>
            </div><!-- End meta bottom -->

        </article>
    </div>
</section>
@endsection
