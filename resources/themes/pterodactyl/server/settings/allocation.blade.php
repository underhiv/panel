{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.master')

@section('title')
    @lang('server.config.allocation.header')
@endsection

@section('content-header')
    <h1>@lang('server.config.allocation.header')<small>@lang('server.config.allocation.header_sub')</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('index') }}">@lang('strings.home')</a></li>
        <li><a href="{{ route('server.index', $server->uuidShort) }}">{{ $server->name }}</a></li>
        <li>@lang('navigation.server.configuration')</li>
        <li class="active">@lang('navigation.server.port_allocations')</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-8">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('server.config.allocation.available')</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>@lang('strings.ip')</th>
                            <th>@lang('strings.alias')</th>
                            <th>@lang('strings.port')</th>
                            <th></th>
                        </tr>
                        @foreach ($server->allocations as $allocation)
                            <tr>
                                <td>
                                    <code>{{ $allocation->ip }}</code>
                                </td>
                                <td class="middle">
                                    @if(is_null($allocation->ip_alias))
                                        <span class="label label-default">@lang('strings.none')</span>
                                    @else
                                        <code>{{ $allocation->ip_alias }}</code>
                                    @endif
                                </td>
                                <td><code>{{ $allocation->port }}</code></td>
                                <td class="col-xs-2 middle">
                                    @if($allocation->id === $server->allocation_id)
                                        <span class="label label-success" data-allocation="{{ $allocation->id }}">@lang('strings.primary')</span>
                                    @else
                                        <span class="label label-default" data-action="set-connection" data-allocation="{{ $allocation->id }}">@lang('strings.make_primary')</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('server.config.allocation.help')</h3>
            </div>
            <div class="box-body">
                <p>@lang('server.config.allocation.help_text')</p>
            </div>
        </div>
    <div>
</div>
@endsection

@section('footer-scripts')
    @parent
    {!! Theme::js('js/frontend/server.socket.js') !!}
    <script>
    @can('reset-db-password', $server)
        $('[data-action="reset-database-password"]').click(function (e) {
            e.preventDefault();
            var block = $(this);
            $(this).find('i').addClass('fa-spin');
            $.ajax({
                type: 'POST',
                url: Router.route('server.ajax.reset-database-password', { server: Pterodactyl.server.uuidShort }),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                },
                data: {
                    'database': $(this).data('id')
                }
            }).done(function (data) {
                block.parent().find('code').html(data);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error(jqXHR);
                var error = 'An error occured while trying to process this request.';
                if (typeof jqXHR.responseJSON !== 'undefined' && typeof jqXHR.responseJSON.error !== 'undefined') {
                    error = jqXHR.responseJSON.error;
                }
                swal({
                    type: 'error',
                    title: 'Whoops!',
                    text: error
                });
            }).always(function () {
                block.find('i').removeClass('fa-spin');
            });
        });
    @endcan
    </script>
@endsection
