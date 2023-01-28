@extends('layouts.default')

<!-- Load nội dung trang home/dashboard.php vào vị trí section('page') của layouts/default.php -->
@section('content')
    <div class="container mt-200 mb-100">
        <img src="{{ asset('/storage/images/404/404.svg') }}" class="m-auto d-block w-50" alt="Not Found Error">
    </div>
@endsection

<!-- Chèn script vào vị trí section("js") trong layout default -->
@section('javascript')
@include('layouts.script')
@endsection
