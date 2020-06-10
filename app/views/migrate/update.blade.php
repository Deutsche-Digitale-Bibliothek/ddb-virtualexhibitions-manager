@extends('layouts.master')

@section('content')
<div class="well">
    <h1>Update aller Ausstellungen</h1>
</div>

<h2>Aktualisierungen</h2>

<ul>
@foreach ($msgs as $msg)
    <li>{{$msg}}</li>
@endforeach
</ul>

<p>Alle Updates erfolgreich durchgeführt.</p>

<a class="btn btn-success" style="margin: 30px 0;" href="/admin" role="button">
    <span class="glyphicon glyphicon-arrow-left"></span> zurück zur Übersicht
</a>
@stop

@section('page-bottom')
@stop