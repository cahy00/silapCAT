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
					<form action="/tilok/store" method="POST" enctype="multipart/form-data">
						@csrf()
						<div class="card-body">
							<div class="row">
								<div class="col-4">
									<div class="form-group">
										<label for="name">Nama Event</label>
										<select name="event_id" id="" class="form-control form-select">
											@foreach ($event as $item)
												<option value="{{$item->id}}">{{$item->name}}</option>
											@endforeach
										</select>
										<span class="error invalid-feedback">{{$errors->first('name')}}</span>
									</div>
								</div>
								<div class="col-4">
									<div class="form-group">
										<label for="name">Nama Tilok</label>
										<input
											type="text"
											class="form-control"
											id="name"
											name="name"
											placeholder="Nama Event"
											required
										/>
										<span class="error invalid-feedback">{{$errors->first('name')}}</span>
									</div>
								</div>
								<div class="col-4">
									<div class="form-group">
										<label for="name">Alamat</label>
										<select name="address" id="" class="form-control select2bs3">
											<option value="manokwari">Kab Manokwari</option>
										</select>
										<span class="error invalid-feedback">{{$errors->first('name')}}</span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-6">
									<div class="form-group">
										<label>Tanggal Mulai</label>
											<div class="input-group date" id="start_date_event" data-target-input="nearest">
													<input type="text" name="start_date" class="form-control datetimepicker-input" data-target="#start_date_event"
													placeholder="Tanggal"
													required/>
													<div class="input-group-append" data-target="#start_date_event" data-toggle="datetimepicker">
															<div class="input-group-text"><i class="fa fa-calendar"></i></div>
													</div>
											</div>
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label>Tanggal Selesai</label>
											<div class="input-group date" id="reservationdate" data-target-input="nearest">
													<input type="text" name="end_date" class="form-control datetimepicker-input" data-target="#reservationdate"
													placeholder="Tanggal"
													required/>
													<div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
															<div class="input-group-text"><i class="fa fa-calendar"></i></div>
													</div>
											</div>
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
							<tbody>
								@foreach ($eventTilok as $no => $item)
								<tr>
									<td>{{$no+1}}</td>
									<td>{{$item->event->name}}</td>
									<td>{{$item->tilok->name}}</td>
									<td>{{$item->tilok->start_date}}</td>
									<td>{{$item->tilok->end_date}}</td>
									<td>
										<a href="/report/create/{{$item->id}}" class="text-center btn btn-primary"><i class="fa fa-eye" aria-hidden="true"></i></a>
										{{-- <a href="/document/inka/show/{{encrypt($item->id)}}" class="text-center btn btn-primary"><i class="fa fa-eye" aria-hidden="true"></i></a> --}}
										@if (Auth::user()->role == 'admin')
										<a href="/event/edit/{{encrypt($item->id)}}" class="btn btn-success"><i class="fa fa-pencil-alt" aria-hidden="true"></i></a>
										<a href="/event/destroy/{{encrypt($item->id)}}" class="text-center btn btn-danger" onclick="return confirm('Delete this data??')"><i class="fa fa-trash" aria-hidden="true"></i></a>
										@endif
									</td>
								</tr>
								@endforeach
							</tbody>
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