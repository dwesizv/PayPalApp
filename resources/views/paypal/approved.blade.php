@extends('app.base')

@section('content')

<div class="alert alert-success">
    Pago aprobado por PayPal. Gracias por tu compra.
    <a href="{{ route('main') }}" class="btn btn-secondary">Volver al inicio</a>
</div>

@endsection