@extends('layouts.master')

@section('content')
        <div class="jumbotron text-center">
            <h1 style="">Ausstellungen Veröffentlichen</h1>
            <p>Hier können Sie Ausstellung auf die Ausspielungsserver übertragen!</p>
        </div>
        <?php  // var_dump($configPublish); ?>
<!--         <div class="panel-group" id="instancelist" role="tablist" aria-multiselectable="true">
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
                        <div class="list-group">
                            @if ($va->last_unpublished_at && $va->last_unpublished_at > $va->last_published_at)
                            Zuletzt von den Ausspielungsservern gelöscht am: <?php echo date('d.m.Y \u\m H:i:s', strtotime($va->last_unpublished_at)); ?> Uhr
                            @elseif ($va->last_published_at)
                            Zuletzt auf Ausspielungsservern veröffentlicht am: <?php echo date('d.m.Y \u\m H:i:s', strtotime($va->last_published_at)); ?> Uhr
                            @endif
                        </div>
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