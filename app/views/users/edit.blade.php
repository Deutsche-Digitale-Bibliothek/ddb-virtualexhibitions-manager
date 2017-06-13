@extends('layouts.master')
@section('content')
        <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Benutzer bearbeiten</div>
                </div>
                <div class="panel-body gina-form">
                    {{-- Form::open(array('url' => 'user/register', 'class' => 'form-horizontal', 'role' => 'form')) --}}
                    {{ Form::model($omimuser, array('url' => array('user/edit', $omimuser->id))) }}

                    @if ($errors->any())
                    <div id="login-alert" class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        {{ implode('', $errors->all('<li class="error">:message</li>')) }}
                    </div>
                    @endif
                    <div class="form-group">
                        {{ Form::label('user-create-username', 'Benutzername') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            {{ Form::text('username', null, array(
                            'id' => 'user-create-username', 'placeholder' => 'Benutzername', 'class' => 'form-control')) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('user-create-forname', 'Vorname') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            {{ Form::text('forname', null, array(
                            'id' => 'user-create-forname', 'placeholder' => 'Vorname', 'class' => 'form-control')) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('user-create-surename', 'Nachname') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            {{ Form::text('surename', null, array(
                            'id' => 'user-create-surename', 'placeholder' => 'Nachname', 'class' => 'form-control')) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('user-create-isroot', 'Rootrechte') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-check"></i></span>

                            {{ Form::select('isroot', array('0' => 'Nein', '1' => 'Ja'), null,
                                array('id' => 'user-create-isroot', 'class' => 'form-control')) }}

                        </div>
                    </div>
                    <div class="submit-group pull-right">
                        {{ Form::button('Speichern', array('class' => 'btn btn-success', 'type' => 'submit')) }}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
@stop