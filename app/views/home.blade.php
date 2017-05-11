@extends('layouts.master')
@section('content')
<!--         <div class="jumbotron text-center">
            <h1>Redaktionssystem <small><br>für Virtuelle Ausstellungen der Deutschen&nbsp;Digitalen&nbsp;Bibliothek</small></h1>
            <p><small>Die <a href="https://www.deutsche-digitale-bibliothek.de/content/exhibits/">Liste der veröffentlichten Ausstellungen</a> wird im DDB-Portal als statische Seite gepflegt.</small></p>
        </div>

        {{ Form::open(array(
            'id' => 'omim-sort-instance-list',
            'url' => '',
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
                        <p><span class="glyphicon glyphicon-time" style="color:#666;"></span> Instanz erzeugt am <?php echo date('d.m.Y \u\m H:i:s', strtotime($va->created_at)); ?> Uhr</p>
                            <a class="btn btn-info" href="/{{ $va->slug }}" role="button"><span class="glyphicon glyphicon-search"></span> zur Ausstellung</a>
                            <a class="btn btn-primary" href="/{{ $va->slug }}/admin" role="button"><span class="glyphicon glyphicon-pencil"></span> Administrieren</a>
                    </div>
                </div>
            </div>
            <?php $counter++; ?>
            @endforeach
        </div> -->
@stop
@section('page-bottom')
    <script>
    // $(document).ready(function() {
    //     $('select').change(function () {
    //         $(this).closest('form').submit();
    //     });
    //     $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    //         localStorage.setItem('lastTab', $(e.target).attr('href'));
    //     });

    //     var lastTab = localStorage.getItem('lastTab');
    //     if (lastTab) {
    //         $('ul.nav-pills').children().removeClass('active');
    //         $('a[href='+ lastTab +']').parents('li:first').addClass('active');
    //         $('div.tab-content').children().removeClass('active');
    //         $(lastTab).addClass('active');
    //     }
    // });

    </script>
@stop