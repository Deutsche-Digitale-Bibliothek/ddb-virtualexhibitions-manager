@extends('layouts.master')
@section('content')
        <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Benutzerpasswort in allen Ausstellungen ändern</div>
                </div>
                <div class="panel-body gina-form">
                    {{ Form::open(array(
                        'url' => 'omeka-user/chpwd-instances-user?name=' . urlencode($params['name'])
                            . '&amp;username=' . urlencode($params['username'])
                            . '&amp;email=' . urlencode($params['email']),
                        'class' => 'form-horizontal',
                        'role' => 'form'
                    )) }}
                    @if ($errors->any())
                    <div id="login-alert" class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        {{ implode('', $errors->all('<li class="error">:message</li>')) }}
                    </div>
                    @endif
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
                                <td>{{$params['username']}}</td>
                                <td>{{$params['name']}}</td>
                                <td>{{$params['email']}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group">
                        {{ Form::label('instances-user-user-chpwd-password', 'Passwort') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            {{ Form::password('password', array(
                                'id' => 'instances-user-user-chpwd-password',
                                'placeholder' => 'neues Passwort',
                                'class' => 'form-control'
                            )) }}
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