@extends('app.base')

@section('content')

<table class="table table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Producto</th>
            <th>
                Carrito de la compra
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>Producto 1</td>
            <td>
                <a href="{{ route('add', 1) }}" class="btn btn-success btn-sm">+</a>
                @isset($cart[1])
                    {{ $cart[1]['quantity'] }}
                @endisset
                <a href="{{ route('substract', 1) }}" class="text-white btn btn-danger btn-sm">-</a>
                @isset($cart[1])
                    Total: {{ $cart[1]['quantity'] * $cart[1]['price'] }} €
                @endisset
            </td>
        </tr>
        <tr>
            <td>2</td>
            <td>Producto 2</td>
            <td>
                <a href="{{ route('add', 2) }}" class="btn btn-success btn-sm">+</a>
                @isset($cart[2])
                    {{ $cart[2]['quantity'] }}
                @endisset
                <a href="{{ route('substract', 2) }}" class="text-white btn btn-danger btn-sm">-</a>
                @isset($cart[2])
                    Total: {{ $cart[2]['quantity'] * $cart[2]['price'] }} €
                @endisset
            </td>
        </tr>
        <tr>
            <td colspan="3" class="text-right">
                @if($total > 0)
                    <strong>Total: {{ $total }} €</strong>
                    <a href="{{ route('checkout') }}" class="btn btn-primary btn-sm">Tramitar pedido</a>
                @endif
            </td>
        </tr>
    </tbody>
</table>

@endsection