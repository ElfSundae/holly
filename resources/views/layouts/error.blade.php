@extends('layouts.master')

@section('baidu-analytics', config('services.baidu_analytics'))

@section('title', 'Error '.$exception->getStatusCode())

@prepend('css')
<style type="text/css">
.message {
  font-size: 21px;
}
</style>
@endprepend

@section('body')
<div class="message">
  @yield('message', $exception->getMessage())
</div>
@stop
