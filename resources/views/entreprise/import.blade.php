@extends('layouts.master')

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">Administration</h4><span class="text-muted mt-1 tx-13 mx-2 mb-0">/ Importer des Entreprises</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Importer des Entreprises depuis un fichier Excel</h4>
                </div>
                <div class="card-body">
                    
                    {{-- Section pour afficher les messages de succès ou d'erreur après l'upload --}}
                    @if(session('success'))
                        <div class="alert alert-success font-weight-bold">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger font-weight-bold">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Boîte d'instructions pour l'utilisateur --}}
                    <div class="card card-body border-primary mt-4 mb-4">
                        <h5 class="card-title">Instructions Importantes</h5>
                        <p class="card-text">
                            Veuillez vous assurer que la première ligne de votre fichier Excel contient les en-têtes suivants EXACTEMENT :
                        </p>
                        <code class="text-primary font-weight-bold" style="display: block;">
                            code, rs, activite, GOUV, VILLE, NUM_RUE, RUE, STATUT_JUR, ADR_CNSS, LOCALITE
                        </code>
                    </div>

                    {{-- Le formulaire d'upload --}}
                    <form action="{{ route('admin.entreprises.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Choisir un fichier Excel</label>
                            <input type="file" name="file" class="form-control-file" id="file" required>
                            <small class="form-text text-muted">Fichiers supportés : .xlsx, .xls, .csv.</small>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-upload mr-2"></i>Importer le Fichier
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection