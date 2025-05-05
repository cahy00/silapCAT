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

						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
								<i class="fas fa-minus"></i>
							</button>
						</div>
					</div>
					{{-- @if (session('status'))
							<div class="alert alert-success alert-dismissible">
								<button class="close" data-dismiss="alert" area-hidden="true" type="button">x</button>
								{{session('status')}}
							</div>
					@endif --}}
					<!-- /.card-header -->
					<!-- form start -->
					<form action="/detail-event/store" method="POST" enctype="multipart/form-data">
						@csrf()
						<div class="card-body">
							<div class="row">
								<div class="col-4">
									<div class="form-select">
										<label for="exampleInputEmail1">Event</label>
										<select name="event_id" id="" class="form-control" style="text-transform: uppercase;">
											@foreach ($event as $item)
												<option value="{{$item->id}}" style="text-transform: uppercase;">{{$item->name}} Tahun {{$item->year}}</option>
											@endforeach
										</select>
										@error('event_id')
											<div class="invalid-feedback">
												<strong>{{$message}}</strong>
											</div>
										@enderror
									</div>
								</div>
								<div class="col-4">
									<div class="form-group">
										<label for="tilok">Titik Lokasi</label>
										<input
											type="text"
											class="form-control"
											id="tilok"
											name="tilok"
											placeholder="Titik Lokasi"
											required
										/>
										<span class="error invalid-feedback">{{$errors->first('tilok')}}</span>
									</div>
								</div>
								{{-- <div class="col-4">
									<div class="form-group">
										<label for="jadwal">Jadwal Kegiatan</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="far fa-calendar-alt"></i>
												</span>
											</div>
											<input type="text" class="form-control float-right" id="reservation" name="jadwal">
										</div>
										<span class="error invalid-feedback">{{$errors->first('jadwal')}}</span>
									</div>
								</div> --}}
							</div>

							{{-- <div class="row">
								<div class="col-2">
									<div class="form-group">
										<label for="all_session">Keseluruhan Sesi</label>
										<input
											type="text"
											class="form-control"
											id="all_session"
											name="all_session"
											placeholder="Keseluruhan Sesi"
											required
										/>
										<span class="error invalid-feedback">{{$errors->first('all_session')}}</span>
									</div>
								</div>
								<div class="col-2">
									<div class="form-group">
										<label for="all_participant">Keseluruhan Peserta</label>
										<input
											type="text"
											class="form-control"
											id="all_participant"
											name="all_participant"
											placeholder="Keseluruhan Peserta"
											required
										/>
										<span class="error invalid-feedback">{{$errors->first('all_participant')}}</span>
									</div>
								</div>
							</div> --}}

							{{-- <div class="row">
								<div class="col-4">
									<div class="form-group">
										<label for="dok_survey">Dokumen Survey Tilok</label>
										<input
											type="file"
											class="form-control"
											id="dok_survey"
											name="dok_survey"
											required
										/>
										<span class="error invalid-feedback">{{$errors->first('dok_survey')}}</span>
									</div>
								</div>
								<div class="col-4">
									<div class="form-group">
										<label for="dok_sktim">SK TIM</label>
										<input
											type="file"
											class="form-control"
											id="dok_sktim"
											name="dok_sktim"
											required
										/>
										<span class="error invalid-feedback">{{$errors->first('dok_sktim')}}</span>
									</div>
								</div>
								<div class="col-4">
									<div class="form-group">
										<label for="dok_laporan">Dokumen Laporan</label>
										<input
											type="file"
											class="form-control"
											id="dok_laporan"
											name="dok_laporan"
											required
										/>
										<span class="error invalid-feedback">{{$errors->first('dok_laporan')}}</span>
									</div>
								</div>
							</div> --}}
							
							<button type="submit" class="btn btn-primary">
								Submit
							</button>
						</div>
					</form>
				</div>
			</div>
			<div class="col-12">
				<div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{$tilok}}</h3>

                <p>Titik Lokasi Kegiatan</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>0</h3>

                <p>Keseluruhan Peserta</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>44</h3>

                <p>User Registrations</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>65</h3>

                <p>Unique Visitors</p>
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
						<table id="example1" class="table table-bordered table-striped">
							<thead>
							<tr>
								<th>NO</th>
								<th>NAMA EVENT</th>
								<th>TILOK</th>
								<th>PELAKSANAAN</th>
								<th>AKSI</th>
							</tr>
							</thead>
							<tbody>
								@foreach ($detail_event as $no => $item)
								<tr>
									<td>{{$no+1}}</td>
									<td>{{$item->event->name}} Tahun {{$item->event->year}}</td>
									<td>{{$item->tilok}}</td>
									<td>{{$item->jadwal}}</td>
									<td>
										<a href="/detail-event/show/{{encrypt($item->id)}}" class="text-center btn btn-primary"><i class="fa fa-eye" aria-hidden="true"></i></a>
										@if (Auth::user()->role == 'admin')
										<a href="/event/edit/{{encrypt($item->id)}}" class="btn btn-success"><i class="fa fa-pencil-alt" aria-hidden="true"></i></a>
										{{-- <a href="/event/destroy/{{encrypt($item->id)}}" class="text-center btn btn-danger" onclick="return confirm('Delete this data??')"><i class="fa fa-trash" aria-hidden="true">DELETE</i></a> --}}
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
								<th>PELAKSANAAN</th>
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