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

            <div>

                @yield('detail-content')

            </div>

        </div>
    </div>
@endsection
