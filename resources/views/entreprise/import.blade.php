@extends('layouts.master')

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">Administration</h4><span class="text-muted mt-1 tx-13 mx-2 mb-0">/ Importer des Données</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Utilisation de "card-deck" pour un layout plus robuste et égalisé --}}
    <div class="card-deck">

        {{-- CARTE POUR L'IMPORT DES ENTREPRISES --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">1. Importer les Entreprises</h4>
            </div>
            <div class="card-body d-flex flex-column">
                
                {{-- Messages pour l'import des entreprises --}}
                @if(session('success_entreprises'))
                    <div class="alert alert-success">
                        {!! session('success_entreprises') !!}
                    </div>
                @endif
                @if(session('error_entreprises'))
                    <div class="alert alert-danger font-weight-bold">{{ session('error_entreprises') }}</div>
                @endif

                <div class="card card-body border-primary mb-4">
                    <h5 class="card-title">Instructions</h5>
                    <p class="card-text">
                        En-têtes requis :
                    </p>
                    <code class="text-primary font-weight-bold" style="display: block; font-size: 0.9rem;">
                       <b>entident</b>, <b>rs</b>, nat09_2023...
                    </code>
                </div>

                <form action="{{ route('admin.entreprises.import.store') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                    @csrf
                    <div class="form-group">
                        <label for="file-entreprises">Fichier des entreprises</label>
                        <input type="file" name="file-entreprises" class="form-control-file" id="file-entreprises" required>
                        <small class="form-text text-muted">Formats : .xlsx, .xls.</small>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="fas fa-upload mr-2"></i>Importer
                    </button>
                </form>
            </div>
        </div>

        {{-- CARTE POUR L'IMPORT DES TÉLÉPHONES --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">2. Importer les Téléphones</h4>
            </div>
            <div class="card-body d-flex flex-column">
                
                {{-- Messages pour l'import des téléphones --}}
                @if(session('success_telephones'))
                    <div class="alert alert-success">
                        {!! session('success_telephones') !!}
                    </div>
                @endif
                @if(session('error_telephones'))
                    <div class="alert alert-danger font-weight-bold">{{ session('error_telephones') }}</div>
                @endif

                <div class="card card-body border-success mb-4">
                    <h5 class="card-title">Instructions</h5>
                    <p class="card-text">
                        En-têtes requis :
                    </p>
                    <code class="text-success font-weight-bold" style="display: block; font-size: 0.9rem;">
                       <b>entident</b>, <b>telephone</b>, source
                    </code>
                    <p class="card-text mt-2">
                       <b>Note :</b> Importez les entreprises d'abord.
                    </p>
                </div>

                <form action="{{ route('admin.entreprises.import.telephones') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                    @csrf
                    <div class="form-group">
                        <label for="file-telephones">Fichier des téléphones</label>
                        <input type="file" name="file-telephones" class="form-control-file" id="file-telephones" required>
                        <small class="form-text text-muted">Formats : .xlsx, .xls.</small>
                    </div>
                    <button type="submit" class="btn btn-success mt-3">
                        <i class="fas fa-phone mr-2"></i>Importer
                    </button>
                </form>
            </div>
        </div>
        
        {{-- CARTE POUR L'IMPORT DES EMAILS --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">3. Importer les Emails</h4>
            </div>
            <div class="card-body d-flex flex-column">

                {{-- Messages pour l'import des emails --}}
                @if(session('success_emails'))
                    <div class="alert alert-success">
                        {!! session('success_emails') !!}
                    </div>
                @endif
                @if(session('error_emails'))
                    <div class="alert alert-danger font-weight-bold">{{ session('error_emails') }}</div>
                @endif

                <div class="card card-body border-info mb-4">
                    <h5 class="card-title">Instructions</h5>
                    <p class="card-text">
                        En-têtes requis :
                    </p>
                    <code class="text-info font-weight-bold" style="display: block; font-size: 0.9rem;">
                       <b>entident</b>, <b>email</b>, source
                    </code>
                    <p class="card-text mt-2">
                       <b>Note :</b> Les emails seront liés aux entreprises existantes.
                    </p>
                </div>

                <form action="{{ route('admin.entreprises.import.emails') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                    @csrf
                    <div class="form-group">
                        <label for="file-emails">Fichier des emails</label>
                        <input type="file" name="file-emails" class="form-control-file" id="file-emails" required>
                        <small class="form-text text-muted">Formats : .xlsx, .xls.</small>
                    </div>
                    <button type="submit" class="btn btn-info mt-3">
                        <i class="fas fa-envelope mr-2"></i>Importer
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
