@extends('app.base')

@section('content')

<div class="alert alert-success">
    Error durante el proceso de pago por PayPal.
    Por favor, intenta nuevamente más tarde o contacta con el soporte si el problema persiste.
    <a href="{{ route('main') }}" class="btn btn-secondary">Volver al carrito</a>
</div>

@endsection