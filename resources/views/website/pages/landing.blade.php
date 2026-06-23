@extends('website.index')

@section('content')
    <section id="hero" class="hero section dark-background">

        <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

            @foreach ($banner as $banner)
                <div class="carousel-item active">
                    <img src="uploads/{{ $banner->file }}" alt="" class="img-fluid">
                    {{-- <div class="carousel-container">
				<h2>{{$banner->name}}</h2>
				<p>{{$banner->desc}}</p>
			</div> --}}

                </div>
            @endforeach

            <a class="carousel-control-prev" href="#hero-carousel" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
            </a>

            <a class="carousel-control-next" href="#hero-carousel" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
            </a>

            <ol class="carousel-indicators"></ol>

        </div>

    </section>

    <section id="clients" class="clients section">

        <div data-aos="fade-up" data-aos-delay="100">

        <div class="clients-slider swiper init-swiper">
            <script type="application/json" class="swiper-config">
            {
                "loop": true,
                "speed": 6000,
                "autoplay": {
                "delay": 1,
                "disableOnInteraction": false
                },
                "centeredSlides": true,
                "slideToClickedSlide": true,
                "slidesPerView": "auto",
                "spaceBetween": 40,
                "breakpoints": {
                "320": {
                    "slidesPerView": 2,
                    "spaceBetween": 20
                },
                "640": {
                    "slidesPerView": 3,
                    "spaceBetween": 20
                },
                "992": {
                    "slidesPerView": 4,
                    "spaceBetween": 30
                },
                "1200": {
                    "slidesPerView": 6,
                    "spaceBetween": 40
                }
                }
            }
            </script>

            <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="client-logo">
                <img src="assets1/img/clients/client-1.png" class="img-fluid" alt="">
                </div>
            </div><!-- End Client Item -->

            <div class="swiper-slide">
                <div class="client-logo">
                <img src="assets1/img/clients/client-2.png" class="img-fluid" alt="">
                </div>
            </div><!-- End Client Item -->

            <div class="swiper-slide">
                <div class="client-logo">
                <img src="assets1/img/clients/client-3.png" class="img-fluid" alt="">
                </div>
            </div><!-- End Client Item -->

            <div class="swiper-slide">
                <div class="client-logo">
                <img src="assets1/img/clients/client-4.png" class="img-fluid" alt="">
                </div>
            </div>

            <div class="swiper-slide">
                <div class="client-logo">
                <img src="assets1/img/clients/client-5.webp" class="img-fluid" alt="">
                </div>
            </div>

            <div class="swiper-slide">
                <div class="client-logo">
                <img src="assets1/img/clients/client-6.png" class="img-fluid" alt="">
                </div>
            </div>

            <div class="swiper-slide">
                <div class="client-logo">
                <img src="assets1/img/clients/client-7.png" class="img-fluid" alt="">
                </div>
            </div>

            <div class="swiper-slide">
                <div class="client-logo">
                <img src="assets1/img/clients/client-8.png" class="img-fluid" alt="">
                </div>
            </div>

            <div class="swiper-slide">
                <div class="client-logo">
                <img src="assets1/img/clients/client-9.png" class="img-fluid" alt="">
                </div>
            </div>

            <div class="swiper-slide">
                <div class="client-logo">
                <img src="assets1/img/clients/client-10.png" class="img-fluid" alt="">
                </div>
            </div>

            </div>

        </div>

        </div>

    </section>

    <section id="features" class="features section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Informasi Kepegawaian</h2>
            <p>Informasi Kepegawaian Kanreg XIV BKN</p>
        </div><!-- End Section Title -->

        <div class="container">

            <div class="d-flex justify-content-center">

                <ul class="nav nav-tabs" data-aos="fade-up" data-aos-delay="100">

                    <li class="nav-item">
                        <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#features-tab-1">
                            <h4>Agenda & Kegiatan</h4>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-2">
                            <h4>Pengumuman</h4>
                        </a><!-- End tab nav item -->

                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-3">
                            <h4>FAQ</h4>
                        </a>
                    </li><!-- End tab nav item -->

                </ul>

            </div>

            <div class="tab-content" data-aos="fade-up" data-aos-delay="200">

                <div class="tab-pane fade recent-posts active show" id="features-tab-1">
                    <div class="row">
                        {{-- event item --}}
                        @foreach ($events as $event)
                            <div class="col-xl-4 col-md-6">
                                <div class="post-item position-relative h-100 d-flex flex-column" data-aos="fade-up"
                                    data-aos-delay="100">

                                    <div class="post-img position-relative overflow-hidden">
                                        <img src="{{ $event['poster'] }}" class="img-fluid" alt=""
                                            style="min-height: 240px; max-height:240px; min-width:450px; max-width:450px; object-fit: cover; object-position:top;">
                                            <span class="post-date">{{ \Carbon\Carbon::parse($event['tanggal_pelaksanaan'])->locale('id')->translatedFormat('d F Y') }}</span>
                                    </div>

                                    <div class="post-content d-flex flex-column flex-grow-1">
                                        <h3 class="post-title">{{ $event['judul'] }}</h3>

                                        {{-- <ul class="list-unstyled mb-3">
                                            <li><i class="bi bi-calendar-event me-2 text-danger"></i>
                                                <span class="opacity-50">{{ \Carbon\Carbon::parse($event['tanggal_pelaksanaan'])->timezone('Asia/Jayapura')->locale('id')->translatedFormat('d F Y, H:i') }}
                                                    WIT</span>
                                            </li>
                                            <li><i class="bi bi-geo-alt me-2 text-success"></i>
                                                <span class="opacity-50">
                                                    {!! !empty($event['zoomlink']) && $event['zoomlink'] !== '-' ? 'Zoom Meeting' : '-' !!}
                                                </span>
                                            </li>
                                        </ul> --}}
                                        <div class="mt-auto">
                                            <hr>
                                            <a href="{{ env('API_WEBINAR_URL') . '/agenda/' . $event['slug'] }}"
                                                class="cta-event" target="_blank">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Detail Event
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach

                    </div>
                </div><!-- End tab content item -->

                <div class="tab-pane fade " id="features-tab-2">
                    <div class="row">
                        <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0 d-flex flex-column justify-content-center">
                            <h3>Pengumuman</h3>
                            <h5 class="fst-italic">Pengumuman Penting! Jangan lewatkan informasi terbaru ini. Pastikan
                                untuk
                                menyimak dan mengikuti arahan selengkapnya.</h5>
                            <ul>
                                @foreach ($announcement as $item)
                                    <li><i class="bi bi-check2-all"> <span><a
                                                    href="/detail-announcement/{{ encrypt($item->id) }}"
                                                    style="color: black">{{ $item->title }}</a></span></i> </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-lg-6 order-1 order-lg-2 text-center carousel">
                            <img src="{{ 'bkn/announce.png' }}" alt="" class="img-fluid">
                        </div>
                    </div>
                </div><!-- End tab content item -->

                <div class="tab-pane fade" id="features-tab-3">
                    <div class="row">
                        <section id="faq" class="faq section">

                            <div class="container">

                                <div class="row gy-4">

                                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                                    <div class="content px-xl-5">
                                    <h3><span>Frequently Asked </span><strong>Questions</strong></h3>
                                    <p>
                                        Punya pertanyaan? Kami sudah rangkum jawaban untuk pertanyaan yang sering diajukan di sini!
                                    </p>
                                    </div>
                                </div>

                                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">

                                    <div class="faq-container">
                                    @foreach ($faq as $item)
                                        
                                        <div class="faq-item faq-active">
                                            <h3><span class="num">{{$loop->iteration}}</span> <span>{{$item->question}}</span></h3>
                                            <div class="faq-content">
                                            <p>{!! strip_tags(Str::words($item->answer, 10, '...')) !!}</p>
                                            </div>
                                            <i class="faq-toggle bi bi-chevron-right"></i>
                                        </div>

                                    @endforeach
                                    
                                    </div>

                                </div>
                                </div>

                            </div>

                        </section>
                    </div>
                </div><!-- End tab content item -->

            </div>

        </div>

    </section>

    

    <section id="about" class="about section">

        <div class="content">
            <div class="container">
                <div class="row">
                    @foreach ($headline as $item)
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <img src="uploads/{{ $item->thumbnail }}" alt="Image " class="img-fluid img-overlap rounded-4"
                                data-aos="zoom-out" style="height: auto; max-height:auto; min-width:100%;max-width:100%">
                        </div>
                        <div class="col-lg-5 ml-auto" data-aos="fade-up" data-aos-delay="100">
                            <h3 class="content-subtitle text-white opacity-50">Headline</h3>
                            <h2 class="content-title mb-4">{{ $item->title }}</h2>
                            <p class="opacity-50 mb-5">{!! strip_tags(Str::words($item->content, 50, '...')) !!}</p>

                            <p><a href="detail-post/{{ $item->slug }}" class="btn-cta">Read More</a></p>

                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </section>

    <section id="recent-posts" class="recent-posts section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Berita Kepegawaian</h2>
            <p>Berita Kepegawaian Kantor Regioanl XIV BKN</p>
        </div>
        <!-- End Section Title -->

        <div class="container">

            <div class="row gy-5">

                @foreach ($news as $item)
                    <div class="col-xl-4 col-md-6">
                        <div class="post-item position-relative h-100" data-aos="fade-up" data-aos-delay="100">

                            <div class="post-img position-relative overflow-hidden">
                                <img src="uploads/{{ $item->thumbnail }}" class="img-fluid" alt=""
                                    style="min-height: 300px; max-height:300px; min-width:450px; max-width:450px; object-fit: cover">
                                <span class="post-date">{{ $item->created_at->locale('id')->translatedFormat('d F Y') }}</span>
                            </div>

                            <div class="post-content d-flex flex-column">
                                <div class="meta d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person"></i> <span class="ps-2">Admin Web</span>
                                    </div>
                                    <span class="px-3 text-black-50">/</span>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-folder2"></i> <span class="ps-2">Kepegawaian</span>
                                    </div>
                                </div>
                                <br>
                                <h3 class="post-title">{!! strip_tags(Str::words($item->title, 6, '...')) !!}</h3>
                                <p class="opacity-50">{!! strip_tags(Str::words($item->content, 10, '...')) !!}</p>


                                <hr>

                                <a href="detail-post/{{ $item->slug }}" class="readmore stretched-link"><span>Read
                                        More</span><i class="bi bi-arrow-right"></i></a>

                            </div>

                        </div>
                    </div>
                @endforeach




            </div>

        </div>

    </section>

    <section id="services-2" class="services-2 section">
        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Artikel Kepegawaian</h2>
            <p>Artikel kepegawaian Kanreg XIV BKN</p>
        </div><!-- End Section Title -->

        <div class="services-carousel-wrap">
            <div class="container">
                <div class="swiper init-swiper">
                    <script type="application/json" class="swiper-config">
					{
						"loop": true,
						"speed": 600,
						"autoplay": {
							"delay": 5000
						},
						"slidesPerView": "auto",
						"pagination": {
							"el": ".swiper-pagination",
							"type": "bullets",
							"clickable": true
						},
						"navigation": {
							"nextEl": ".js-custom-next",
							"prevEl": ".js-custom-prev"
						},
						"breakpoints": {
							"320": {
								"slidesPerView": 1,
								"spaceBetween": 40
							},
							"1200": {
								"slidesPerView": 3,
								"spaceBetween": 40
							}
						}
					}
				</script>
                    <button class="navigation-prev js-custom-prev">
                        <i class="bi bi-arrow-left-short"></i>
                    </button>
                    <button class="navigation-next js-custom-next">
                        <i class="bi bi-arrow-right-short"></i>
                    </button>
                    <div class="swiper-wrapper">
                        @foreach ($article as $item)
                            <div class="swiper-slide">
                                <div class="service-item">
                                    <div class="service-item-contents">
                                        <a href="detail-post/{{ $item->slug }}">
                                            <span class="service-item-category">Artikel Kepegawaian</span>
                                            <h2 class="service-item-title">{{ $item->title }}</h2>
                                        </a>
                                    </div>
                                    <img src="uploads/{{ $item->thumbnail }}" alt="Image" class="img-fluid"
                                        style="min-height: 450px; max-height:450px; min-width:450px; max-width:450px; object-fit: cover">
                                </div>
                            </div>
                        @endforeach

                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </section>

    <section id="call-to-action" class="call-to-action">
        <div class="container" data-aos="fade-up">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <h3>SURVEY KEPUASAN MASYARAKAT</h3>
                    <p>Melangkah ke dunia konsultasi virtual, menjembatani jarak dengan bijak. Kini, solusi hadir di layar,
                        pertanyaan dijawab tanpa batas ruang dan waktu. Bersama-sama kita temukan pemahaman baru, satu
                        konsultasi virtual pada satu kesempatan</p>
                    <a class="cta-btn" href="https://skm.bkn14.com/" target="_blank">Isi Survey</a>
                </div>
            </div>
        </div>
    </section>
@endsection
