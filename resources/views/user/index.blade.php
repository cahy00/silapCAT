@extends('layouts._layout')

@section('content')
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1>User</h1>
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
			<!-- left column -->
			<div class="col-md-6">
				<!-- general form elements -->
				<div class="card card-primary">
					<div class="card-header">
						<h3 class="card-title">Form Tambah User</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form method="POST" action="/user">
						@csrf
						<div class="card-body">
							<div class="form-group">
								<label for="exampleInputEmail1">Email</label>
								<input
									type="email"
									name="email"
									class="form-control"
									id="exampleInputEmail1"
									placeholder="Enter email"
								/>
							</div>
							<div class="form-group">
								<label for="name">Nama Lengkap</label>
								<input
									type="text"
									class="form-control"
									id="name"
									name="name"
									placeholder="Nama Lengkap"
								/>
							</div>
							<div class="form-group">
								<label for="NIP">NIP</label>
								<input
									type="text"
									class="form-control"
									id="NIP"
									name="nip"
									placeholder="NIP"
								/>
							</div>
							<div class="form-group">
								<label for="exampleInputPassword1">Password</label>
								<input
									type="password"
									class="form-control"
									name="password"
									id="exampleInputPassword1"
									placeholder="Password"
								/>
							</div>
							<div class="form-select">
								<label for="role">Unit Kerja</label>
								<select name="role" id="role" class="form-control">
									<option value="inka">Bidang Informasi Kepegawaian</option>
									<option value="pdsk">Bidang Pengembangan dan Supervisi Kepegawaian</option>
									<option value="mutasi">Bidang Mutasi dan Status Kepegawaian</option>
									<option value="pensiun">Bidang Pengangkatan dan Pensiun</option>
									<option value="tu">Bagian Tata Usaha</option>
								</select>
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