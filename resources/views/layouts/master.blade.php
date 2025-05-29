<!DOCTYPE html>
<html lang="en">
	
	<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">  {{-- ✅ VÉRIFIEZ CETTE LIGNE --}}

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Vos autres balises meta, liens CSS, etc. --}}
    @include('layouts.head') {{-- Si vous incluez votre CSS ici --}}
</head>

	<body class="main-body app sidebar-mini">
		<!-- Loader -->
		<div id="global-loader">
			<img src="{{URL::asset('assets/img/loader.svg')}}" class="loader-img" alt="Loader">
		</div>
		<!-- /Loader -->
		@include('layouts.main-sidebar')		
		<!-- main-content -->
		<div class="main-content app-content">
			@include('layouts.main-header')			
			<!-- container -->
			<div class="container-fluid">
				@yield('page-header')
				@yield('content')
				@include('layouts.sidebar')
				@include('layouts.models')
            	@include('layouts.footer')
				@include('layouts.footer-scripts')	
	</body>
</html>