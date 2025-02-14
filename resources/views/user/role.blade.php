@extends('layouts._layout')

@section('content')
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1>Manajemen Role & Permission</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active">Manajemen User</li>
				</ol>
			</div>
		</div>
	</div>
</section>

<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-6">
				<!-- general form elements -->
				<div class="card card-primary">
					<div class="card-header">
						<h3 class="card-title">Form Tambah Role</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form method="POST" action="/user">
						@csrf
						<div class="card-body">
							<div class="form-group">
								<label for="exampleInputEmail1">Role</label>
								<input
									type="email"
									name="email"
									class="form-control"
									id="exampleInputEmail1"
									placeholder="Enter email"
								/>
							</div>
						</div>
						<!-- /.card-body -->

						<div class="card-footer">
							<button type="submit" class="btn btn-primary">
								Submit
							</button>
						</div>
					</form>
				</div>

			</div>
			<div class="col-md-6">
				<!-- general form elements -->
				<div class="card card-primary">
					<div class="card-header">
						<h3 class="card-title">Form Tambah Permission</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form method="POST" action="/user">
						@csrf
						<div class="card-body">
							<div class="form-group">
								<label for="exampleInputEmail1">Permission</label>
								<input
									type="email"
									name="email"
									class="form-control"
									id="exampleInputEmail1"
									placeholder="Enter email"
								/>
							</div>
						</div>
						<!-- /.card-body -->

						<div class="card-footer">
							<button type="submit" class="btn btn-primary">
								Submit
							</button>
						</div>
					</form>
				</div>

			</div>
		</div>
	</div>
</section>

@endsection