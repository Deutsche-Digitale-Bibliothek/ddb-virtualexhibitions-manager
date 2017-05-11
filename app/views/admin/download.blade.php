@extends('layouts.master')

@section('content')
        <div class="jumbotron text-center">
            <h1 style="">Download Ausstellung</h1>
            <p>Hier können Sie die Ausstellung herunterladen.</p>
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

        <div class="panel panel-default">
            <div class="panel-body gina-form">
                <ul>
                    <li><a href="/downloads/files-{{ $va->slug }}-{{ $startPublishTime }}.tar.gz">Ausstellungsdateien</a></li>
                    <li><a href="/downloads/db-{{ $va->slug }}-{{ $startPublishTime }}.tar.gz">Datenbankdateien</a></li>
                </ul>
            </div>
        </div>

@stop
@section('page-bottom')

@stop