@extends('layouts.master')

@section('content')
<h1>Migration der Ausstellung zur aktuellen Version</h1>
<dl>
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
@foreach ($msg as $area => $areamsgs)
    <h3>{{ $area }}</h3>
    <ul>
        @foreach ($areamsgs as $areamsg)
        <li>{{ $areamsg }}</li>
        @endforeach
    </ul>
@endforeach
<a class="btn btn-success" href="/admin" role="button"><span class="glyphicon glyphicon-arrow-left"></span> zurück zur Übersicht</a>
@stop

@section('page-bottom')
@stop