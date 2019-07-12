@extends('layouts.master')
@section('content')
<div class="jumbotron text-center">
    <h1 style="">Impressum bearbeiten</h1>
    <p>Hier können Sie die Vorlage für das Impressum aller Ausstellungen editieren!</p>
</div>
<div class="col-xs-12">
    {{ Form::open(array('url' => 'admin/imprint', 'class' => 'form-horizontal', 'role' => 'form')) }}
    <div class="form-group">
        {{ Form::label('imprint_litfass', 'Impressum für Ausstellungen "Litfaß Partner Standard"') }}
        {{ Form::textarea('imprint_litfass', $contents['litfass'], array('id' => 'imprint_litfass', 'class' => 'editor_field')) }}
    </div>
    <div class="form-group">
        {{ Form::label('imprint_litfass', 'Impressum für Ausstellungen "Litfaß Partner Featured"') }}
        {{ Form::textarea('imprint_litfass_featured', $contents['litfass_featured'], array('id' => 'imprint_litfass_featured', 'class' => 'editor_field')) }}
    </div>
    <div class="form-group">
        {{ Form::label('imprint_litfass', 'Impressum für Ausstellungen "Litfaß DDB Exhibition"') }}
        {{ Form::textarea('imprint_litfass_ddb', $contents['litfass_ddb'], array('id' => 'imprint_litfass_ddb', 'class' => 'editor_field')) }}
    </div>
    <div class="text-center" style="margin:2rem 0;">
    {{ Form::button('Speichern', array('class' => 'btn btn-success', 'type' => 'submit')) }}
    </div>
    {{ Form::close() }}
</div>
@stop
@section('page-bottom')
<script src="/js/ckeditor/ckeditor.js"></script>
<script src="/js/ckeditor/translations/de.js"></script>
<script>
var editorFields = document.querySelectorAll('.editor_field');
for (var i = 0; i < editorFields.length; i++) {
    ClassicEditor
        .create(editorFields[i], {
            toolbar: [ 'undo', 'redo', '|', 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
            language: 'de'
            })
        .catch(error => {
            console.error(error);
        });
}
{{-- ClassicEditor
    .create(
        document.querySelector('#imprint'), {
            toolbar: [ 'undo', 'redo', '|', 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
            language: 'de'
        }
    )
    .catch(error => {
        console.error(error);
    }); --}}
</script>
@stop