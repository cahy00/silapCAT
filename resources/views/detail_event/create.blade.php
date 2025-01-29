@extends('layouts._layout')

@section('content')
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1>{{$title}} Tilok {{$data->tilok}}</h1>
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
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">
								<button type="button" class="btn btn-dark" data-toggle="modal" data-target="#modal-xl">
                  <i class="fas fa-plus"> Input Data</i>
                </button>
								<a href="" class="btn btn-light"><i class="fas fa-plus"> Import Data</i></a>

							</h3>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal-xl">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Input Data</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
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
									<form action="/detail-tilok/store" method="POST" enctype="multipart/form-data">
										@csrf()
										<input type="hidden" name="detail_event_id" value="{{$data->id}}">
										<div class="card-body">
											<div class="row">
												<div class="col-4">
													<div class="form-group">
														<label for="tilok">Titik Lokasi</label>
														<input
															type="text"
															class="form-control"
															id="tilok"
															name="tilok"
															value="{{$data->tilok}}"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('tilok')}}</span>
													</div>
												</div>
												<div class="col-4">
													<div class="form-group">
														<label>Tanggal</label>
															<div class="input-group date" id="reservationdate" data-target-input="nearest">
																	<input type="text" name="tanggal" class="form-control datetimepicker-input" data-target="#reservationdate"
																	placeholder="Tanggal"
																	required/>
																	<div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
																			<div class="input-group-text"><i class="fa fa-calendar"></i></div>
																	</div>
															</div>
													</div>
												</div>
												<div class="col-4">
													<div class="form-group">
														<label for="instansi">Instansi</label>
														<input
															type="text"
															class="form-control"
															id="instansi"
															name="instansi"
															placeholder="Instansi"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('instansi')}}</span>
													</div>
												</div>
											</div>
							
											<div class="row">
												<div class="col-2">
													<div class="form-group">
														<label for="sesi">Sesi</label>
														<input
															type="text"
															class="form-control"
															id="sesi"
															name="sesi"
															placeholder="Sesi"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('sesi')}}</span>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="hadir">Hadir</label>
														<input
															type="text"
															class="form-control"
															id="hadir"
															name="hadir"
															placeholder="Peserta Hadir"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('hadir')}}</span>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="tidak_hadir">Tidak Hadir</label>
														<input
															type="text"
															class="form-control"
															id="tidak_hadir"
															name="tidak_hadir"
															placeholder=" PesertaTidak Hadir"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('tidak_hadir')}}</span>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="nilai_tertinggi">Nilai Tertinggi</label>
														<input
															type="text"
															class="form-control"
															id="nilai_tertinggi"
															name="nilai_tertinggi"
															placeholder="Nilai Tertinggi"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('nilai_tertinggi')}}</span>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="nilai_terendah">Nilai Terendah</label>
														<input
															type="text"
															class="form-control"
															id="nilai_terendah"
															name="nilai_terendah"
															placeholder="Nilai Terendah"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('nilai_terendah')}}</span>
													</div>
												</div>
												{{-- <div class="col-2">
													<div class="form-group">
														<label for="jumlah">Jumlah</label>
														<input
															type="text"
															class="form-control"
															id="jumlah"
															name="jumlah"
															placeholder="Jumlah"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('jumlah')}}</span>
													</div>
												</div> --}}
											</div>
											
											<button type="submit" class="btn btn-primary">
												Submit
											</button>
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
        <div class="row">
          <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-book"></i></span>
    
              <div class="info-box-content">
                <span class="info-box-text">Dokumen Survey Tilok</span>
                <span class="info-box-number">
                  <a target="_blank" href="{{asset('public/uploads/'. $data->dok_survey)}}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-book"></i></span>
    
              <div class="info-box-content">
                <span class="info-box-text">Dokumen SK TIM</span>
                <span class="info-box-number">
                  <a target="_blank" href="{{asset('public/uploads/'. $data->dok_sktim)}}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
    
          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>
    
          <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-book"></i></span>
    
              <div class="info-box-content">
                <span class="info-box-text">Dokumen Laporan</span>
                <span class="info-box-number">
                  <a target="_blank" href="{{asset('public/uploads/'. $data->dok_laporan)}}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
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
								<th>TILOK</th>
								<th>INSTANSI</th>
								<th>SESI</th>
								<th>HADIR</th>
								<th>TIDAK HADIR</th>
								<th>NILAI TERTINGGI</th>
								<th>NILAI TERENDAH</th>
								<th>JUMLAH</th>
								<th>AKSI</th>
							</tr>
							</thead>
							<tbody>
								@foreach ($detail_tilok as $no => $item)
								<tr>
									<td>{{$no+1}}</td>
									<td>{{$item->detailEvent->tilok}}</td>
									<td>{{$item->instansi}}</td>
									<td>{{$item->sesi}}</td>
									<td>{{$item->hadir}}</td>
									<td>{{$item->tidak_hadir}}</td>
									<td>{{$item->nilai_tertinggi}}</td>
									<td>{{$item->nilai_terendah}}</td>
									<td>{{$item->jumlah}}</td>
									<td>
										{{-- <a href="/detail-event/show/{{encrypt($item->id)}}" class="text-center btn btn-primary"><i class="fa fa-eye" aria-hidden="true">DETAIL</i></a> --}}
										@if (Auth::user()->role == 'admin')
										<a href="/event/edit/{{encrypt($item->id)}}" class="btn btn-success"><i class="fa fa-pencil-alt" aria-hidden="true"></i></a>
										<a href="/event/destroy/{{encrypt($item->id)}}" class="text-center btn btn-danger" onclick="return confirm('Delete this data??')"><i class="fa fa-trash" aria-hidden="true"></i></a>
										@endif
									</td>
								</tr>
								@endforeach
							</tbody>
							{{-- <tfoot>
							<tr>
								<th>NO</th>
								<th>TILOK</th>
								<th>INSTANSI</th>
								<th>SESI</th>
								<th>HADIR</th>
								<th>TIDAK HADIR</th>
								<th>NILAI TERTINGGI</th>
								<th>NILAI TERENDAH</th>
								<th>JUMLAH</th>
								<th>AKSI</th>
							</tr>
							</tfoot> --}}
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