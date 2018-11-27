@extends('layouts.master')

@section('content')
<div class="well">
    <h1>Migration</h1>
    <dl class="dl-horizontal">
        <dt>Ausstellungstitel</dt>
        <dd>{{ $va->title }}</dd>
        <dt>Untertitel</dt>
        <dd>{{ $va->subtitle }}</dd>
        <dt>Slug</dt>
        <dd>{{ $va->slug }}</dd>
        <dt>Sprache</dt>
        <dd>{{ $va->language }}</dd>
        <dt>ID</dt>
        <dd>{{ $va->id }}</dd>
    </dl>
</div>
<h2>Änderungen der Datenbank</h2>
@foreach ($msg as $area => $areamsgs)
    <h3>{{ $area }}</h3>
    <ul>
        @foreach ($areamsgs as $areamsg)
        <li>{{ $areamsg }}</li>
        @endforeach
    </ul>
@endforeach
<a class="btn btn-success" style="margin: 30px 0;" href="/admin" role="button"><span class="glyphicon glyphicon-arrow-left"></span> zurück zur Übersicht</a>
@stop

@section('page-bottom')
@stop