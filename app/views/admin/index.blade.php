@extends('layouts.master')

@section('content')
        <div class="jumbotron text-center well">
            <h1>Redaktionssystem <small><br>für Virtuelle Ausstellungen der Deutschen&nbsp;Digitalen&nbsp;Bibliothek</small></h1>
            <p><small>Die <a href="https://www.deutsche-digitale-bibliothek.de/content/exhibits/">Liste der veröffentlichten Ausstellungen</a> wird im DDB-Portal als statische Seite gepflegt.</small></p>
        </div>
        <div class="row" style="margin-bottom:32px;">
            <div class="col-md-4">
                <a href="{{ URL::to('admin/create') }}" class="btn btn-success btn-block"><span class="glyphicon glyphicon-plus"></span> Neue Omeka-Instanz erzeugen</a>
            </div>
        </div>

        {{ Form::open(array(
            'id' => 'omim-sort-instance-list',
            'url' => '/admin',
            'method' => 'get',
            'role' => 'form'))
        }}
        <div class="form-group">
            {{ Form::label('omim-sort-instance-list-sort', ' ') }}
            {{ Form::select('sort-list',
                array(
                    'date-asc' => 'Sortieren nach Datum aufsteigend',
                    'date-desc' => 'Sortieren nach Datum absteigend',
                    'title-asc' => 'Sortieren nach Titel aufsteigend',
                    'title-desc' => 'Sortieren nach Titel absteigend',
                    'slug-asc' => 'Sortieren nach Slug aufsteigend',
                    'slug-desc' => 'Sortieren nach Slug absteigend',
                    ),
                Input::get('sort-list'),
                array(
                'id' => 'omim-sort-instance-list-sort',
                'class' => 'form-control'))
            }}
        </div>
        {{ Form::close() }}
        <div class="panel-group" id="instancelist" role="tablist" aria-multiselectable="true">
            <?php $counter = 0; ?>
            @foreach ($omiminstance as $va)
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading-{{ ucfirst($va->slug) }}">
                    <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#instancelist" href="#collapse-{{ ucfirst($va->slug) }}" aria-expanded="false" aria-controls="collapse-{{ ucfirst($va->slug) }}">
                            <span>{{ ucfirst($va->slug) }}</span><span> - </span><span>{{{ $va->title }}}</span>
                        </a>
                    </h4>
                </div>
                <div id="collapse-{{ ucfirst($va->slug) }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-{{ ucfirst($va->slug) }}">
                    <div class="panel-body">
                        <h2>{{{ $va->title }}}<br>
                        <small>{{{ $va->subtitle }}}</small></h2>
                        <ul class="list-unstyled">
                            <li><span class="glyphicon glyphicon-time" style="color:#666;"></span> Instanz erzeugt am <?php echo date('d.m.Y \u\m H:i:s', strtotime($va->created_at)); ?> Uhr.</li>
                            @if ($va->last_unpublished_at && $va->last_unpublished_at > $va->last_published_at)
                            <li class="text-danger"><span class="glyphicon glyphicon-time" style="color:#666;"></span> Zuletzt vom Produktivserver gelöscht am: <?php echo date('d.m.Y \u\m H:i:s', strtotime($va->last_unpublished_at)); ?> Uhr.</li>
                            @elseif ($va->last_published_at)
                            <li class="text-success"><span class="glyphicon glyphicon-time" style="color:#666;"></span> Zuletzt auf Produktivserver veröffentlicht am: <?php echo date('d.m.Y \u\m H:i:s', strtotime($va->last_published_at)); ?> Uhr.</li>
                            @endif
                        </ul>
                        <div>
                            <a class="btn btn-info" href="/{{ $va->slug }}" role="button"><span class="glyphicon glyphicon-search"></span> zur Ausstellung</a>
                            <a class="btn btn-primary" href="/{{ $va->slug }}/admin" role="button"><span class="glyphicon glyphicon-pencil"></span> Administrieren</a>
                            <a class="btn btn-warning" href="{{ URL::to('migratelatest') }}?oid={{ $va->id }}" role="button"><span class="glyphicon glyphicon-wrench"></span> Migrieren</a>
                            <a class="btn btn-success" href="{{ URL::to('admin/publish') }}?oid={{ $va->id }}" role="button"><span class="glyphicon glyphicon-cog"></span> Veröffentlichen</a>
                            <a class="btn btn-danger" href="{{ URL::to('admin/delete') }}?oid={{ $va->id }}" role="button"><span class="glyphicon glyphicon-remove"></span>  Löschen</a>
                            <a class="btn btn-primary" href="{{ URL::to('admin/download') }}?oid={{ $va->id }}" role="button"><span class="glyphicon glyphicon-download"></span> Download</a>
                            @if ($va->version != '1.0.0')
                            <a class="btn btn-warning" href="{{ URL::to('migrate') }}?oid={{ $va->id }}" role="button"><span class="glyphicon glyphicon-refresh"></span>  Migrieren</a>
                            @endif
                            @if ($va->last_published_at && $va->last_unpublished_at < $va->last_published_at)
                            @foreach ($configOmim['remote'] as $remoteSrvNo => $remoteSrvConfig)
                            <a class="btn btn-info pull-right" href="{{ $remoteSrvConfig['production']['http']['url'] }}/{{ $va->slug }}" role="button" target="_blank" style="margin-left:5px;"><span class="glyphicon glyphicon-search"></span> Produktionsserver Nr. {{ $remoteSrvNo }}</a>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <?php $counter++; ?>
            @endforeach
        </div>
@stop

@section('page-bottom')
    <script>
    $(document).ready(function() {
        $('select').change(function () {
            $(this).closest('form').submit();
        });

        $('#instancelist').on('shown.bs.collapse', function(e) {
            localStorage.setItem('lastTab', e.target.id);
        });

        $('#instancelist').on('hidden.bs.collapse', function(e) {
            localStorage.removeItem('lastTab');
        });

        var lastTab = localStorage.getItem('lastTab');
        if (lastTab) {
            // $('#' + localStorage.getItem('lastTab')).collapse('show');
            $('#' + lastTab).addClass('in');
            $('a.collapsed', $('#' + lastTab).prev()).removeClass('collapsed');
        }
    });
    </script>
@stop