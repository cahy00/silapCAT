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
					<form action="/document/inka" method="POST" enctype="multipart/form-data">
						@csrf()
						<input hidden name="id" value="{{decrypt($id)}}">
						<div class="card-body">
							<div class="row">
								<div class="col-3">
									<div class="form-select">
										<label for="exampleInputEmail1">Tipe Dokumen</label>
										<select name="type_id" id="" class="form-control" style="text-transform: uppercase;">
											@foreach ($type as $item)
												<option value="{{$item->id}}" style="text-transform: uppercase;">{{$item->title}}</option>
											@endforeach
										</select>
										@error('type_id')
											<div class="invalid-feedback">
												<strong>{{$message}}</strong>
											</div>
										@enderror
									</div>
								</div>
								<div class="col-3">
									<div class="form-group">
										<label for="exampleInputEmail1">Nomor Dokumen</label>
										<input
											type="text"
											class="form-control"
											id="exampleInputEmail1"
											name="number"
											placeholder="Nomor Dokumen"
										/>
										<span class="error invalid-feedback">{{$errors->first('number')}}</span>
									</div>
								</div>
								<div class="col-3">
									<div class="form-group">
										<label for="exampleInputEmail1">Perihal Dokumen</label>
										<input
											type="text"
											class="form-control"
											id="exampleInputEmail1"
											name="name"
											placeholder="Perihal Dokumen"
										/>
									</div>
								</div>
								<div class="col-3">
									<div class="form-group">
										<label>Tanggal Dokumen</label>
											<div class="input-group date" id="reservationdate" data-target-input="nearest">
													<input type="text" name="date" class="form-control datetimepicker-input" data-target="#reservationdate"
													placeholder="Tanggal Dokumen"/>
													<div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
															<div class="input-group-text"><i class="fa fa-calendar"></i></div>
													</div>
											</div>
									</div>
								</div>
							</div>
							<div class="row">
								@if ($title == 'Surat Masuk')
								<div class="col-2">
									<div class="form-group">
										<label>Tanggal @if($title == 'Surat Keluar') Keluar @else Masuk @endif</label>
											<div class="input-group date" id="tgl_masuk" data-target-input="nearest">
													<input type="text" name="tgl_distribusi" class="form-control datetimepicker-input" data-target="#tgl_masuk"
													placeholder="Tanggal @if($title == 'Surat Keluar') Keluar @else Masuk @endif"/>
													<div class="input-group-append" data-target="#tgl_masuk" data-toggle="datetimepicker">
															<div class="input-group-text"><i class="fa fa-calendar"></i></div>
													</div>
											</div>
									</div>
								</div>
								<div class="col-3">
									<div class="form-group">
										<label for="disposisi">Disposisi</label>
										<input
											type="text"
											class="form-control"
											id="disposisi"
											name="disposisi"
											placeholder="Disposisi"
										/>
										<span class="error invalid-feedback">{{$errors->first('disposisi')}}</span>
									</div>
								</div>
								<div class="col-2">
									<div class="form-group">
										<label for="disposisi">Asal Dokumen</label>
										<input
											type="text"
											class="form-control"
											id="asal"
											name="asal"
											placeholder="Asal Dokumen"
										/>
										<span class="error invalid-feedback">{{$errors->first('asal')}}</span>
									</div>
								</div>
								<div class="col-2">
									<div class="form-select">
										<label for="exampleInputEmail1">Sifat Dokumen</label>
										<select name="sifat" id="" class="form-control" style="text-transform: uppercase;">
											<option value="biasa">Biasa</option>
											<option value="penting">Penting</option>
											<option value="rahasia">Rahasia</option>
											<option value="segera">Segera</option>
										</select>
									</div>
								</div>
								<div class="col-3">
									<div class="form-group">
										<label for="exampleInputFile">Upload Dokumen</label>
										<div class="input-group">
											<input
													type="file"
													name="file"
													class="form-control"
													id="exampleInputFile"
												/>
										</div>
									</div>
								</div>
								@endif
								@if($title == 'Surat Keluar')
								<div class="col-4">
									<div class="form-group">
										<label>Tanggal @if($title == 'Surat Keluar') Keluar @else Masuk @endif</label>
											<div class="input-group date" id="tgl_masuk" data-target-input="nearest">
													<input type="text" name="tgl_distribusi" class="form-control datetimepicker-input" data-target="#tgl_masuk"
													placeholder="Tanggal @if($title == 'Surat Keluar') Keluar @else Masuk @endif"/>
													<div class="input-group-append" data-target="#tgl_masuk" data-toggle="datetimepicker">
															<div class="input-group-text"><i class="fa fa-calendar"></i></div>
													</div>
											</div>
									</div>
								</div>
								<div class="col-4">
									<div class="form-select">
										<label for="exampleInputEmail1">Sifat Dokumen</label>
										<select name="sifat" id="" class="form-control" style="text-transform: uppercase;">
											<option value="biasa">Biasa</option>
											<option value="penting">Penting</option>
											<option value="rahasia">Rahasia</option>
											<option value="segera">Segera</option>
										</select>
									</div>
								</div>
								<div class="col-4">
									<div class="form-group">
										<label for="exampleInputFile">Upload Dokumen</label>
										<div class="input-group">
											<input
													type="file"
													name="file"
													class="form-control"
													id="exampleInputFile"
												/>
										</div>
									</div>
								</div>
								@endif
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
								<th>TIPE DOKUMEN</th>
								<th>NO DOKUMEN</th>
								<th>PERIHAL DOKUMEN</th>
								<th>TANGGAL MASUK</th>
								<th>AKSI</th>
							</tr>
							</thead>
							<tbody>
								@foreach ($document as $no => $item)
								<tr>
									<td>{{$no+1}}</td>
									<td>{{$item->type->title}}</td>
									<td>{{$item->number}}</td>
									<td>
										<a href="{{asset($item->file)}}">{{$item->name}}</a>
									</td>
									<td>{{$item->tgl_distribusi}}</td>
									<td>
										<a href="/document/inka/show/{{encrypt($item->id)}}" class="text-center"><i class="fa fa-eye" aria-hidden="true"></i></a>
										@if (Auth::user()->role == 'admin')
										<a href="/document/inka/edit/{{encrypt($item->id)}}"><i class="fa fa-fire" aria-hidden="true"></i></a>
										<a href="/document/destroy/{{encrypt($item->id)}}" class="text-center" onclick="return confirm('Delete this data??')"><i class="fa fa-trash" aria-hidden="true"></i></a>
										@endif
									</td>
								</tr>
								@endforeach
							</tbody>
							<tfoot>
							<tr>
								<th>NO</th>
								<th>TIPE DOKUMEN</th>
								<th>NO DOKUMEN</th>
								<th>PERIHAL DOKUMEN</th>
								<th>TANGGAL MASUK</th>
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