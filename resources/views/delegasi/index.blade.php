@extends('layouts._layout')

@section('content')
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1>{{$title}}</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active">{{$title}}</li>
				</ol>
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card card-primary">
					<div class="card-header">
						<h3 class="card-title">Form {{$title}}</h3>
					</div>
					{{-- @if (session('status'))
							<div class="alert alert-success alert-dismissible">
								<button class="close" data-dismiss="alert" area-hidden="true" type="button">x</button>
								{{session('status')}}
							</div>
					@endif --}}
					<!-- /.card-header -->
					<!-- form start -->
					<form action="/delegasi/store" method="POST" enctype="multipart/form-data">
						@csrf()
						<div class="card-body">
							<div class="row">
								<div class="col-6">
									<div class="form-group">
										<label for="name">Pilih Event - Tilok</label>
										<select name="event_tilok_id" id="" class="form-control form-select">
											@foreach ($eventTilok as $item)
												<option value="{{$item->id}}">{{$item->event->name}} -- {{$item->tilok->name}}</option>
											@endforeach
										</select>
										<span class="error invalid-feedback">{{$errors->first('name')}}</span>
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label for="name">Pilih User</label>
										<select name="user_id" id="" class="form-control form-select">
											@foreach ($user as $item)
												<option value="{{$item->id}}">{{$item->name}}</option>
											@endforeach
										</select>
										<span class="error invalid-feedback">{{$errors->first('name')}}</span>
									</div>
								</div>
							</div>
							
							<button type="submit" class="btn btn-primary">
								Submit
							</button>
						</div>
					</form>
				</div>
			</div>
			<div class="col-12">
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
								<th>NAMA EVENT</th>
								<th>TITIK LOKASI</th>
								<th>TANGGAL MULAI</th>
								<th>TANGGAL SELESAI</th>
								<th>AKSI</th>
							</tr>
							</thead>
							
							<tfoot>
							<tr>
								<th>NO</th>
								<th>NAMA EVENT</th>
								<th>TITIK LOKASI</th>
								<th>TANGGAL MULAI</th>
								<th>TANGGAL SELESAI</th>
								<th>AKSI</th>
							</tr>
							</tfoot>
						</table>
					</div>
					<!-- /.card-body -->
				</div>
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->
	</div>
	<!-- /.container-fluid -->
</section>
@endsection