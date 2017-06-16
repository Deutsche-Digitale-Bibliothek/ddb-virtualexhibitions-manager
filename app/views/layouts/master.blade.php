<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Omeka Multi-Instanz Manager</title>
        {{ HTML::style('css/default.css') }}
        @section('html-head')
        @show
    </head>
    <body>
        @section('pre-header')
        @show
        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">Omeka Multi-Instanz Manager</a>
                </div>
                <div class="collapse navbar-collapse">
                    @if (Auth::user())
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Administration</a>
                            <ul class="dropdown-menu" role="menu">
                                <li{{ Request::is('admin') ? ' class="active"' : '' }}>
                                    {{ HTML::link('admin', 'Übersicht') }}</li>
                                @if (Auth::user()->isroot == 1)
                                <li{{ Request::is('admin/create') ? ' class="active"' : '' }}>
                                    {{ HTML::link('admin/create', 'Neue Omeka-Instanz erzeugen') }}</li>
                                @endif
                            </ul>
                        </li>
                        @if (Auth::user()->isroot == 1)
                        <li class="dropdown{{ Request::is('user/*') ? ' active"' : '"' }}>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Omim Benutzer</a>
                            <ul class="dropdown-menu" role="menu">
                                <li{{ Request::is('user/register') ? ' class="active"' : '' }}>
                                    {{ HTML::link('user/register', 'Omim Benutzer hinzufügen') }}</li>
                                <li{{ Request::is('user/list') ? ' class="active"' : '' }}>
                                    {{ HTML::link('user/list', 'Omim Benutzer verwalten') }}</li>
                            </ul>
                        </li>
                        <li class="dropdown{{ Request::is('omeka-user/*') ? ' active"' : '"' }}>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Omeka Benutzer</a>
                            <ul class="dropdown-menu" role="menu">
                                <li{{ Request::is('omeka-user/create') ? ' class="active"' : '' }}>
                                    {{ HTML::link('omeka-user/create', 'Omeka Standard-Benutzer hinzufügen') }}</li>
                                <li{{ Request::is('omeka-user/list') ? ' class="active"' : '' }}>
                                    {{ HTML::link('omeka-user/list', 'Omeka Standard-Benutzer verwalten') }}</li>
                                <li{{ Request::is('omeka-user/omeka-instances') ? ' class="active"' : '' }}>
                                    {{ HTML::link('omeka-user/omeka-instances', 'Benutzer in Ausstellungen bearbeiten') }}</li>
                                <li{{ Request::is('omeka-user/edit-instance-users') ? ' class="active"' : '' }}>
                                    {{ HTML::link('omeka-user/edit-instance-users', 'Benutzer aller Ausstellungen bearbeiten') }}</li>
                            </ul>
                        </li>
                        @endif
                    </ul>
                    @endif
                    <ul class="nav navbar-nav pull-right">
                        <!-- <li class="divider-vertical"></li> -->
                        @if (Auth::user())
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> {{ !empty(Auth::user()->forname)? Auth::user()->forname . ' ' . Auth::user()->surename : Auth::user()->username }} <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    {{ HTML::link('user/logout', 'Abmelden') }}
                                </li>
<!--                                 <li>
                                    <a href="#"><i class="icon-edit"></i> Profil bearbeiten</a>
                                </li> -->
                            </ul>
                        @else
                        <li{{ Request::is('user/login') ? ' class="active"' : '' }}>
                            {{ HTML::link('user/login', 'Anmelden') }}
                        @endif
                        </li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
        @section('pre-container')
        @show
        <div class="container container-top">
            @if (Session::has('error-message'))
            <div id="error-messages" class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                {{ Session::get('error-message') }}
            </div>
            @endif
            @if (Session::has('success-message'))
            <div id="success-messages" class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                {{ Session::get('success-message') }}
            </div>
            @endif
            <?php

                /**
                 * Debug
                 */

                // echo $environment = App::environment();
                // echo '<br>' . gethostname();
            ?>
            @yield('content')
        </div>
        @section('footer')
        @show
        {{ HTML::script('js/main.js') }}
        <script>
            $(function() {
                $('[data-toggle="tooltip"]').tooltip()
            });
        </script>
        @section('page-bottom')
        @show
    </body>
</html>