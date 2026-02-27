@extends('app.base')

@section('content')
<form action="{{ route('pay') }}" method="post">
    @csrf
    Total:
    <input readonly disabled type="number" name="total" class="form-control mb-2" value="{{ $total }}">
    Nombre:
    <input type="text" name="name" placeholder="Nombre completo" class="form-control mb-2" value="{{session('name')}}" required>
    Correo electrónico:
    <input type="email" name="email" placeholder="Correo electrónico" class="form-control mb-2" value="{{session('email')}}" required>
    Dirección de envío:
    <input type="text" name="address" placeholder="Dirección de envío" class="form-control mb-2" value="{{session('address')}}" required>
    <button type="submit" class="btn btn-primary">Pagar</button>
    <a href="{{ route('main') }}" class="btn btn-secondary">Volver al carrito</a>
</form>
@endsection