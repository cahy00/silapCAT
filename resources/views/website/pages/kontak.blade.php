@extends('website.index')

@section('content')
<div class="page-title dark-background" data-aos="fade" style="background-image: url({{asset('bkn/bknbaru.jpg')}}); object-fit:cover">
	<div class="container position-relative">
		<h1>Kontak Kami</h1>
		<nav class="breadcrumbs">
			<ol>
				<li><a href="index.html">Beranda</a></li>
				<li class="current">Kontak Kami</li>
			</ol>
		</nav>
	</div>
</div><!-- End Page Title -->

<!-- Contact Section -->
<section id="contact" class="contact section">

	<div class="mb-5">
		<iframe style="width: 100%; height: 400px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.303652777045!2d134.03024969999998!3d-0.9202994000000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d53f40feef5a5a9%3A0x3a4bc53ee8d7f9de!2sKantor%20Regional%20XIV%20BKN%20Manokwari!5e0!3m2!1sid!2sid!4v1735254960124!5m2!1sid!2sid" frameborder="0" allowfullscreen=""></iframe>
	</div><!-- End Google Maps -->

	<div class="container" data-aos="fade">

		<div class="row gy-5 gx-lg-5">

			<div class="col-lg-4">

				<div class="info">
					<h3>Kantor Regional XIV BKN</h3>

					<div class="info-item d-flex">
						<i class="bi bi-geo-alt flex-shrink-0"></i>
						<div>
							<h4>Location:</h4>
							<p>Jl. Manokwari - Maruni, Anday, Katebu, Distrik Manokwari, Kabupaten Manokwari, Papua Barat</p>
							<p>Provinsi Papua Barat</p>
						</div>
					</div><!-- End Info Item -->

					<div class="info-item d-flex">
						<i class="bi bi-envelope flex-shrink-0"></i>
						<div>
							<h4>Email:</h4>
							<p>kanreg14.manokwari@bkn.go.id</p>
						</div>
					</div><!-- End Info Item -->

					<div class="info-item d-flex">
						<i class="bi bi-phone flex-shrink-0"></i>
						<div>
							<h4>Call:</h4>
							<p>+62 821 3886 8989</p>
						</div>
					</div><!-- End Info Item -->

				</div>

			</div>

			<div class="col-lg-8">
				<form action="" role="form" class="php-email-form">
					<div class="row">
						<div class="col-md-6 form-group">
							<input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required="">
						</div>
						<div class="col-md-6 form-group mt-3 mt-md-0">
							<input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required="">
						</div>
					</div>
					<div class="form-group mt-3">
						<input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required="">
					</div>
					<div class="form-group mt-3">
						<textarea class="form-control" name="message" placeholder="Message" required=""></textarea>
					</div>
					<div class="my-3">
						<div class="loading">Loading</div>
						<div class="error-message"></div>
						<div class="sent-message">Your message has been sent. Thank you!</div>
					</div>
					<div class="text-center"><button type="submit">Send Message</button></div>
				</form>
			</div><!-- End Contact Form -->

		</div>

	</div>

</section>
		
@endsection