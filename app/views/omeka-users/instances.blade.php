@extends('layouts.master')
@section('content')
    <h1 class="page-header">Benutzer in Ausstellungen</h1>
    <div class="panel-group" id="instancelist" role="tablist" aria-multiselectable="true">
    {{ Form::open(array('url' => 'omeka-user/omeka-instances', 'class' => 'form-horizontal', 'role' => 'form')) }}
    @foreach ($instances as $va)
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="heading-{{ ucfirst($va->slug) }}">
                <h4 class="panel-title">
                    <a class="collapsed" data-toggle="collapse" data-parent="#instancelist"
                        href="#collapse-{{ ucfirst($va->slug) }}" aria-expanded="false"
                        aria-controls="collapse-{{ ucfirst($va->slug) }}">
                        <span>{{ ucfirst($va->slug) }}</span>
                        <span> - </span><span>{{{ $va->title }}}</span>
                    </a>
                </h4>
            </div>
            <div id="collapse-{{ ucfirst($va->slug) }}" class="panel-collapse collapse"
                role="tabpanel" aria-labelledby="heading-{{ ucfirst($va->slug) }}">
                <div class="panel-body">
                    <h3>{{{ $va->title }}}<br>
                    <small>{{{ $va->subtitle }}}</small></h3>
                    @if (isset($users[$va->id]) && !empty($users[$va->id]))
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Benutzername</th>
                                <th>Name</th>
                                <th>E-Mail</th>
                                <th>Rolle</th>
                                <th>Aktiv</th>
                            </tr>
                        </thead>
                        @foreach ($users[$va->id] as $user)
                        <tbody>
                            <tr>
                                <td>{{$user->id}}</td>
                                <td>{{$user->username}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{ Form::select('exh[' . $va->id . '][' . $user->id . '][role]',
                                    array(
                                        'super' => 'Super-User',
                                        'admin' => 'Administrationsbereich',
                                        'contributor' => 'Mitarbeiter',
                                        'researcher' => 'Forscher'
                                    ),
                                    $user->role,
                                    array(
                                        'id' => 'user-edit-role_' . $va->id . '_' . $user->id,
                                        'class' => 'form-control')) }}
                                </td>
                                <td>{{ Form::select('exh[' . $va->id . '][' . $user->id . '][active]',
                                    array(
                                        '1' => 'Ja',
                                        '0' => 'Nein'
                                    ),
                                    $user->active,
                                    array(
                                        'id' => 'user-edit-active_' . $va->id . '_' . $user->id,
                                        'class' => 'form-control')) }}
                                </td>
                            </tr>
                        </tbody>
                        @endforeach
                    </table>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
        <div class="submit-group pull-right" style="margin:30px 0;">
            {{ Form::button('Änderungen Speichern', array('class' => 'btn btn-warning', 'type' => 'submit')) }}
        </div>
    {{ Form::close() }}
    </div>
    <?php // var_dump($users); ?>
@stop