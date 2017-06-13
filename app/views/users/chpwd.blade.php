@extends('layouts.master')
@section('content')
        <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Passwort ändern</div>
                </div>
                <div class="panel-body gina-form">
                    {{ Form::model($omimuser, array('url' => array('user/chpwd', $omimuser->id))) }}
                    @if ($errors->any())
                    <div id="login-alert" class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        {{ implode('', $errors->all('<li class="error">:message</li>')) }}
                    </div>
                    @endif
                    <div class="form-group">
                        {{ Form::label('user-chpwd-password', 'Passwort') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            {{ Form::password('password', array(
                                'id' => 'user-chpwd-password',
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