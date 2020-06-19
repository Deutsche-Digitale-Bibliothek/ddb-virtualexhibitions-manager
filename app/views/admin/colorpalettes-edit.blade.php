@extends('layouts.master')
@section('html-head')
{{ HTML::style('js/colorpicker/css/bootstrap-colorpicker.min.css') }}
@stop
@section('content')
<div class="jumbotron text-center">
    <h1 style="">Benutzerdefinierte Farbpaletten</h1>
    <p>Erstellen Sie eine benutzerdefinierte Farbpalette!</p>
</div>
<div class="row">
    <div class="col-xs-12">
        {{ Form::open(array(
            'id' => 'omim-colorpalette-edit',
            'url' => 'admin/colorpalettes-edit',
            'role' => 'form'))
        }}
            <div class="panel panel-default">
                <div class="panel-body gina-form">
                    <div class="form-group">
                        <label for="palette_palette_showname">Name der Farbpalette</label>
                        <input type="text" class="form-control" name="palette_palette_showname"
                            id="palette_palette_showname" value="{{$paletteShowName}}">
                        <span class="help-block">
                            Erlaubt sind Buchstaben (ohne Umlaute) und Zahlen
                        </span>
                        <input type="hidden" name="palette_oldpalette" id="palette_oldpalette" value="{{{$palette}}}">
                    </div>
                </div>
            </div>
            <div id="colorRepeaters">
            @foreach ($dbPalette as $pk => $color)
                <div class="panel panel-default palette_color_panel">
                    <div class="panel-body gina-form">
                        <button class="pull-right btn btn-danger" id="palette_color_delete_{{$pk}}"
                            style="margin:-31px -16px 0 0;" title="Farbe löschen" data-delnum="{{$pk}}">
                            <span class="glyphicon glyphicon-remove"></span>
                        </button>
                        <div class="form-group">
                            <label for="palette_color_{{$pk}}">Name der Farbe</label>
                            <input type="text" class="form-control" id="palette_color_{{$pk}}"
                                name="palette[{{$pk}}][color]" value="{{$color['color']}}" required maxlength="128">
                            <span class="help-block">
                                Der Name muss innerhalb der Palette einzigartig sein.
                                Erlaubt sind Kleinbuchstaben a-z (ohne Umlaute), Zahlen sowie "_" und "-"
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="palette_hex_{{$pk}}">Farbwert</label>
                            <div id="colorpicker_{{$pk}}" class="input-group colorpicker-component">
                                <input type="text" value="{{$color['hex']}}" class="form-control" id="palette_hex_{{$pk}}"
                                name="palette[{{$pk}}][hex]" required maxlength="7">
                                <span class="input-group-addon"><i></i></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="palette_type_{{$pk}}">Typ der Farbe</label>
                            <select class="form-control" id="palette_type_{{$pk}}" name="palette[{{$pk}}][type]">
                                <option value="light" @if ($color['type'] == 'light') selected @endif>hell</option>
                                <option value="dark" @if ($color['type'] == 'dark') selected @endif>dunkel</option>
                            </select>
                            <span class="help-block">
                                &quot;hell&quot;: Helle Hintergrundfarbe mit dunkler Schrift.<br>
                                &quot;dunkel&quot;: Dunkle Hintergrundfarbe mit heller Schrift.
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="palette_menu_{{$pk}}">Typ der Farbe</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="palette_menu" id="palette_menu_{{$pk}}" value="{{$pk}}"
                                    @if ($color['menu'] == '1') checked @endif>
                                    Diese Farbe als Farbe für aktive Felder im Navigationsmenü verwenden.
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
            <div class="panel panel-default">
                <div class="panel-body gina-form">
                    <div class="form-group">
                        <button class="btn btn-info btn-block" id="addColor">
                            <span class="glyphicon glyphicon-plus"></span> Farbe hinzufügen
                        </button>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body gina-form">
                    <div class="submit-group pull-right">
                        <button class="btn btn-danger" id="delete_palette" value="{{{$palette}}}">Farbpalette löschen</button>
                        <button class="btn btn-success" type="submit">Speichern</button>
                    </div>
                </div>
            </div>
        {{ Form::close() }}
    </div>
