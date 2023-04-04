@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @foreach($products as $product)
        <div class="col-md-4">
            <div class="card">
              <div class="card-body mb-2"><br>
                <h5 class="card-title">{{ $product->name }}</h5>
                <p class="card-text">
                    <strong>Harga :</strong> Rp. {{ number_format($product->price)}} <br>
                    <hr>
                    <strong>Keterangan :</strong> <br>
                    {{ $product->description }} 
                </p>
                <a href="{{ url('pesan') }}/{{ $product->id }}" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Pesan</a>
              </div>
            </div> 
        </div>
        @endforeach
    </div>
</div>
@endsection
