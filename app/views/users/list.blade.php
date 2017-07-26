@extends('layouts.master')
@section('content')
    <h1 class="page-header">Omim Benutzer</h1>
    <div class="panel-group" id="userlist" role="tablist" aria-multiselectable="true">
    @foreach ($omimusers as $omimuser)
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="heading-user-{{ $omimuser->id }}">
                <h4 class="panel-title">
                    <a class="collapsed" data-toggle="collapse" data-parent="#userlist" href="#collapse-{{ $omimuser->id }}" aria-expanded="false" aria-controls="collapse-{{ $omimuser->id }}">
                        <span>{{ $omimuser->id }}</span><span> - </span><span>{{{ $omimuser->username }}}</span>@if (!empty($omimuser->forname) || !empty($omimuser->surename)) - <span>{{{ $omimuser->forname }}} {{{ $omimuser->surename }}}</span>@endif
                    </a>
                </h4>
            </div>
            <div id="collapse-{{ $omimuser->id }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-user-{{ $omimuser->id }}">
                <div class="panel-body">
                    <ul class="list-unstyled">
                        <li>Adminstratorenrechte: @if ($omimuser->isroot == '1') Ja @else Nein @endif</li>
                    </ul>
                    <div>
                        <a class="btn btn-primary" href="{{ URL::to('user/edit') }}/{{ $omimuser->id }}" role="button"><span class="glyphicon glyphicon-pencil"></span> Bearbeiten</a>
                        <a class="btn btn-primary" href="{{ URL::to('user/chpwd') }}/{{ $omimuser->id }}" role="button"><span class="glyphicon glyphicon-pencil"></span> Passwort ändern</a>
                        <a class="btn btn-danger" href="{{ URL::to('user/delete') }}/{{ $omimuser->id }}" role="button"><span class="glyphicon glyphicon-remove"></span>  Löschen</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    </div>
@stop