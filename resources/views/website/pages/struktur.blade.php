@extends('website.layout-detail')

@section('detail-content')

@section('title-detail')
    Struktur Organisasi
@endsection

@section('desc-detail')
    Struktur Organisasi Kantor Regional XIV BKN
@endsection

<section id="blog-details" class="blog-details section">
    <div class="container">
        <div class="row">
            <h2 class="display-4">Struktur Organisasi Kanreg XIV</h2>

            @foreach ($struktur as $item)
                <a href="#" class="me-12 thumbnail">
                    <img src="uploads/{{ $item->file }}" alt="" class="img-fluid">
                </a>
            @endforeach

            <br>
            <h2 class="category-title">Kepala Kantor Regional XIV BKN</h2>

            @foreach ($kepalaRegional as $item)
                <div class="d-md-flex small-img">
                    <img src="uploads/{{ $item->photo }}" alt="Foto Pegawai" class="img-fluid"
                        style="min-height: 100px; max-height:150px; min-width:auto; max-width:205px; object-fit: cover">
                    <div>
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td>Nama Lengkap</td>
                                    <td>:</td>
                                    <td>{{ $item->name }}</td>
                                </tr>
                                <tr>
                                    <td>NIP</td>
                                    <td>:</td>
                                    <td>{{ $item->masked_nip }}</td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>:</td>
                                    <td>{{ $item->position }}</td>
                                </tr>
                                <tr>
                                    <td>Unit Kerja</td>
                                    <td>:</td>
                                    <td>{{ $item->departement->name }}</td>
                                </tr>
                                <tr>
                                    <td>LHKPN</td>
                                    <td>:</td>
                                    <td><a href="uploads/{{ $item->lhkpn }}" target="_blank">LHKPN Kepala Kantor
                                            Regional XIV BKN</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <br>
            <h2 class="category-title">Kelompok Jabatan Administrator</h2>

            @foreach ($administrator as $item)
                <div class="d-md-flex small-img">
                    <img src="uploads/{{ $item->photo }}" alt="Foto Pegawai" class="img-fluid"
                        style="min-height: 100px; max-height:150px; min-width:auto; max-width:205px; object-fit: cover">
                    <div>
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td>Nama Lengkap</td>
                                    <td>:</td>
                                    <td>{{ $item->name }}</td>
                                </tr>
                                <tr>
                                    <td>NIP</td>
                                    <td>:</td>
                                    <td>{{ $item->masked_nip }}</td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>:</td>
                                    <td>{{ $item->position }}</td>
                                </tr>
                                <tr>
                                    <td>Unit Kerja</td>
                                    <td>:</td>
                                    <td>{{ $item->departement->name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <h2 class="category-title">Kelompok Jabatan Pengawas</h2>

            @foreach ($pengawas as $item)
                <br>
                <div class="d-md-flex small-img">

                    <img src="uploads/{{ $item->photo }}" alt="Foto Pegawai" class="img-fluid"
                        style="min-height: 100px; max-height:150px; min-width:auto; max-width:205px; object-fit: cover">
                    <div>
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td>Nama Lengkap</td>
                                    <td>:</td>
                                    <td>{{ $item->name }}</td>
                                </tr>
                                <tr>
                                    <td>NIP</td>
                                    <td>:</td>
                                    <td>{{ $item->masked_nip }}</td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>:</td>
                                    <td>{{ $item->position }}</td>
                                </tr>
                                <tr>
                                    <td>Unit Kerja</td>
                                    <td>:</td>
                                    <td>{{ $item->departement->name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
            @endforeach
        </div>
    </div>
</section>
@endsection
