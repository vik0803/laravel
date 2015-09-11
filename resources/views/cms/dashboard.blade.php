<?php
$metaName = 'Page Name: Dashboard';
$metaTitle = 'Page Title: Dashboard';
$metaDescription = 'Page Description: Dashboard';
?>

@extends('cms.master')

@section('content')
<h1>{{ $metaName }}</h1>
You are logged in!
@endsection
