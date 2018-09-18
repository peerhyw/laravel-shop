@extends('layouts.app')
@section('title','错误')

@section('content')
<div class="panel panel-default">
	<div class="panel-heading">错误</div>
	<div class="panel-body text-center">
		<h1>{{ $msg }}</h1>
		<a href="{{ route('root') }}" class="btn btn-primary">返回首页</a>
	</div>
</div>
@endsection