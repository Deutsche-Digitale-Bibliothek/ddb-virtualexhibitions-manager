@extends('layouts.master')
@section('content')
<div class="jumbotron text-center">
    <h1 style="">Einverständniserklärung bearbeiten</h1>
    <p>Hier können Sie die Vorlagen für die Einverständniserklärung zur Verwendung von Omeka editieren!</p>
</div>
<div class="col-xs-12">
    {{ Form::open(array('url' => 'admin/consent', 'class' => 'form-horizontal', 'role' => 'form')) }}
    <div class="form-group">
        {{ Form::label('consent_termsofuse', 'Nutzungsbedingungen für Omeka Benutzer') }}
        {{ Form::textarea('consent_termsofuse', $contents['termsofuse'], array('id' => 'consent_termsofuse', 'class' => 'editor_field')) }}
    </div>
    <div class="form-group">
        {{ Form::label('consent_privacypolicy', 'Datenschutzhinweise für Omeka Benutzer') }}
        {{ Form::textarea('consent_privacypolicy', $contents['privacypolicy'], array('id' => 'consent_privacypolicy', 'class' => 'editor_field')) }}
    </div>
    <div class="text-center" style="margin:2rem 0;">
    {{ Form::button('Speichern', array('class' => 'btn btn-success', 'type' => 'submit')) }}
    <a class="btn btn-success" href="{{ url('admin/publishconsent'); }}" role="button"><span class="glyphicon glyphicon-cog"></span> Veröffentlichen</a>
    </div>
    {{ Form::close() }}
</div>
@stop
@section('page-bottom')
<script src="/js/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
  selector: '.editor_field',
  height: 600,
  language: 'de',
  menubar: false,
  branding: false,
  plugins: ['anchor autolink fullscreen code help wordcount link'],
  block_formats: 'Absatz=p; \u00dcberschrift 1=h1; \u00dcberschrift 2=h2; \u00dcberschrift 3=h3; \u00dcberschrift 4=h4; Vorformatiert=pre',
  toolbar: 'undo redo | formatselect | bold italic | link | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent anchor | removeformat code wordcount | fullscreen help'
});
</script>
@stop