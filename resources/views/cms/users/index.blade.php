<?php
$metaName = 'Page Name: Users';
$metaTitle = 'Page Title: Users';
$metaDescription = 'Page Description: Users';
?>

@extends('cms.master')

@section('content')
    @if (isset($datatables))
        <div class="content-header">
            <h1>{{ $metaName }}</h1>
            <div class="btn-group">
                <a href="{{ \Locales::route('users/admins/create') }}" class="js-create btn btn-primary"><span class="glyphicon glyphicon-plus"></span>{{ trans('cms/forms.createUserButton') }}</a>
            </div>
        </div>
        <div class="dataTableWrapper table-responsive ajax-lock">
            <table id="datatablesUsers" class="dataTable table table-striped table-bordered table-hover">
            @if (isset($datatables['ajax']))
                <thead>
                    <th>{{ trans('cms/datatables.name') }}</th>
                    <th>{{ trans('cms/datatables.email') }}</th>
                </thead>
                <tbody>
                {{-- State can't be saved with pipelining and deferLoading enabled and first page in html
                    @foreach ($datatables['data'] as $td)
                    <tr>
                        <td>{{ $td->name }}</td>
                        <td>{{ $td->email }}</td>
                    </tr>
                    @endforeach
                --}}
                </tbody>
            @endif
            </table>
        </div>
    @endif
@endsection

@section('script')
unikat.callback = function() {
    'use strict';

    @if (isset($datatables))
        $('.dataTable').DataTable({
        @if (isset($datatables['ajax']))
            ajax: unikat.datatables({
                url: '{{ $datatables['ajax'] }}'
            }),
        @else
            data: {!! $datatables['data'] !!},
        @endif
            columns: [
                { data: 'name', title: '{{ trans('cms/datatables.name') }}' },
                { data: 'email', title: '{{ trans('cms/datatables.email') }}' }
            ]
        });
    @endif
};
@endsection
