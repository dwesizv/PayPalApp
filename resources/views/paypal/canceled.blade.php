@extends('app.base')

@section('content')

<div class="alert alert-success">
    Pago cancelado por PayPal. No se ha realizado ningún cargo.
    Si deseas volver a intentarlo, por favor regresa al carrito de compras.
    <a href="{{ route('main') }}" class="btn btn-secondary">Volver al carrito</a>
</div>

@endsection