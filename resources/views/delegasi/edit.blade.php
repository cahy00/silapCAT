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
					<form action="/event/edit/{{encrypt($data_event->id)}}" method="POST" enctype="multipart/form-data">
						@csrf()
            @method('PUT')
						<div class="card-body">
							<div class="row">
								<div class="col-6">
									<div class="form-group">
										<label for="name">Nama Event </label>
										<input
											type="text"
											class="form-control"
											id="name"
											name="name"
											placeholder="Nama Event"
											required
                      value="{{$data_event->name}}"
										/>
										<span class="error invalid-feedback">{{$errors->first('name')}}</span>
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label for="year">Tahun Event</label>
										<input
											type="text"
											class="form-control"
											id="year"
											name="year"
											placeholder="Tahun Event"
											required
                      value="{{$data_event->year}}"
										/>
										<span class="error invalid-feedback">{{$errors->first('year')}}</span>
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
								<th>TAHUN</th>
								<th>AKSI</th>
							</tr>
							</thead>
							<tbody>
								@foreach ($data as $no => $item)
								<tr>
									<td>{{$no+1}}</td>
									<td>{{$item->name}}</td>
									<td>{{$item->year}}</td>
									<td>
										<a href="/document/inka/show/{{encrypt($item->id)}}" class="text-center"><i class="fa fa-eye" aria-hidden="true"></i></a>
										@if (Auth::user()->role == 'admin')
										<a href="/document/inka/edit/{{encrypt($item->id)}}"><i class="fa fa-fire" aria-hidden="true"></i></a>
										<a href="/event/destroy/{{encrypt($item->id)}}" class="text-center" onclick="return confirm('Delete this data??')"><i class="fa fa-trash" aria-hidden="true"></i></a>
										@endif
									</td>
								</tr>
								@endforeach
							</tbody>
							<tfoot>
							<tr>
								<th>NO</th>
								<th>NAMA EVENT</th>
								<th>TAHUN</th>
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