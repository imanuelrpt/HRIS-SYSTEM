@extends('layouts.guest')

@section('title', isset($title) ? trim((string) $title) : 'Masuk')

@section('content')
    {{ $slot ?? '' }}
@endsection
