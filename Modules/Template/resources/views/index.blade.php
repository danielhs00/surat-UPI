@extends('layouts.mantis')
@section('title', 'Dashboard Operator')
@include('components.mantis.header', ['role' => 'operator'])

@section('content')
        <h4>Selamat datang, {{ auth()->user()->name }}!</h4>
        <p class="text-muted">Anda login sebagai Operator.</p>
@endsection