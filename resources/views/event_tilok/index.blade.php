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
								<th>TILOK</th>
								<th>TGL MULAI</th>
								<th>TGL SELESAI</th>
								<th>AKSI</th>
							</tr>
							</thead>
							<tbody>
								@foreach ($event_tilok as $no => $item)
								<tr>
									<td>{{$no+1}}</td>
									<td>{{$item->event->name}} Tahun {{$item->event->year}}</td>
									<td>{{$item->tilok->name}}</td>
									<td>{{$item->tilok->start_date}}</td>
									<td>{{$item->tilok->end_date}}</td>
									{{-- <td>{{$item->tilok}}</td>
									<td>{{$item->jadwal}}</td> --}}
									<td>
										@if (Auth::user()->hasRole('operator'))
										<a href="/report-operator/create/{{$item->id}}" class="text-center btn btn-primary"><i class="fa fa-eye" aria-hidden="true"></i></a>
										@endif
										@if (Auth::user()->hasRole('admin'))
										<a href="/report/create/{{$item->id}}" class="text-center btn btn-primary"><i class="fa fa-eye" aria-hidden="true"></i></a>
										<a href="/event/edit/{{encrypt($item->id)}}" class="btn btn-success"><i class="fa fa-pencil-alt" aria-hidden="true"></i></a>
										@endif
									</td>
								</tr>
								@endforeach
							</tbody>
							<tfoot>
							<tr>
								<th>NO</th>
								<th>NAMA EVENT</th>
								<th>TILOK</th>
								<th>TGL MULAI</th>
								<th>TGL SELESAI</th>
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