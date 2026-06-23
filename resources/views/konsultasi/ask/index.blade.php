@extends('konsultasi.layouts.app')

@section('title')
KOPACE - Konsultasi Pelayanan Cepat
@endsection

@section('content')
<section id="hero" class="hero section transparent-background">

	<div class="container text-center" data-aos="fade-up" data-aos-delay="100">
		<div class="row justify-content-center">
			<div class="col-lg-8">
				<h2>Welcome to Our Website</h2>
				<p>We are still working on our website. Stay tuned for updates!</p>
			</div>
			<div class="countdown" data-count="2024/12/3">
				<span class="count-days"></span> days <span class="count-hours"></span>:<span class="count-minutes"></span>:<span class="count-seconds"></span>
			</div>

			<div class="col-lg-5 hero-newsletter">
				<p>Subscribe now to get the latest updates!</p>
				<form action="forms/newsletter.php" method="post" class="php-email-form">
					<div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Subscribe"></div>
					<div class="loading">Loading</div>
					<div class="error-message"></div>
					<div class="sent-message">Your subscription request has been sent. Thank you!</div>
				</form>
			</div>

		</div>
	</div>

</section>

<section id="contact" class="contact">
	{{-- <div class="container position-relative" data-aos="fade-up">

		<div class="row gy-4 d-flex justify-content-end">

			<div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">

				<div class="info-item d-flex">
					<i class="bi bi-geo-alt flex-shrink-0"></i>
					<div>
						<h4>Alamat Kantor:</h4>
						<p>Jl. Manokwari - Maruni, Anday, Distrik Manokwari, Kabupaten Manokwari, Papua Bar. 98315</p>
					</div>
				</div>

				<div class="info-item d-flex">
					<i class="bi bi-envelope flex-shrink-0"></i>
					<div>
						<h4>Email:</h4>
						<p>bknkanreg14@bkn.go.id</p>
					</div>
				</div>

				<div class="info-item d-flex">
					<i class="bi bi-phone flex-shrink-0"></i>
					<div>
						<h4>Kontak:</h4>
						<p>+62 821 3886 8989</p>
					</div>
				</div>

			</div>

			<div class="col-lg-6" data-aos="fade-up" data-aos-delay="250">

				<form action="/question" method="post" class="php-email-form">
					@csrf
					<div class="row">
						<div class="col-md-6 form-group">
							<input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" autocomplete="off" placeholder="NAMA LENGKAP"  required>
						</div>
						<div class="col-md-6 form-group mt-3 mt-md-0">
							<input type="text" class="form-control" name="nip" id="email" placeholder="NIP" maxlength="18" autocomplete="off" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 form-group">
							<input type="text" name="instansi" class="form-control" id="instansi" placeholder="INSTANSI" autocomplete="off">
						</div>
						<div class="col-md-6 form-group mt-3 mt-md-0">
							<select class="form-select" aria-label="Default select example" name="city_id" required>
								<option selected>KAB/KOTA DOMISILI</option>
								@foreach ($city as $item)
								<option value="{{$item->id}}">{{$item->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 form-group">
							<select class="form-select" aria-label="Default select example" name="question_category_id" required>
								<option selected>KATEGORI PERTANYAAN</option>
								@foreach ($question_category as $item)
								<option value="{{$item->id}}">{{$item->name}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-6 form-group mt-3 mt-md-0">
							<input type="text" class="form-control" name="wa" id="wa" placeholder="NO HANDPHONE(08xxx)" autocomplete="off" maxlength="13" required>
						</div>
					</div>
					<div class="form-group mt-3">
						<textarea class="form-control" name="pesan" rows="10" placeholder="DETAIL PERTANYAAN" required></textarea>
					</div>
					{!! NoCaptcha::renderJs() !!}
					{!! NoCaptcha::display() !!}
					<br>
					<button type="submit">Send Message</button>
				</form>

			</div>

		</div>

	</div> --}}
</section>

<section id="why-us" class="why-us">
	<div class="container" data-aos="fade-up">

		<div class="section-header">
			<h2 style="font: bold; font-family:Georgia, 'Times New Roman', Times, serif">Konsultasi Kepegawaian</h2>

		</div>

		<div class="row g-0" data-aos="fade-up" data-aos-delay="200">

			<div class="col-xl-5 img-bg" style="background-image: url('assets/bkn/faq.png')"></div>
			<div class="col-xl-7 slides  position-relative">

				<div class="slides-1 swiper">
					<div class="swiper-wrapper">

						@foreach ($data as $item)
						<div class="swiper-slide">
							<div class="item">
								<h3 class="mb-3">{{$item->question_category->name}}</h3>
								<h4 class="mb-3">{!!strip_tags(Str::words($item->pesan, 20, '...'))!!}</h4>
								<p>{!!strip_tags(Str::words($item->jawaban, 20, '...'))!!}</p>
							</div>
						</div>


						@endforeach


					</div>
					<div class="swiper-pagination"></div>
				</div>
				<div class="swiper-button-prev"></div>
				<div class="swiper-button-next"></div>
			</div>

		</div>

	</div>
</section>

<section id="call-to-action" class="call-to-action">
	<div class="container" data-aos="fade-up">
		<div class="row justify-content-center">
			<div class="col-lg-6 text-center">
				<h3>KONSULTASI VIA ZOOM</h3>
				<p>Melangkah ke dunia konsultasi virtual, menjembatani jarak dengan bijak. Kini, solusi hadir di layar, pertanyaan dijawab tanpa batas ruang dan waktu. Bersama-sama kita temukan pemahaman baru, satu konsultasi virtual pada satu kesempatan</p>
				<a class="cta-btn" href="#">Ajukan</a>
			</div>
		</div>

	</div>
</section>




@endsection