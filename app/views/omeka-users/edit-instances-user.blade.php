@extends('layouts.master')
@section('content')
<div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">Benutzer in allen Ausstellungen bearbeiten</div>
        </div>
        <div class="panel-body gina-form">
            {{ Form::open(array(
                'url' => 'omeka-user/edit-instances-user?oldname=' . urlencode($params['name'])
                    . '&amp;oldusername=' . urlencode($params['username'])
                    . '&amp;oldemail=' . urlencode($params['email']),
                'class' => 'form-horizontal',
                'role' => 'form'
            )) }}
            @if ($errors->any())
            <div id="login-alert" class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                {{ implode('', $errors->all('<li class="error">:message</li>')) }}
            </div>
            @endif
            <div class="form-group">
                {{ Form::label('user-edit-username', 'Benutzername') }}
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                    {{ Form::text('username', $params['username'], array(
                    'id' => 'user-edit-username', 'placeholder' => 'Benutzername', 'class' => 'form-control')) }}
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('user-edit-name', 'Name') }}
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                    {{ Form::text('name', $params['name'], array(
                    'id' => 'user-edit-name', 'placeholder' => 'Name', 'class' => 'form-control')) }}
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('user-edit-email', 'E-Mail') }}
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                    {{ Form::text('email', $params['email'], array(
                    'id' => 'user-edit-email', 'placeholder' => 'E-Mail', 'class' => 'form-control')) }}
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('user-edit-role', 'Rolle') }}
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-check"></i></span>
                    {{ Form::select(
                        'role',
                        array(
                            '' => '',
                            'super' => 'Super-User',
                            'admin' => 'Administrationsbereich',
                            'contributor' => 'Mitarbeiter',
                            'researcher' => 'Forscher'
                        ),
                        null,
                        array('id' => 'user-edit-role', 'class' => 'form-control')) }}
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('user-edit-active', 'Aktiv') }}
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-check"></i></span>
                    {{ Form::select(
                        'active',
                        array(
                            '' => '',
                            1 => 'Ja',
                            0 => 'Nein',
                        ),
                        null,
                        array('id' => 'user-edit-active', 'class' => 'form-control')) }}
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