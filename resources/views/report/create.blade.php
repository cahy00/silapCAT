@extends('layouts._layout')

@section('content')
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				{{-- <h1>{{$title}} Tilok {{$data->tilok}}</h1> --}}
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
								<button type="button" class="btn btn-dark" data-toggle="modal" data-target="#modal-import">
                  <i class="fas fa-plus"> Import Data</i>
                </button>
								<button type="button" class="btn btn-light" data-toggle="modal" data-target="#uploadDokumen">
                  <i class="fas fa-plus"> Upload Dokumen</i>
                </button>
							</h3>
						</div>
					</div>
				</div>
			</div>

			{{-- <div class="modal fade" id="modal-xl">
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
									<form action="{{ Auth::user()->hasRole('admin') ? '/report/store' : '/report-operator/store' }}" method="POST" enctype="multipart/form-data">
										@csrf()
										<input type="hidden" name="event_id" value="{{$data->event->id}}">
										<input type="hidden" name="tilok_id" value="{{$data->tilok->id}}">
										<div class="card-body">
											<div class="row">
												<div class="col-3">
													<div class="form-group">
														<label for="tilok">Event Kegiatan</label>
														<input
															type="text"
															class="form-control"
															id="tilok"
															value="{{$data->event->name}}"
															required
															disabled
														/>
														<span class="error invalid-feedback">{{$errors->first('tilok')}}</span>
													</div>
												</div>
												<div class="col-3">
													<div class="form-group">
														<label for="tilok">Titik Lokasi</label>
														<input
															type="text"
															class="form-control"
															id="tilok"
															value="{{$data->tilok->name}}"
															required
															disabled
														/>
														<span class="error invalid-feedback">{{$errors->first('tilok')}}</span>
													</div>
												</div>
												<div class="col-3">
													<div class="form-group">
														<label>Tanggal</label>
															<div class="input-group date" id="reservationdate" data-target-input="nearest">
																	<input type="text" name="exam_date" class="form-control datetimepicker-input" data-target="#reservationdate"
																	placeholder="Tanggal" autocomplete="off"
																	required/>
																	<div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
																			<div class="input-group-text"><i class="fa fa-calendar"></i></div>
																	</div>
															</div>
													</div>
												</div>
												<div class="col-3">
													<div class="form-group">
														<label for="instansi">Instansi</label>
														<select name="instansi_name" id="" class="form-control form-select">
															<option value="Pemerintah Kab Manokwari">Pemerintah Kab Manokwari</option>
															<option value="Pemerintah Kab Manokwari Selatan">Pemerintah Kab Manokwari Selatan</option>
															<option value="Pemerintah Kab Fakfak">Pemerintah Kab Fakfak</option>
															<option value="Pemerintah Kab Kaimana">Pemerintah Kab Kaimana</option>
															<option value="Pemerintah Kab Pegunungan Arfak">Pemerintah Kab Pegunungan Arfak</option>
															<option value="Pemerintah Kab Teluk Bintuni">Pemerintah Kab Teluk Bintuni</option>
															<option value="Pemerintah Kab Teluk Wondama">Pemerintah Kab Teluk Wondama</option>
															<option value="Pemerintah Kab Sorong">Pemerintah Kab Sorong</option>
															<option value="Pemerintah Kab Sorong Selatan">Pemerintah Kab Sorong Selatan</option>
															<option value="Pemerintah Kab Maybrat">Pemerintah Kab Maybrat</option>
															<option value="Pemerintah Kab Tambraw">Pemerintah Kab Tambraw</option>
															<option value="Pemerintah Kab Raja Ampat">Pemerintah Kab Raja Ampat</option>
															<option value="Pemerintah Kota Sorong">Pemerintah Kota Sorong</option>
															<option value="Pemerintah Provinsi Papua Barat Daya">Pemerintah Provinsi Papua Barat Daya</option>
															<option value="Pemerintah Provinsi Papua Barat">Pemerintah Provinsi Papua Barat</option>
														</select>
														<span class="error invalid-feedback">{{$errors->first('instansi_name')}}</span>
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
															id="session"
															name="session"
															placeholder="Sesi"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('session')}}</span>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="hadir">Hadir</label>
														<input
															type="text"
															class="form-control"
															id="participant_present"
															name="participant_present"
															placeholder="Peserta Hadir"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('participant_present')}}</span>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="tidak_hadir">Tidak Hadir</label>
														<input
															type="text"
															class="form-control"
															id="participant_absent"
															name="participant_absent"
															placeholder=" PesertaTidak Hadir"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('participant_absent')}}</span>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="hadir">Peserta Keseluruhan</label>
														<input
															type="text"
															class="form-control"
															id="hadir"
															name="participant_total"
															placeholder="Peserta Keseluruhan"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('hadir')}}</span>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="nilai_tertinggi">Nilai Tertinggi</label>
														<input
															type="text"
															class="form-control"
															id="nilai_tertinggi"
															name="highest_score"
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
															name="lowest_score"
															placeholder="Nilai Terendah"
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('nilai_terendah')}}</span>
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
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div> --}}

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
									<form action="{{ Auth::user()->hasRole('admin') ? '/report/store' : '/report-operator/store' }}" method="POST" enctype="multipart/form-data">
										@csrf()
										<input type="hidden" name="event_id" value="{{$data->event->id}}">
										<input type="hidden" name="tilok_id" value="{{$data->tilok->id}}">
										<div class="card-body">
											<div class="row">
												<div class="col-3">
													<div class="form-group">
														<label for="tilok">Event Kegiatan</label>
														<input
															type="text"
															class="form-control"
															id="tilok"
															{{-- name="tilok" --}}
															value="{{$data->event->name}}"
															required
															disabled
														/>
														<span class="error invalid-feedback">{{$errors->first('tilok')}}</span>
													</div>
												</div>
												<div class="col-3">
													<div class="form-group">
														<label for="tilok">Titik Lokasi</label>
														<input
															type="text"
															class="form-control"
															id="tilok"
															{{-- name="tilok" --}}
															value="{{$data->tilok->name}}"
															{{-- placeholder="{{$data->tilok->name}}" --}}
															required
															disabled
														/>
														<span class="error invalid-feedback">{{$errors->first('tilok')}}</span>
													</div>
												</div>
												<div class="col-3">
													<div class="form-group">
														<label>Tanggal</label>
															<div class="input-group date" id="reservationdate" data-target-input="nearest">
																	<input type="text" name="exam_date" class="form-control datetimepicker-input" data-target="#reservationdate"
																	placeholder="Tanggal" autocomplete="off"
																	required/>
																	<div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
																			<div class="input-group-text"><i class="fa fa-calendar"></i></div>
																	</div>
															</div>
													</div>
												</div>
												<div class="col-3">
													<div class="form-group">
														<label for="instansi">Instansi</label>
														<select name="instansi_name" id="" class="form-control select2bs4" style="width: 100%;">
															<option value="Pemerintah Kab Manokwari">Pemerintah Kab Manokwari</option>
															<option value="Pemerintah Kab Manokwari Selatan">Pemerintah Kab Manokwari Selatan</option>
															<option value="Pemerintah Kab Fakfak">Pemerintah Kab Fakfak</option>
															<option value="Pemerintah Kab Kaimana">Pemerintah Kab Kaimana</option>
															<option value="Pemerintah Kab Pegunungan Arfak">Pemerintah Kab Pegunungan Arfak</option>
															<option value="Pemerintah Kab Teluk Bintuni">Pemerintah Kab Teluk Bintuni</option>
															<option value="Pemerintah Kab Teluk Wondama">Pemerintah Kab Teluk Wondama</option>
															<option value="Pemerintah Kab Sorong">Pemerintah Kab Sorong</option>
															<option value="Pemerintah Kab Sorong Selatan">Pemerintah Kab Sorong Selatan</option>
															<option value="Pemerintah Kab Maybrat">Pemerintah Kab Maybrat</option>
															<option value="Pemerintah Kab Tambraw">Pemerintah Kab Tambraw</option>
															<option value="Pemerintah Kab Raja Ampat">Pemerintah Kab Raja Ampat</option>
															<option value="Pemerintah Kota Sorong">Pemerintah Kota Sorong</option>
															<option value="Pemerintah Provinsi Papua Barat Daya">Pemerintah Provinsi Papua Barat Daya</option>
															<option value="Pemerintah Provinsi Papua Barat">Pemerintah Provinsi Papua Barat</option>
															<option value="#">Setjen Komisi Pemilihan Umum</option>
															<option value="#">Badan Nasional Penanggulangan Bencana</option>
															<option value="#">Badan Meteorologi, Klimatologi dan Geofisika</option>
															<option value="#">Badan Pengawas Pemilihan Umum</option>
															{{-- <option value="Instansi Vertikal">Instansi Vertikal</option>
															<option value="lain-lain">lain-lain</option> --}}
														</select>
														<span class="error invalid-feedback">{{$errors->first('instansi_name')}}</span>
													</div>
												</div>
											</div>
											
											{{-- REPEATER CONTAINER --}}
											<div id="reports-container">
												<div class="report-item border rounded p-3 mb-3">
													<div class="row">
														<div class="col-2">
															<div class="form-group">
																<label for="sesi">Sesi</label>
																<input
																	type="number"
																	class="form-control session"
																	id="session"
																	name="reports[0][session]"
																	placeholder="1-4"
																	required
																	min="1"
																	max="4"
																/>
																{{-- <select name="reports[0][session]" id="session[0]" class="form-control session">
																	<option value="1">1</option>
																	<option value="2">2</option>
																	<option value="3">3</option>
																	<option value="4">4</option>
																</select> --}}
																<span class="error invalid-feedback">{{$errors->first('session')}}</span>
															</div>
															<div class="col-md-9 text-left">
																<button type="button" class="btn btn-sm btn-danger btn-remove-item">Hapus Sesi</button>
															</div>
														</div>
														<div class="col-2">
															<div class="form-group">
																<label for="hadir">Hadir</label>
																<input
																	type="number"
																	class="form-control participant_present"
																	id="participant_present"
																	name="reports[0][participant_present]"
																	placeholder="Peserta Hadir"
																	required
																	maxlength="3"
																	min="1"
																	max="500"
																/>
																<span class="error invalid-feedback">{{$errors->first('participant_present')}}</span>
															</div>
														</div>
														<div class="col-2">
															<div class="form-group">
																<label for="tidak_hadir">Tidak Hadir</label>
																<input
																	type="number"
																	class="form-control participant_absent"
																	id="participant_absent"
																	name="reports[0][participant_absent]"
																	placeholder=" PesertaTidak Hadir"
																	required
																/>
																<span class="error invalid-feedback">{{$errors->first('participant_absent')}}</span>
															</div>
														</div>
														<div class="col-2">
															<div class="form-group">
																<label for="hadir">Peserta Keseluruhan</label>
																<input
																	type="number"
																	class="form-control participant_total"
																	id="hadir"
																	name="reports[0][participant_total]"
																	placeholder="Peserta Keseluruhan"
																	required
																/>
																<span class="error invalid-feedback">{{$errors->first('hadir')}}</span>
															</div>
														</div>
														<div class="col-2">
															<div class="form-group">
																<label for="nilai_tertinggi">Nilai Tertinggi</label>
																<input
																	type="number"
																	class="form-control highest_score"
																	id="nilai_tertinggi"
																	name="reports[0][highest_score]"
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
																	type="number"
																	class="form-control lowest_score"
																	id="nilai_terendah"
																	name="reports[0][lowest_score]"
																	placeholder="Nilai Terendah"
																	required
																/>
																<span class="error invalid-feedback">{{$errors->first('nilai_terendah')}}</span>
															</div>
															
														</div>
													</div>
												</div>
											</div>
											
											<button type="submit" class="btn btn-primary">
												Submit
											</button>
											<button type="button" id="btn-add-report" class="btn btn-secondary">+ Tambah Sesi</button>
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

			<div class="modal fade" id="modal-import">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Import Data</h4>
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
									<form action="/import-report" method="POST" enctype="multipart/form-data">
										@csrf()
										<div class="card-body">
											<div class="row">
												<div class="col-12">
													<div class="form-group">
														<label for="file">File Excel</label>
														<input
															type="file"
															class="form-control"
															id="file"
															name="file"
															{{-- value="{{$data->event->name}}" --}}
														/>
														<span class="error invalid-feedback">{{$errors->first('file')}}</span>
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
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

			<div class="modal fade" id="uploadDokumen">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Upload Dokumen</h4>
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
									<form action="/report/upload-dokumen" method="POST" enctype="multipart/form-data">
										@csrf()
										<input type="hidden" name="event_id" value="{{$data->event->id}}">
										<input type="hidden" name="tilok_id" value="{{$data->tilok->id}}">
										<div class="card-body">
											<div class="row">
												<div class="col-6">
													<div class="form-group">
														<label for="tilok">Event Kegiatan</label>
														<select name="document_name" id="" class="form-control form-select">
															<option value="Laporan Pelaksanaan">Laporan Pelaksanaan</option>
															<option value="Laporan Survey Tilok">Laporan Survey Tilok</option>
															<option value="Dokumen SK TIM">SK TIM</option>
														</select>
														<span class="error invalid-feedback">{{$errors->first('tilok')}}</span>
													</div>
												</div>
												<div class="col-6">
													<div class="form-group">
														<label for="tilok">File Laporan</label>
														<input
															type="file"
															class="form-control"
															id="document_path"
															name="document_path"
															{{-- value="{{$data->tilok->name}}" --}}
															{{-- placeholder="{{$data->tilok->name}}" --}}
															required
														/>
														<span class="error invalid-feedback">{{$errors->first('document_path')}}</span>
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
					@foreach ($document as $item)
						<div class="col-12 col-sm-6 col-md-4">
							<div class="info-box">
								<span class="info-box-icon bg-info elevation-1"><i class="fas fa-book"></i></span>
			
								<div class="info-box-content">
									<span class="info-box-text">{{$item->document_name}}</span>
									<span class="info-box-number">
										<a target="_blank" href="{{asset('public/uploads/'. $item->document_path)}}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
									</span>
								</div>
								<!-- /.info-box-content -->
							</div>
							<!-- /.info-box -->
						</div>
					@endforeach
    
          <div class="clearfix hidden-md-up"></div>
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
								<th>TANGGAL PELAKSANAAN</th>
								<th>INSTANSI</th>
								<th>SESI</th>
								<th>HADIR</th>
								<th>TIDAK HADIR</th>
								<th>NILAI TERTINGGI</th>
								<th>NILAI TERENDAH</th>
								<th>PESERTA KESELURUHAN</th>
								{{-- <th>AKSI</th> --}}
							</tr>
							</thead>
							<tbody>
								@foreach ($detail_tilok as $no => $item)
								<tr>
									<td>{{$no+1}}</td>
									<td>{{$item->tilok->name}}</td>
									<td>{{$item->exam_date}}</td>
									<td>{{$item->instansi_name}}</td>
									<td>{{$item->session}}</td>
									<td>{{$item->participant_present}}</td>
									<td>{{$item->participant_absent}}</td>
									<td>{{$item->highest_score}}</td>
									<td>{{$item->lowest_score}}</td>
									<td>{{$item->participant_total}}</td>
									{{-- <td>
										<a href="/detail-event/show/{{encrypt($item->id)}}" class="text-center btn btn-primary"><i class="fa fa-eye" aria-hidden="true"></i></a>
										@if (Auth::user()->role == 'admin')
										<a href="/event/edit/{{encrypt($item->id)}}" class="btn btn-success"><i class="fa fa-pencil-alt" aria-hidden="true"></i></a>
										<a href="/event/destroy/{{encrypt($item->id)}}" class="text-center btn btn-danger" onclick="return confirm('Delete this data??')"><i class="fa fa-trash" aria-hidden="true"></i></a>
										@endif
									</td> --}}
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

