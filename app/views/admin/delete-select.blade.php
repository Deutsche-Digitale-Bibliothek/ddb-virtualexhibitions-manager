@extends('layouts.master')

@section('content')
        <div class="jumbotron text-center">
            <h1 style="">Ausstellung Löschen</h1>
            <p>Hier können Sie die Ausstellung auf dem Redaktions- und / oder auf den Ausspielungsservern löschen.</p>
        </div>

        <h1 class="page-header">{{{ $va->title }}}<br>
            <small>{{{ $va->subtitle }}}</small></h1>

        <p>Slug der Ausstellung: <strong>{{ $va->slug }}</strong></p>

        <ul class="list-unstyled">
            <li><span class="glyphicon glyphicon-time" style="color:#666;"></span> Instanz erzeugt am <?php echo date('d.m.Y \u\m H:i:s', strtotime($va->created_at)); ?> Uhr.</li>
            @if ($va->last_unpublished_at && $va->last_unpublished_at > $va->last_published_at)
            <li class="text-danger"><span class="glyphicon glyphicon-time" style="color:#666;"></span> Zuletzt von den Ausspielungsservern gelöscht am: <?php echo date('d.m.Y \u\m H:i:s', strtotime($va->last_unpublished_at)); ?> Uhr.</li>
            @elseif ($va->last_published_at)
            <li class="text-success"><span class="glyphicon glyphicon-time" style="color:#666;"></span> Zuletzt auf den Ausspielungsservern veröffentlicht am: <?php echo date('d.m.Y \u\m H:i:s', strtotime($va->last_published_at)); ?> Uhr.</li>
            @endif
        </ul>

        <div class="panel panel-default">
            <div class="panel-body gina-form">
                {{ Form::open(array(
                    'id' => 'omim-delete-instance',
                    'url' => 'admin/delete',
                    'role' => 'form'))
                }}

                {{ Form::hidden('oid', $va->id) }}
                @if ($errors->any())
                <div id="delete-alert" class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <ul>
                        {{ implode('', $errors->all('<li class="error">:message</li>')) }}
                    </ul>
                </div>
                @endif

                <div id="delete-default-alert" class="alert alert-danger">
                    <ul class="list-unstyled">
                        <li>Beim Löschen werden sowohl Dateien als auch Datenbankeinträge dauerhaft entfernt.</li>
                    </ul>
                </div>
                @if ($va->last_published_at && $va->last_unpublished_at < $va->last_published_at)
                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('del-produktion', 'ok', null, array(
                            'id' => 'omim-delete-instance-production',
                            // 'class' => 'checkbox'
                            ))
                        }}
                        Ausstellung von <strong>Ausspielungsservern</strong> löschen!
                        <span class="help-block">Die Ausstellung wird von den Ausspielungsservern gelöscht.<br>
                            Falls sie die Ausstellung vom Redaktionsserver nicht löschen, können Sie sie später wieder veröffentlichen.</span>
                    </label>
                </div>
                @endif

                <div class="checkbox">
                    <label>
                        {{ Form::checkbox('del-development', 'ok', null, array(
                            'id' => 'omim-delete-instance-development',
                            // 'class' => 'checkbox'
                            ))
                        }}
                        Ausstellung vom <strong>Redaktionsserver</strong>
                        @if ($va->last_published_at && $va->last_unpublished_at < $va->last_published_at)
                            und von den <strong>Ausspielungsservern</strong>
                        @endif
                        löschen!
                        <span class="help-block">Die Ausstellung wird sowohl vom Redaktionsserver, als auch von den Ausspielungsservern gelöscht.<br>
                            Wenn Sie die Ausstellung hier vom Redaktionsserver löschen, können Sie diese auch nicht mehr veröffentlichen!</span>
                    </label>
                </div>

                <div class="submit-group pull-right">
                    {{ Form::button('Löschen', array('class' => 'btn btn-danger', 'type' => 'submit')) }}
                </div>
                {{ Form::close() }}
            </div>
        </div>

@stop
@section('page-bottom')

@stop