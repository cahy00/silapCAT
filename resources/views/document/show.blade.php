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
					{{-- <div class="card-header">
						<h3 class="card-title">Data Dokumen</h3>
					</div> --}}
					<!-- /.card-header -->
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
					<!-- /.card-body -->
				</div>
			</div>
			{{-- <div class="col">
				<a href="/document/inka/{{}}" class="btn btn-success">Kembali</a>
			</div> --}}
		</div>
	</div>
</section>
@endsection