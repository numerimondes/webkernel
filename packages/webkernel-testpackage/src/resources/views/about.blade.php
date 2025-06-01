@extends('webkernel-testpackage::layouts.app')

@section('title', 'About')

@section('content')
    <h2>About {{ $package_name }}</h2>
    <p>{{ $description }}</p>
    <p>Version: {{ $version }}</p>

    <a href="{{ route('webkernel_testpackage.index') }}">
        {{ __('{webkernel-testpackage}::translations.buttons.back') }}
    </a>
@endsection
