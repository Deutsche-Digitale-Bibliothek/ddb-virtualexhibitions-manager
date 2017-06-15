@extends('layouts.master')
@section('content')
    <h1 class="page-header">Omeka Standard-Benutzer</h1>
    <div class="panel-group" id="userlist" role="tablist" aria-multiselectable="true">
    @foreach ($users as $user)
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="heading-user-{{ $user->id }}">
                <h4 class="panel-title">
                    <a class="collapsed" data-toggle="collapse" data-parent="#userlist" href="#collapse-{{ $user->id }}" aria-expanded="false" aria-controls="collapse-{{ $user->id }}">
                        <span>{{ $user->id }}</span><span> - </span><span>{{{ $user->username }}}</span> - <span>{{{ $user->name }}}</span> - <span>{{{ $user->email }}}</span>
                    </a>
                </h4>
            </div>
            <div id="collapse-{{ $user->id }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-user-{{ $user->id }}">
                <div class="panel-body">
                    <ul class="list-unstyled">
                        <li>Rechte: {{ $user->role }}</li>
                    </ul>
                    <div>
                        <a class="btn btn-primary" href="{{ URL::to('omeka-user/edit') }}/{{ $user->id }}" role="button"><span class="glyphicon glyphicon-pencil"></span> Bearbeiten</a>
                        <a class="btn btn-primary" href="{{ URL::to('omeka-user/chpwd') }}/{{ $user->id }}" role="button"><span class="glyphicon glyphicon-pencil"></span> Passwort ändern</a>
                        <a class="btn btn-danger" href="{{ URL::to('omeka-user/delete') }}/{{ $user->id }}" role="button"><span class="glyphicon glyphicon-remove"></span>  Löschen</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    </div>
@stop