</div>
@stop
@section('page-bottom')
<script src="/js/colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script>
(function ($) {

    'use strict';

    var colorCounter = {{ $paletteCounter }};

    function bindAddColor() {
        $('#addColor').bind('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#colorRepeaters').append(
                '<div class="panel panel-default palette_color_panel">' +
                    '<div class="panel-body gina-form">' +
                        '<button class="pull-right btn btn-danger" id="palette_color_delete_' + colorCounter + '" ' +
                            'style="margin:-31px -16px 0 0;" title="Farbe löschen" data-delnum="' + colorCounter + '">' +
                            '<span class="glyphicon glyphicon-remove"></span> ' +
                        '</button>' +
                        '<div class="form-group">' +
                            '<label for="palette_color_' + colorCounter + '">Name der Farbe</label>' +
                            '<input type="text" class="form-control" id="palette_color_' + colorCounter + '" ' +
                                'name="palette[' + colorCounter + '][color]" required maxlength="128">' +
                            '<span class="help-block">' +
                                'Der Name muss innerhalb der Palette einzigartig sein. ' +
                                'Erlaubt sind Kleinbuchstaben a-z (ohne Umlaute), Zahlen sowie "_" und "-"' +
                            '</span>' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label for="palette_hex_' + colorCounter + '">Farbwert</label>' +
                            '<div id="colorpicker_' + colorCounter + '" class="input-group colorpicker-component">' +
                                '<input type="text" value="#666666" class="form-control" id="palette_hex_' + colorCounter + '" ' +
                                'name="palette[' + colorCounter + '][hex]" required maxlength="7">' +
                                '<span class="input-group-addon"><i></i></span>' +
                            '</div>' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label for="palette_type_' + colorCounter + '">Typ der Farbe</label>' +
                            '<select class="form-control" id="palette_type_' + colorCounter + '" name="palette[' + colorCounter + '][type]">' +
                                '<option value="light">hell</option>' +
                                '<option value="dark">dunkel</option>' +
                            '</select>' +
                            '<span class="help-block">' +
                                '&quot;hell&quot;: Helle Hintergrundfarbe mit dunkler Schrift.<br>' +
                                '&quot;dunkel&quot;: Dunkle Hintergrundfarbe mit heller Schrift.' +
                            '</span>' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label for="palette_menu_' + colorCounter + '">Typ der Farbe</label>' +
                            '<div class="radio">' +
                                '<label>' +
                                    '<input type="radio" name="palette_menu" id="palette_menu_' + colorCounter + '" value="' + colorCounter + '">' +
                                    'Diese Farbe als Farbe für aktive Felder im Navigationsmenü verwenden.' +
                                '</label>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
            $('#colorpicker_' + colorCounter).colorpicker({format: 'hex'});
            $('#palette_color_delete_' + colorCounter).on('click', deleteColor);
            colorCounter++;
        });
    }

    function bindColorPicker() {
        for (var i = 0; i < colorCounter; i++) {
            $('#colorpicker_' + i).colorpicker({format: 'hex'});
        }
    }

    function deleteColor(e)
    {
        e.preventDefault();
        $('#colorpicker_' + $(this).data('delnum')).colorpicker('destroy');
        $(this).parents('.palette_color_panel').remove();
    }

    function bindDeleteColor() {
        for (var i = 0; i < colorCounter; i++) {
            $('#palette_color_delete_' + i).on('click', deleteColor);
        }
    }

    function bindDeletePalette() {
        $('#delete_palette').on('click', function(e) {
            e.preventDefault();
            if (confirm('Sind Sie sicher, dass die komplette Farbpalette gelöscht werden soll?')) {
                $('#omim-colorpalette-edit').append(
                    '<input type="hidden" name="delete_palette" value="' + $(this).val() + '">');
                $('#omim-colorpalette-edit').submit();
            }
        })
    }

    function main() {
        bindColorPicker();
        bindAddColor();
        bindDeleteColor();
        bindDeletePalette();
    }

    $(function() {
        main();
    });

})(jQuery);
</script>
@stop