@extends('website.layout-detail')

@section('detail-content')

@section('title-detail')
    Agenda
@endsection

@section('desc-detail')
    Kanal Agenda Kantor Regional XIV BKN
@endsection

<section id="blog-posts-2" class="blog-posts-2 section">
    <div class="container">
        <div class="row">

            <h2 class="display-4">Agenda Kantor Regional</h2>

            @foreach ($agenda as $item)
                <a href="#" class="me-12 thumbnail">
                    <img src="uploads/{{ $item->file }}" alt="" class="img-fluid">
                </a>
            @endforeach


        </div>
    </div>
</section>
@endsection
