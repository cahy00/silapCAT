@extends('konsultasi.layouts.app')

@section('title')
HELPDESK SSCASN
@endsection

@section('content')
<div class="breadcrumbs d-flex align-items-center" style="background-image: url('{{asset('assets/img/contact-header.jpg')}}');">
	<div class="container position-relative d-flex flex-column align-items-center">

		<h2>HELPDESK SSCASN</h2>
		
		<ol>
			<li></li>
		</ol>

	</div>
</div>



<section id="blog" class="blog">
	<div class="container" data-aos="fade-up">
		

		<div class="row g-5">

			<div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">

				<article class="blog-details">
					<h2 class="title">Kumpulan Konsultasi</h2>

					@foreach ($data as $item)

					<div class="post-author d-flex ">
						{{-- <h4 class="comments-count">8 Comments</h4> --}}
						<div class="comments">
							
							<div id="comment-1" class="comment">
								<div class="d-flex">
									<div class="comment-img"><img src="{{asset('assets/bkn/people.png')}}" alt=""></div>
									<div>
										<h4><a href="" style="color: black">{{$item->name}}</a> - {{$item->city->name}}</h4>
										<time>
											{{$item->question_category->name}}, 
											{{$item->created_at->format('d M Y')}},  {{$item->created_at->format('H:i')}}
										</time>
										<p>
											{!!$item->pesan!!}
										</p>
									</div>
								</div>
							</div>

							<div id="comment-reply-1" class="comment comment-reply">
								<div class="d-flex">
									<div class="comment-img"><img src="assets/bkn/team.png" alt=""></div>
									<div>
										<h5><a href="">Admin Helpdesk</a> <a href="#" class="reply"></a></h5>
										<time datetime="2020-01-01"></time>
										<p style="justify-content: center">
											{!!$item->jawaban!!}
										</p>
									</div>
								</div>
							</div>
						</div>	
						
					</div>
					
					@endforeach
					<br>
					{{$data->links()}}
				</article>
				<!-- End blog comments -->

			</div>

			

			<div class="col-lg-4" data-aos="fade-up" data-aos-delay="400">

				<div class="sidebar ps-lg-4">

					<img src="{{asset('assets/bkn/logo_bkn.png')}}" alt="" class="img-thumbnail">
					<br>
					<br>
					<br>
					<div class="sidebar-item search-form">
						<h3 class="sidebar-title">Cari Solusi</h3>
						<form action="/all-question" method="GET" class="mt-3">
							<input type="text" placeholder="Masukkan Pertanyaan atau Nomor HP" name="q" autocomplete="off">
							<button type="submit"><i class="bi bi-search"></i></button>
						</form>
					</div><!-- End sidebar search formn-->

					<div class="sidebar-item categories">
						<h3 class="sidebar-title">Kategori Pertanyaan</h3>
						<ul class="mt-3">
							@foreach ($category as $item)
								<li><a href="/category/{{$item->id}}">{{$item->name}} </li>
							@endforeach
							{{-- <span>{{$item->count()}}</span></a> --}}
						</ul>
					</div><!-- End sidebar categories-->

					<div class="sidebar-item categories">
						<h3 class="sidebar-title">Kab/Kota Domisili</h3>

						<ul class="mt-3">

							@foreach ($city as $item)
								<li><a href="/city/{{$item->id}}">{{$item->name}}</a></li>
							@endforeach

						</ul>

					</div><!-- End sidebar recent posts-->

					<div class="sidebar-item tags">
						<h3 class="sidebar-title">Tags</h3>
						<ul class="mt-3">
							<li><a href="#">AgenPerubahan</a></li>
							<li><a href="#">ASNBelajarMandiri</a></li>
							<li><a href="#">ASNPelayanPublik</a></li>
							<li><a href="#">CegahPelanggaranNSPK</a></li>
							<li><a href="#">CASN2024</a></li>
							<li><a href="#">BKNKiniBeda</a></li>
							<li><a href="#">SatuDataASN</a></li>
							<li><a href="#">ReformasiBKN</a></li>
							<li><a href="#">StrategiPengawasanDanPengendalian</a></li>
							<li><a href="#">PPPK2023</a></li>
							<li><a href="#">CASNMengundurkanDIRI</a></li>
						</ul>
					</div><!-- End sidebar tags-->

				</div><!-- End Blog Sidebar -->

			</div>
		</div>

	</div>
</section>

<section id="call-to-action" class="call-to-action">
	<div class="container" data-aos="fade-up">
		<div class="row justify-content-center">
			<div class="col-lg-6 text-center">
				<h3>KONSULTASI VIA WHATSAPP</h3>
				<p>Melangkah ke dunia konsultasi virtual, menjembatani jarak dengan bijak. Kini, solusi hadir di layar, pertanyaan dijawab tanpa batas ruang dan waktu. Bersama-sama kita temukan pemahaman baru, satu konsultasi virtual pada satu kesempatan</p>
				<a class="cta-btn" href="#">Ajukan</a>
			</div>
		</div>

	</div>
</section>


@endsection