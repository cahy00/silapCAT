@extends('layouts._layout')


@section('content')
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1>Tipe Dokumen</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active">Tipe Dokumen</li>
				</ol>
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>

<section class="content">
	<div class="container-fluid">
		<div class="row">
			<!-- left column -->
			<div class="col-md-6">
				@if (session('status'))
							<div class="alert alert-success alert-dismissible">
								<button class="close" data-dismiss="alert" area-hidden="true" type="button">x</button>
								{{session('status')}}
							</div>
					@endif
				<!-- general form elements -->
				<div class="card card-primary">
					<div class="card-header">
						<h3 class="card-title">Tambah Tipe Dokumen</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form action="/type" method="POST">
						@csrf()
						{{-- <input type="text" name="user_id" hidden> --}}
						<div class="card-body">
							<div class="form-group">
								<label for="exampleInputEmail1">Tipe Dokumen</label>
								<input
									type="text"
									class="form-control"
									id="exampleInputEmail1"
									name="title"
									placeholder="Masukkan Tipe Dokumen"
									required
								/>
								<span class="error invalid-feedback">{{$errors->first('title')}}</span>
							</div>
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
						<h3 class="card-title">Data Tipe Dokumen</h3>
					</div>

					<div class="card-body">
						<table id="example1" class="table table-bordered table-striped">
							<thead>
							<tr>
								<th>NO</th>
								<th>JENIS DOKUMEN</th>
								<th>AKSI</th>
							</tr>
							</thead>
							<tbody>
								@foreach ($data as $no => $data)
									<tr>
										<td>{{ $no+1 }}</td>
										<td style="text-transform: uppercase">{{$data->title}}</td>
										<td>X</td>
									</tr>
								@endforeach
							</tbody>
							<tfoot>
							<tr>
								<th>NO</th>
								<th>JENIS DOKUMEN</th>
								<th>AKSI</th>
							</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection