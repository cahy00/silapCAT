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
						</div>
						<!-- /.card-body -->

						
				</div>

			</div>
			<div class="col-md-6">
				<div class="card card-primary">
					<div class="card-header">
						<h3 class="card-title">Form Tambah Role</h3>
					</div>
					<div class="card-body">
						<div class="form-group">
							<label for="exampleInputEmail1">Role</label>
							{{-- <select name="role_name[]" id="" class="select2 form-control" multiple="multiple">
								@foreach ($role as $role)
								<option value="{{$role->name}}">{{$role->name}}</option>
								@endforeach
							</select> --}}
							<select name="role_name" id="" class="form-control form-select">
								@foreach ($role as $role)
								<option value="{{$role->name}}">{{$role->name}}</option>
								@endforeach
							</select>
						</div>
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
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Data {{$title}}</h3>
					</div>
					<!-- /.card-header -->
					<div class="card-body">
						<table id="example1" class="table table-bordered table-striped">
							<thead>
							<tr>
								<th>NO</th>
								<th>NAMA LENGKAP</th>
								<th>EMAIL</th>
								<th>ROLE</th>
								<th>AKSI</th>
							</tr>
							</thead>
							<tbody>
								@foreach ($user as $no => $item)
								<tr>
									<td>{{$no+1}}</td>
									<td>{{$item->name}}</td>
									<td>{{$item->email}}</td>
									<td>
                    @foreach ($item->roles as $role)
                        <span>{{ $role->name }}</span><br>
                    @endforeach
                </td>
									<td>
										@if (Auth::user()->hasRole('admin'))
										<a href="/user/edit/{{encrypt($item->id)}}" class="btn btn-success" data-toggle="modal" data-target="#modal-xl"><i class="fa fa-pencil-alt" aria-hidden="true"></i></a>
										<a href="/user/destroy/{{encrypt($item->id)}}" class="text-center btn btn-danger" onclick="return confirm('Delete this data??')"><i class="fa fa-trash" aria-hidden="true"></i></a>
										@endif
									</td>
								</tr>
								@endforeach
							</tbody>
							<tfoot>
							<tr>
								<th>NO</th>
								<th>NAMA LENGKAP</th>
								<th>EMAIL</th>
								<th>ROLE</th>
								<th>AKSI</th>
							</tr>
							</tfoot>
						</table>
					</div>
					<!-- /.card-body -->
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modal-xl">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Edit Data User</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="col-12">
					<div class="card card-primary">
						<div class="card-header">
							<h3 class="card-title">Form Update</h3>
						</div>
						<form action="">
							@csrf
							<div class="card-body">
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label for="exampleInputEmail1">Email</label>
											<input
												type="email"
												name="email"
												class="form-control"
												id="exampleInputEmail1"
												placeholder="Enter email"
												{{-- value="{{$userEdit->email}}" --}}
											/>
										</div>
									</div>
									<div class="col-md-3">
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
									</div>
									<div class="col-md-3">
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
									</div>
									<div class="col-md-3">
										<label for="name">Role</label>
										<select name="role_name" id="" class="form-control form-select">
											{{-- @foreach ($role as $role)
											<option value="{{$role->name}}">{{$role->name}}</option>
											@endforeach --}}
										</select>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>


@endsection