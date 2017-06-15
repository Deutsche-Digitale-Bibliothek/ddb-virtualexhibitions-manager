@extends('layouts.master')
@section('content')
        <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Omim Benutzer löschen</div>
                </div>
                <div class="panel-body">
                <table class="table">
                        <thead>
                            <tr>
                                <th>Benutzername</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$omimuser->username}}</td>
                                <td>{{$omimuser->forname}} {{$omimuser->surename}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <p>Sind Sie sicher, dass Sie den Benutzer <strong>"{{{ $omimuser->username }}}"</strong> unwiederruflich löschen möchten?</p>
                    <a class="btn btn-success" href="{{ URL::to('user/list') }}" role="button">Abbrechen</a>
                    <a class="btn btn-danger" href="{{ URL::to('user/delete') }}/{{ $omimuser->id }}?confirm=ok" role="button"><span class="glyphicon glyphicon-remove"></span>  Löschen</a>
                </div>
            </div>
        </div>
@stop