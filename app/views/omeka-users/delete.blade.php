@extends('layouts.master')
@section('content')
        <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Omeka Standard-Benutzer löschen</div>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Benutzername</th>
                                <th>Name</th>
                                <th>E-Mail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$user->username}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <p>Sind Sie sicher, dass Sie den Benutzer <strong>"{{{ $user->username }}}"</strong> unwiederruflich löschen möchten?</p>
                    <a class="btn btn-success" href="{{ URL::to('omeka-user/list') }}" role="button">Abbrechen</a>
                    <a class="btn btn-danger" href="{{ URL::to('omeka-user/delete') }}/{{ $user->id }}?confirm=ok" role="button"><span class="glyphicon glyphicon-remove"></span>  Löschen</a>
                </div>
            </div>
        </div>
@stop