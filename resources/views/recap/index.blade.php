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
				<div class="row">
					<div class="col-lg-2 col-6">
						<!-- small box -->
						<div class="small-box bg-olive">
							<div class="inner">
								<h3>{{$jumlah}}</h3>
			
								<p>Jumlah Peserta</p>
							</div>
							<div class="icon">
								<i class="ion ion-bag"></i>
							</div>
						</div>
					</div>
					<div class="col-lg-2 col-6">
						<!-- small box -->
						<div class="small-box bg-lime">
							<div class="inner">
								<h3>{{$hadir_tilok}}</h3>
			
								<p>Peserta Hadir</p>
							</div>
							<div class="icon">
								<i class="ion ion-stats-bars"></i>
							</div>
						</div>
					</div>
					<div class="col-lg-2 col-6">
						<!-- small box -->
						<div class="small-box bg-warning">
							<div class="inner">
								<h3>{{$tidak_hadir_tilok}}</h3>
			
								<p>Peserta Tidak Hadir</p>
							</div>
							<div class="icon">
								<i class="ion ion-person-add"></i>
							</div>
						</div>
					</div>
					<div class="col-lg-2 col-6">
						<!-- small box -->
						<div class="small-box bg-maroon">
							<div class="inner">
								<h3>{{$tertinggi_tilok}}</h3>
			
								<p>Nilai Tertinggi</p>
							</div>
							<div class="icon">
								<i class="ion ion-pie-graph"></i>
							</div>
						</div>
					</div>
					<div class="col-lg-2 col-6">
						<!-- small box -->
						<div class="small-box bg-navy">
							<div class="inner">
								<h3>{{$terendah_tilok}}</h3>
			
								<p>Nilai Terendah</p>
							</div>
							<div class="icon">
								<i class="ion ion-pie-graph"></i>
							</div>
						</div>
					</div>
					<div class="col-lg-2 col-6">
						<!-- small box -->
						<div class="small-box bg-indigo">
							<div class="inner">
								<h3>{{$sesi}}</h3>
			
								<p>Sesi Keseluruhan</p>
							</div>
							<div class="icon">
								<i class="ion ion-pie-graph"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Data {{$title}}</h3>
					</div>
					<!-- /.card-header -->
					<div class="card-body">
						<form action="" method="GET">
							<div class="row">
									<div class="col-md-3">
											<select name="event_id" id="event_id" class="form-control">
													<option value="">Semua Event</option>
													@foreach($event as $event)
															<option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
																	{{ $event->name }}
															</option>
													@endforeach
											</select>
									</div>
									<div class="col-md-3">
										<select name="tilok_id" id="tilok_id" class="form-control">
												<option value="">Semua Tilok</option>
												@foreach($tilok as $tilok)
														<option value="{{ $tilok->id }}" {{ request('tilok_id') == $tilok->id ? 'selected' : '' }}>
																{{ $tilok->name }}
														</option>
												@endforeach
										</select>
									</div>
									<div class="col-md-3">
										<input
											type="date"
											name="from_date"
											class="form-control"
											value="{{ request('from_date') }}"
											{{-- onchange="this.form.submit()" --}}
										>
									</div>
									<div class="col-md-3">
										<input
											type="date"
											name="to_date"
											class="form-control"
											value="{{ request('to_date') }}"
											{{-- onchange="this.form.submit()" --}}
										>
									</div>
									
							</div>
							<div class="row">
								<div class="col-md-2 d-flex align-items-end">
									<button type="submit" class="btn btn-primary">Filter</button>
							</div>
							</div>
						</form>
						<br>
						<table id="example1" class="table table-bordered table-striped">
							<thead>
							<tr>
								<th>NO</th>
								<th>NAMA EVENT</th>
								<th>TITIK LOKASI</th>
								<th>INSTANSI</th>
								<th>PELAKSANAAN</th>
								<th>JUMLAH PESERTA</th>
								<th>PESERTA HADIR</th>
								<th>PESERTA TIDAK HADIR</th>
								<th>NILAI TERTINGGI</th>
								<th>NILAI TERENDAH</th>
								<th>DOKUMEN</th>
								<th>KELENGKAPAN DOKUMEN</th>
							</tr>
							</thead>
							<tbody>
								@foreach ($reports as $no => $item)
								<tr>
									<td>{{$no+1}}</td>
									<td>{{$item->event->name}}</td>
									<td>{{$item->tilok->name}}</td>
									<td>{{$item->instansi_list}}</td>
									<td>{{ \Carbon\Carbon::parse($item->tilok->start_date)->format('d-m-Y') }}
										s/d
										{{ \Carbon\Carbon::parse($item->tilok->end_date)->format('d-m-Y') }}</td>
									<td>{{$item->total_participant}}</td>
									<td>{{$item->total_present}}</td>
									<td>{{$item->total_absent}}</td>
									<td>{{$item->highest_score}}</td>
									<td>{{$item->lowest_score}}</td>
									<td>
										<ol>
											@foreach ($item->tilok->documents as $doc)
												<li><a target="_blank" href="{{asset('public/uploads/'. $doc->document_path)}}" class="small-box-footer"> {{$doc->document_name}} </a></li>
												{{-- <td></td> --}}
										@endforeach
										</ol>
									</td>
									<td>
										<input type="checkbox" {{ $item->is_complete ? 'checked' : '' }} disabled>
										{{ $item->is_complete ? 'Lengkap' : 'Belum Lengkap' }}
								</td>
									{{-- <td>
										<a href="/document/inka/show/{{encrypt($item->id)}}" class="text-center btn btn-primary"><i class="fa fa-eye" aria-hidden="true"></i></a>
										@if (Auth::user()->role == 'admin')
										<a href="/event/edit/{{encrypt($item->id)}}" class="btn btn-success"><i class="fa fa-pencil-alt" aria-hidden="true"></i></a>
										<a href="/event/destroy/{{encrypt($item->id)}}" class="text-center btn btn-danger" onclick="return confirm('Delete this data??')"><i class="fa fa-trash" aria-hidden="true"></i></a>
										@endif
									</td> --}}
								</tr>
								@endforeach
							</tbody>
							<tfoot>
							<tr>
								<th>NO</th>
								<th>NAMA EVENT</th>
								<th>TITIK LOKASI</th>
								<th>INSTANSI</th>
								<th>PELAKSANAAN</th>
								<th>JUMLAH PESERTA</th>
								<th>PESERTA HADIR</th>
								<th>PESERTA TIDAK HADIR</th>
								<th>NILAI TERTINGGI</th>
								<th>NILAI TERENDAH</th>
								<th>DOKUMEN</th>
								<th>KELENGKAPAN DOKUMEN</th>
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