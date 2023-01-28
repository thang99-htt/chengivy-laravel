@extends('layouts.default')

<!-- Load nội dung trang home/dashboard.php vào vị trí section('page') của layouts/default.php -->
@section('content')
@include('layouts.home')
@endsection

<!-- Chèn script vào vị trí section("js") trong layout default -->
@section('javascript')
@include('layouts.script')
@endsection
