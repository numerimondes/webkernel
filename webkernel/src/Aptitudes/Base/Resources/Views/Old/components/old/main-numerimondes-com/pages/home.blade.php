@extends('main-numerimondes-com.layouts.app')

@section('content')
@include('main-numerimondes-com.partials.apple-nav')
@include('main-numerimondes-com.components.home.hero')
    @include('main-numerimondes-com.components.home.modules')
    @include('main-numerimondes-com.components.home.architecture')
    @include('main-numerimondes-com.components.home.offers')
    @include('main-numerimondes-com.components.home.partners')
    @include('main-numerimondes-com.components.home.licenses')
    @include('main-numerimondes-com.components.home.roadmap')
    @include('main-numerimondes-com.components.home.footer')
@endsection
