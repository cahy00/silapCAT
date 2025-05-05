@extends('layouts._layout')

@section('content')
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1>Detail Dokumen</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active">Detail Dokumen</li>
				</ol>
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>

<section class="content">
	<div class="container-fluid">
		<div class="row">
			<!-- /.col -->
			<div class="col-md-6">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Data Dokumen</h3>

					</div>
					<!-- /.card-header -->
					<div class="card-body p-0">
						<table class="table">
							<tbody>
								<tr>
									<td>Tipe Dokumen</td>
									<td>{{$data->type->title}}</td>
									{{-- <td><span class="badge bg-danger">55%</span></td> --}}
								</tr>
								<tr>
									<td>Judul Dokumen</td>
									<td>{{$data->name}}</td>
								</tr>
								<tr>
									<td>Nomor Dokumen</td>
									<td>{{$data->number}}</td>
								</tr>
								<tr>
									<td>Tanggal Dokumen</td>
									<td>{{$data->date}}</td>
								</tr>
								<tr>
									<td>File Dokumen</td>
									<td>
										<a href="{{asset('public/uploads/'.$data->file)}}">{{$data->name}}</a>
										<i class="fa fa-download" aria-hidden="true"></i>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<!-- /.card-body -->
				</div>
			</div>
			<div class="col-md-6">
				<div class="card">
					<div class="card-body p-0">
						<table class="table">
							<tbody>
								<tr>
									<td>Jenis Surat</td>
									<td>{{$data->jenis_surat}}</td>
									{{-- <td><span class="badge bg-danger">55%</span></td> --}}
								</tr>
								<tr>
									<td>Tanggal Masuk Dokumen</td>
									<td>{{$data->tgl_distribusi}}</td>
								</tr>
								<tr>
									<td>Asal Dokumen</td>
									<td>{{$data->asal}}</td>
								</tr>
								<tr>
									<td>Disposisi Dokumen</td>
									<td>{{$data->disposisi}}</td>
								</tr>
								<tr>
									<td>Penerima Dokumen</td>
									<td>{{$data->user->name}}</td>
								</tr>
								<tr>
									<td>Sifat Dokumen</td>
									<td><span class="badge @if($data->sifat == 'biasa') bg-primary @elseif($data->sifat == 'rahasia') bg-danger @elseif($data->sifat == 'penting') bg-warning @elseif($data->sifat == 'segera') bg-success @endif">{{$data->sifat}}</span></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="card card-primary">
					<div class="card-header">
						<h3 class="card-title">Tindak Lanjut Dokumen</h3>
					</div>
					<!-- /.card-header -->
					<div class="card-body">
						<form action="/document/inka/show/{id}" method="POST">
							@csrf
							<input type="hidden" name="document_id" value="{{$data->id}}">
							<div class="row">
								<div class="col-6">
									<div class="form-group">
										<label for="followup">Tindak Lanjut</label>
										<select name="followup" id="followup" class="form-control">
											<option value="0">Belum Diproses</option>
											<option value="1">Sedang Diproses</option>
											<option value="2">Sudah Selesai</option>
										</select>
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label for="description">Deskripsi</label>
										<textarea name="description" id="description" cols="30" rows="5" class="form-control"></textarea>
									</div>
								</div>
							</div>
							<button type="submit" class="btn btn-primary">Simpan</button>
						</form>
					</div>
					<!-- /.card-body -->
				</div>
			</div>
		</div>
	</div>
</section>
<section class="content">
	<div class="container-fluid">

		<!-- Timelime example  -->
		<div class="row">
			<div class="col-md-12">
				<div class="timeline">
					@foreach ($action as $item)
							
					<div class="time-label">
						<span class="">{{$item->created_at->format('d M Y')}}</span>
					</div>
					<div>
						<i class="fas fa-envelope bg-blue"></i>
						<div class="timeline-item">
							<span class="time"><i class="fas fa-clock"></i> {{$item->created_at->format('H:i')}}</span>
							<h3 class="timeline-header"><a href="#">{{$item->document->user->name}}</a> sent an inbox</h3>

							<div class="timeline-body">
								{{$item->description}}
							</div>
							<div class="timeline-footer">
								@if ($item->followup == 0)
								<a class="btn btn-danger btn-sm">Belum Ditindaklanjuti</a>
								@elseif($item->followup == 1)
								<a class="btn btn-warning btn-sm">Sedang Diproses</a>
								@else
								<a class="btn btn-success btn-sm">Sudah Selesai</a>
								@endif
							</div>
						</div>
					</div>
					@endforeach
					<div>
						<i class="fas fa-clock bg-gray"></i>
					</div>
				</div>
			</div>
			<!-- /.col -->
		</div>
	</div>
	<!-- /.timeline -->

</section>
@endsection