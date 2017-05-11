@extends('layouts.master')
@section('content')

            <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="panel-title">Anmeldung</div>
                    </div>
                    <div class="panel-body gina-form">
                        {{ Form::open(array('url' => 'user/login', 'class' => 'form-horizontal', 'role' => 'form')) }}

                        @if ($errors->any())
                        <div id="login-alert" class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <ul>
                                {{ implode('', $errors->all('<li class="error">:message</li>')) }}
                            </ul>
                        </div>
                        @endif

                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            {{ Form::text('username', null, array('placeholder' => 'Benutzername', 'class' => 'form-control')) }}
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            {{ Form::password('password', array('placeholder' => 'Passwort', 'class' => 'form-control')) }}
                        </div>
                        <div class="submit-group pull-right">
                            {{ Form::button('Anmelden', array('class' => 'btn btn-success', 'type' => 'submit')) }}
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>

@stop