@extends('layouts.master')
@section('content')
<div class="jumbotron text-center">
    <h1 style="">Benutzerdefinierte Farbpaletten</h1>
    <p>Hier können Sie benutzerdefinierte Farbpaletten erstellen und bearbeiten.</p>
</div>
<div class="row" style="margin-bottom:32px;">
    <div class="col-md-4">
        <a href="{{ URL::to('admin/colorpalettes-create') }}" class="btn btn-success btn-block">
        <span class="glyphicon glyphicon-plus"></span> Neue Farbpalette erzeugen</a>
    </div>
</div>
<div class="col-xs-12">
    <table class="table">
        <thead>
            <tr>
                <th>Bearbeiten</th>
                <th>Palette</th>
                <th>Farben</th>
            </tr>
        </thead>
        <tbody>
        <?php $currentPalette = ''; ?>
        @foreach ($colorPalettes as $colorPalette)
            @if ($colorPalette['palette'] == 'ddb')
                <?php continue; ?>
            @endif
            @if ($colorPalette['palette'] !== $currentPalette)
                @if ('' !== $currentPalette)
                </td></tr>
                @endif
            <tr>
                <td>
                    <a href="{{ url('/admin/colorpalettes-edit'); }}?palette={{ $colorPalette['palette'] }}" class="btn btn-success btn-block">
                        <span class="glyphicon glyphicon-edit"></span>
                        Bearbeiten
                    </a>
                </td>
                <td>{{$colorPalette['show_name']}}</td>
                <td>
                <?php $currentPalette = $colorPalette['palette']; ?>
            @endif
                <div class="colorpalette-color"
                    style="background-color:{{$colorPalette['hex']}};color:#@if ($colorPalette['type'] === 'dark')fff @else 000 @endif;@if ($colorPalette['menu'] === 1)border-width:3px!important;@else @endif">
                    {{$colorPalette['color']}}
                </div>
        @endforeach
                </td>
            </tr>
        </tbody>
    </table>
</div>
@stop
@section('page-bottom')
<script>
</script>
@stop