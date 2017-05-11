@extends('layouts.master')

@section('content')
        <div class="jumbotron text-center">
            <h1 style="">Ausstellungen Veröffentlichen</h1>
            <p>Hier können Sie die Ausstellung auf den Produktionsserver übertragen, d.h. live schalten und damit publik machen!</p>
        </div>

        <h1 class="page-header">{{{ $va->title }}}<br>
            <small>{{{ $va->subtitle }}}</small></h1>

        <p>Slug der Ausstellung: <strong>{{ $va->slug }}</strong></p>

        <ul class="list-unstyled">
            <li><span class="glyphicon glyphicon-time" style="color:#666;"></span> Instanz erzeugt am <?php echo date('d.m.Y \u\m H:i:s', strtotime($va->created_at)); ?> Uhr.</li>
            @if ($va->last_unpublished_at && $va->last_unpublished_at > $va->last_published_at)
            <li class="text-danger"><span class="glyphicon glyphicon-time" style="color:#666;"></span> Zuletzt vom Produktivserver gelöscht am: <?php echo date('d.m.Y \u\m H:i:s', strtotime($va->last_unpublished_at)); ?> Uhr.</li>
            @elseif ($va->last_published_at)
            <li class="text-success"><span class="glyphicon glyphicon-time" style="color:#666;"></span> Zuletzt auf Produktivserver veröffentlicht am: <?php echo date('d.m.Y \u\m H:i:s', strtotime($va->last_published_at)); ?> Uhr.</li>
            @endif
        </ul>

        @if ($va->last_published_at && $va->last_unpublished_at < $va->last_published_at)
        <div class="alert alert-warning" role="alert">
            Die Ausstellung ist bereits veröffentlicht. Wenn Sie sie jetzt noch mal veröffentlichen, überschreiben Sie alle Daten der vorhandenen Ausstellung!
        </div>
        @endif

        <div class="row" style="margin-bottom:32px;">
            <div class="col-md-4 col-md-offset-4">
                <a href="{{ URL::to('admin/publish') }}?oid={{ $va->id }}&amp;confirm=ok" class="btn btn-success btn-lg btn-block"><span class="glyphicon glyphicon-cog"></span> Ausstellung jetzt veröffentlichen</a>
            </div>
        </div>


@stop
@section('page-bottom')

@stop