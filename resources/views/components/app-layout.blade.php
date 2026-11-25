@extends('layouts.app')

@section('title', isset($title) ? trim((string) $title) : 'Dashboard')

@section('content')
    {{ $slot ?? '' }}
@endsection
