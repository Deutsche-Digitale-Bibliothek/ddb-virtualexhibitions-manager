@extends('layouts.master')
@section('content')
        <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Omeka Standard-Benutzer erstellen</div>
                </div>
                <div class="panel-body gina-form">
                    {{ Form::open(array('url' => 'omeka-user/create', 'class' => 'form-horizontal', 'role' => 'form')) }}

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
                        {{ Form::label('user-create-name', 'Name') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            {{ Form::text('name', null, array(
                            'id' => 'user-create-name', 'placeholder' => 'Name', 'class' => 'form-control')) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('user-create-email', 'E-Mail') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                            {{ Form::text('email', null, array(
                            'id' => 'user-create-email', 'placeholder' => 'E-Mail', 'class' => 'form-control')) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('user-create-password', 'Passwort') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            {{ Form::password('password', array(
                            'id' => 'user-create-password', 'placeholder' => 'Passwort', 'class' => 'form-control')) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('user-create-role', 'Rolle') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-check"></i></span>
                            {{ Form::select(
                                'role',
                                array(
                                    'super' => 'Super-User',
                                    'admin' => 'Administrationsbereich',
                                    'contributor' => 'Mitarbeiter',
                                    'researcher' => 'Forscher'
                                ),
                                null,
                                array('id' => 'user-create-role', 'class' => 'form-control')) }}
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