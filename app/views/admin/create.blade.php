@extends('layouts.master')

@section('content')
        <h1 class="page-header">Neue Omeka-Instanz erzeugen<br>
            <small>Generieren Sie eine neue virtuelle Ausstellung</small></h1>
        <div class="panel panel-default">
            <div class="panel-body gina-form">
                {{ Form::open(array(
                    'id' => 'omim-create-instance',
                    'url' => 'admin/create',
                    // 'class' => 'form-horizontal',
                    'role' => 'form'))
                }}
                @if ($errors->any())
                <div id="create-alert" class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <ul>
                        {{ implode('', $errors->all('<li class="error">:message</li>')) }}
                    </ul>
                </div>
                @endif

                <div class="form-group">
                    {{ Form::label('omim-create-instance-title', 'Titel der Ausstellung') }}
                    {{ Form::text('title', null, array(
                        'id' => 'omim-create-instance-title',
                        'placeholder' => 'Titel der Ausstellung',
                        'class' => 'form-control'))
                    }}
                    <span class="help-block">Geben Sie hier einen Titel an. Dieser wird als Ausstellungstitel angelegt. Sie können den Titel später innerhalb von Omeka ändern.</span>
                </div>
                <div class="form-group">
                    {{ Form::label('omim-create-instance-subtitle', 'Untertitel der Ausstellung') }}
                    {{ Form::text('subtitle', null, array(
                        'id' => 'omim-create-instance-subtitle',
                        'placeholder' => 'Untertitel der Ausstellung',
                        'class' => 'form-control'))
                    }}
                    <span class="help-block">Diese Angabe ist optional. Sie können den Untertitel später innerhalb von Omeka erstellen oder ändern.</span>
                </div>
                <div class="form-group">
                    {{ Form::label('omim-create-instance-slug', 'Slug, Ordnername und Pfad der Ausstellung') }}
                    {{ Form::text('slug', null, array(
                        'id' => 'omim-create-instance-slug',
                        'placeholder' => 'Slug, Ordnername und Pfad der Ausstellung',
                        'class' => 'form-control'))
                    }}
                    <span class="help-block">Geben Sie hier den Ordnernamen der Ausstellung an, unter der die Ausstellung in der URL zu erreichen ist. Dies sollte ein kurzer Name sein, wie "tanz", "maya" etc. Geben Sie hier nur Kleinbuchstaben, Bindestriche und ggf. Zahlen an. Deutsche Umlaute, Sonderzeichen und Leerzeichen sind nicht erlaubt.<br><span class="text-danger">Achtung, diese Angabe kann später nicht verändert werden.</span></span>
                </div>
                <div class="form-group">
                    {{ Form::label('omim-create-instance-language', 'Sprache der Ausstellung') }}
                    {{ Form::select('language', array('de' => 'DE', 'en' => 'EN'), null, array(
                        'id' => 'omim-create-instance-language',
                        'placeholder' => 'Sprache der Ausstellung',
                        'class' => 'form-control'))
                    }}
                    <span class="help-block">Geben Sie hier Die Sprache der Ausstellung an.<br><span class="text-danger">Achtung, diese Angabe kann später nicht verändert werden.</span></span>
                </div>

                <h4>Standardbenutzer wählen</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th><i class="glyphicon glyphicon-check"></th>
                            <th>Benutzername</th>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Rolle</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                {{ Form::checkbox('user[' . $user->id . ']', 1, true) }}
                            </td>
                            <td>{{$user->username}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->role}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="submit-group pull-right">
                    {{ Form::button('Erzeugen', array('class' => 'btn btn-success', 'type' => 'submit')) }}
                </div>
                {{ Form::close() }}
            </div>
        </div>
@stop