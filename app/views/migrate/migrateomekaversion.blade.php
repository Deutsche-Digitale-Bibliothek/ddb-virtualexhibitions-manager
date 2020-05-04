@extends('layouts.master')

@section('content')
<div class="well">
    <h1>Update von Omeka in Ausstellungen</h1>
</div>

@if (count($updates) > 0)
    <p>Folgende Aussteluungen wurden aktualisiert.</p>
    <ul>
    @foreach ($updates as $va)
    <li><a href="/{{ $va->slug }}/admin/" target="_blank">{{ $va->title }}</a></li>
    @endforeach
    </ul>
    <p class="strong">Klicken Sie auf die einzelenen Ausstellungen, um die Datenbankmigration von Omeka für die jeweilige Austellung auszufühern.</p>
@else
    <p>Alle Ausstellungen sind aktuell. Keine Änderungen vorgenommen.</p>
@endif

<a class="btn btn-success" style="margin: 30px 0;" href="/admin" role="button">
    <span class="glyphicon glyphicon-arrow-left"></span> zurück zur Übersicht
</a>
@stop

@section('page-bottom')
@stop