@extends('layouts.master')
@section('content')
    <h1 class="page-header">Benutzer in allen Ausstellungen bearbeiten</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Benutzername</th>
                <th>Name</th>
                <th>E-Mail</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($unipueUsers as $key => $user)
            <?php
            $reset = false;
            foreach ($omimOmekaUsers as $omimOmekaUser):
                if ($omimOmekaUser->username == $user->username
                    && $omimOmekaUser->email == $user->email
                    && $omimOmekaUser->name == $user->name):
                    $reset = true;
                    break;
                endif;
            endforeach;
            ?>
                <tr>
                    <td>{{$user->username}}</td>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>
                        <a href="{{ URL::to('omeka-user/edit-instances-user') }}?username={{ urlencode($user->username) }}&amp;name={{ urlencode($user->name) }}&amp;email={{ urlencode($user->email) }}"
                            class="btn btn-primary" data-toggle="tooltip"
                            data-placement="auto right" title="Benutzerdaten bearbeiten">
                            <span class="glyphicon glyphicon-pencil"></span>
                        </a>
                        <a href="{{ URL::to('omeka-user/chpwd-instances-user') }}?username={{ urlencode($user->username) }}&amp;name={{ urlencode($user->name) }}&amp;email={{ urlencode($user->email) }}"
                            class="btn btn-primary" data-toggle="tooltip"
                            data-placement="auto right" title="Passwort ändern">
                            <span class="glyphicon glyphicon-lock"></span>
                        </a>
                        @if ($reset === true)
                        <a href="{{ URL::to('omeka-user/reset-instances-user') }}?username={{ urlencode($user->username) }}&amp;name={{ urlencode($user->name) }}&amp;email={{ urlencode($user->email) }}"
                            class="btn btn-danger" data-toggle="tooltip"
                            data-placement="auto right" title="Auf Daten vom Standardbenutzer zurücksetzen (Passwort und Rolle)">
                            <span class="glyphicon glyphicon-repeat"></span>
                        </a>
                        @endif
                    </td>
                </tr>
        @endforeach
        </tbody>
    </table>
@stop