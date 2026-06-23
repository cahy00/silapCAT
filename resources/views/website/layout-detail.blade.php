@extends('website.index')

@section('content')
    <div class="page-title dark-background" data-aos="fade"
        style="background-image: url({{ asset('bkn/bknbaru.jpg') }}); object-fit:cover">
        <div class="container position-relative">
            <h1>@yield('title-detail')</h1>
            <p>@yield('desc-detail')</p>
            {{-- <nav class="breadcrumbs">
			<ol>
				<li><a href="index.html">Home</a></li>
				<li class="current">Blog Details</li>
			</ol>
		</nav> --}}
        </div>
    </div>

    <div class="container">
        <div class="row">

            <div class="col-lg-8">

                @yield('detail-content')

            </div>

            <div class="col-lg-4 sidebar">

                <div class="widgets-container">


                    <!-- Search Widget -->
                    <div class="search-widget widget-item">

                        <h3 class="widget-title">Search</h3>
                        <form action="">
                            <input type="text">
                            <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                        </form>

                    </div><!--/Search Widget -->

                    <div class="blog-author-widget widget-item">
                        <iframe src="https://www.instagram.com/bknkanreg14/embed/" width="100%" height="400"
                            frameborder="0" scrolling="no"></iframe>
                    </div>

                    <div class="blog-author-widget widget-item">
                        <iframe width="100%" height="315"
                            src="https://www.youtube.com/embed/FDFkl_vutoM?si=D9nWihItCQGu50Ze" title="YouTube video player"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>

                    {{-- <blockquote class="twitter-tweet">
                        <p lang="in" dir="ltr">Penyesuaian kembali jadwal Seleksi untuk PPPK Tahap II.
                            <br><br>Yukk lengkapi persyaratanya dan segera selesaikan pendaftaranmu.<a
                                href="https://twitter.com/hashtag/PejuangNIP2025?src=hash&amp;ref_src=twsrc%5Etfw">#PejuangNIP2025</a><a
                                href="https://twitter.com/hashtag/SatuDataASN?src=hash&amp;ref_src=twsrc%5Etfw">#SatuDataASN</a><a
                                href="https://twitter.com/hashtag/ASNNetral?src=hash&amp;ref_src=twsrc%5Etfw">#ASNNetral</a>
                            <a href="https://t.co/HQmMMTMz7I">pic.twitter.com/HQmMMTMz7I</a>
                        </p>&mdash; #SatuDataASN
                        (@BKNgoid) <a
                            href="https://twitter.com/BKNgoid/status/1876247086745936255?ref_src=twsrc%5Etfw">January 6,
                            2025</a>
                    </blockquote>
                    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script> --}}


                    <!-- Recent Posts Widget 2 -->
                    <div class="recent-posts-widget-2 widget-item">


                        <h3 class="widget-title">Berita Terbaru</h3>

                        @foreach ($news as $item)
                            <div class="post-item">
                                <h4><a href="../detail-post/{{ $item->slug }}">{!! strip_tags(Str::words($item->title, 6, '...')) !!}</a></h4>
                                <time
                                    datetime="2020-01-01">{{ $item->created_at->locale('id')->translatedFormat('d F Y') }}</time>
                            </div>
                        @endforeach

                    </div>
                    <!--/Recent Posts Widget 2 -->

                    <!-- Tags Widget -->
                    <div class="tags-widget widget-item">

                        <h3 class="widget-title">Tags</h3>
                        <ul>
                            <li><a href="#">ASNPelayanPublik</a></li>
                            <li><a href="#">ASNBelajarMandiri</a></li>
                            <li><a href="#">PendataanNonASN</a></li>
                            <li><a href="#">SatuDataASN</a></li>
                            <li><a href="#">AgenPerubahan</a></li>
                            <li><a href="#">ReformasiBKN</a></li>
                            <li><a href="#">ReformasiBirokrasiBKN</a></li>
                        </ul>

                    </div><!--/Tags Widget -->

                </div>

            </div>

        </div>
    </div>
@endsection