@push('script')
<script>
	document.addEventListener('DOMContentLoaded', () => {
		let idx = 1;
		const container = document.getElementById('reports-container');

		function recalc(item){
			const present = +item.querySelector('.partcipant_present').value || 0;
			const absent  = +item.querySelector('.partcipant_absent').value  || 0;
    item.querySelector('.participant_total').value = present + absent;

		const high = +item.querySelector('.highest_score').value || 0;
    const low  = +item.querySelector('.lowest_score').value  || 0;
    const avgEl = item.querySelector('.average_average');
    // Hitung rata-rata hanya jika kedua nilai ada
    avgEl.value = (high && low) 
      ? ((high + low) / 2).toFixed(2) 
      : '';
		}

		document.getElementById('btn-add-report').addEventListener('click', () =>{
			const orig = container.querySelector('.report-item');
			const clone = orig.cloneNode(true);

			// Update semua name attributes & reset value
				clone.querySelectorAll('input').forEach(el => {
				const name = el.getAttribute('name');
				const newName = name.replace(/\d+/, idx);
				el.setAttribute('name', newName);
				if (el.readOnly) el.value = '';
				else el.value = '';
			});

			container.appendChild(clone);
			idx++;

				container.addEventListener('click', e => {
				if (e.target.matches('.btn-remove-item')) {
					const items = container.querySelectorAll('.report-item');
					if (items.length > 1) {
						e.target.closest('.report-item').remove();
					}
				}
			});

				container.addEventListener('input', e => {
				if (e.target.matches('.participant_present, .participant_absent, .highest_score, .lowest_score')) {
					const item = e.target.closest('.report-item');
					recalc(item);
				}
			});
		})
	})
</script>
@endpush