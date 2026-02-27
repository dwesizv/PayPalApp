@extends('app.base')

@section('content')
<div style="max-width: 300px; margin: 10px auto; padding: 10px;">
Se va a proceder a pagar {{ $total }} {{ env('PAYPAL_CURRENCY') }} en Paypal.
</div>
<div id="paypal-container" style="max-width: 300px; margin: 40px auto; padding: 10px;"></div>
<a href="{{ route('main') }}" class="btn btn-secondary">Volver al carrito</a>
@endsection

@section('scripts')
<script src="https://www.paypal.com/sdk/js?currency={{ env('PAYPAL_CURRENCY') }}&client-id={{ env('PAYPAL_SANDBOX_CLIENT_ID') }}"></script>
<script src="{{ url('assets/js/paypal.js') }}"></script>
@endsection