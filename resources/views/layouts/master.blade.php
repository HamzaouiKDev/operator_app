<!DOCTYPE html>
<html lang="en">
    
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Vos autres balises meta, liens CSS, etc. --}}
    @include('layouts.head')
</head>

    <body class="main-body app sidebar-mini">
        <div id="global-loader">
            <img src="{{URL::asset('assets/img/loader.svg')}}" class="loader-img" alt="Loader">
        </div>
        @include('layouts.main-sidebar')        
        <div class="main-content app-content">
            @include('layouts.main-header')            
            <div class="container-fluid">
                @yield('page-header')
                @yield('content')
                @include('layouts.sidebar')
                @include('layouts.models')
                @include('layouts.footer')
            </div>
        </div>
        
        {{-- Le fichier footer-scripts.blade.php charge tous les scripts JS de base --}}
        @include('layouts.footer-scripts')  

        {{-- ======================================================================= --}}
        {{-- ==== LA LIGNE MAGIQUE QUI MANQUAIT ET QUI RÉSOUT TOUT EST ICI ==== --}}
        {{-- Cette ligne récupère le script de notification (et tout autre script "pushé") --}}
        {{-- et l'insère dans la page.                                            --}}
        {{-- ======================================================================= --}}
        @stack('scripts')

    </body>
</html>