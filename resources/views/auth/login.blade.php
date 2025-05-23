@extends('layouts.login_layout')

@section('content')
<div class="card card-outline card-primary">
	<div class="card-header text-center">
		<a href="#" class="h1"><b>SILAP</b>CAT</a>
	</div>
	<div class="card-body">
		@if (session('withErrors'))
			<p class="alert alert-danger">{{session('withErrors')}}</p>
		@endif

		<form action="" method="post">
			@csrf
			<div class="input-group mb-3">
				<input type="text" class="form-control" placeholder="NIP" name="nip" value="{{old('nip')}}" required autofocus autocomplete>
				<div class="input-group-append">
					<div class="input-group-text">
						<span class="fas fa-envelope"></span>
					</div>
				</div>
			</div>
			<div class="input-group mb-3">
				<input type="password" class="form-control" placeholder="Password" name="password" required autofocus>
				<div class="input-group-append">
					<div class="input-group-text">
						<span class="fas fa-lock"></span>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-8">
					<div class="icheck-primary">
						<input type="checkbox" id="remember">
						<label for="remember">
							Remember Me
						</label>
					</div>
				</div>
				<!-- /.col -->
				<div class="col-4">
					<button type="submit" class="btn btn-primary btn-block">Sign In</button>
				</div>
				<!-- /.col -->
			</div>
		</form>
		<!-- /.social-auth-links -->

		<p class="mb-1">
			<a href="forgot-password.html">I forgot my password</a>
		</p>
	</div>
	<!-- /.card-body -->
</div>
@endsection