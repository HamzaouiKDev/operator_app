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
    {{-- MODIFICATION : Utilisation d'une grille responsive pour un meilleur affichage --}}
    <div class="row">
        <div class="col-lg-3 col-md-6">
            {{-- CARTE POUR L'IMPORT DES ENTREPRISES --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">1. Importer les Entreprises</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    @if(session('success_entreprises'))
                        <div class="alert alert-success">{!! session('success_entreprises') !!}</div>
                    @endif
                    @if(session('error_entreprises'))
                        <div class="alert alert-danger font-weight-bold">{{ session('error_entreprises') }}</div>
                    @endif
                    <form action="{{ route('admin.entreprises.import.store') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                        @csrf
                        <div class="form-group">
                            <label for="file-entreprises">Fichier des entreprises</label>
                            <input type="file" name="file-entreprises" class="form-control-file" id="file-entreprises" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-upload mr-2"></i>Importer</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            {{-- CARTE POUR L'IMPORT DES TÉLÉPHONES --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">2. Importer les Téléphones</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    @if(session('success_telephones'))
                        <div class="alert alert-success">{!! session('success_telephones') !!}</div>
                    @endif
                    @if(session('error_telephones'))
                        <div class="alert alert-danger font-weight-bold">{{ session('error_telephones') }}</div>
                    @endif
                    <form action="{{ route('admin.entreprises.import.telephones') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                        @csrf
                        <div class="form-group">
                            <label for="file-telephones">Fichier des téléphones</label>
                            <input type="file" name="file-telephones" class="form-control-file" id="file-telephones" required>
                        </div>
                        <button type="submit" class="btn btn-success mt-3"><i class="fas fa-phone mr-2"></i>Importer</button>
                    </form>
                </div>
            </div>
        </div>
        
       <div class="col-lg-3 col-md-6">
            {{-- CARTE POUR L'IMPORT DES EMAILS --}}
            <div class="card mb-4">
                <div class="card-header"><h4 class="card-title">3. Importer les Emails</h4></div>
                <div class="card-body d-flex flex-column">
                    @if(session('success_emails'))
                        <div class="alert alert-success">{!! session('success_emails') !!}</div>
                    @endif
                    @if(session('error_emails'))
                        <div class="alert alert-danger font-weight-bold">{{ session('error_emails') }}</div>
                    @endif

                    {{-- NOUVEAU : Affichage des détails des emails ignorés --}}
                    @if(session('skipped_emails') && count(session('skipped_emails')) > 0)
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Détails des emails non importés</h5>
                            <div style="max-height: 200px; overflow-y: auto;">
                                <ul class="list-group">
                                    @foreach(session('skipped_emails') as $skipped)
                                        <li class="list-group-item">
                                            <strong>Email:</strong> {{ $skipped['email'] }} <br>
                                            <strong>ID Entreprise:</strong> {{ $skipped['entident'] ?? 'N/A' }} <br>
                                            <strong class="text-danger">Raison:</strong> {{ $skipped['raison'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.entreprises.import.emails') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                        @csrf
                        <div class="form-group">
                            <label for="file-emails">Fichier des emails</label>
                            <input type="file" name="file-emails" class="form-control-file" id="file-emails" required>
                        </div>
                        <button type="submit" class="btn btn-info mt-3"><i class="fas fa-envelope mr-2"></i>Importer</button>
                    </form>
                </div>
            </div>
        </div>


        <div class="col-lg-3 col-md-6">
            {{-- NOUVEAU : CARTE POUR L'IMPORT DES CONTACTS --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">4. Importer les Contacts</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    
                    @if(session('success_contacts'))
                        <div class="alert alert-success">{!! session('success_contacts') !!}</div>
                    @endif
                    @if(session('error_contacts'))
                        <div class="alert alert-danger font-weight-bold">{{ session('error_contacts') }}</div>
                    @endif

                    <div class="card card-body border-warning mb-4">
                        <h5 class="card-title">Instructions</h5>
                        <p class="card-text">En-têtes requis :</p>
                        <code class="text-warning font-weight-bold" style="display: block; font-size: 0.9rem;">
                           entident, nom, prenom, civilite, fonction, telephone, email
                        </code>
                         <p class="card-text mt-2"><b>Note :</b> Les contacts seront liés via `entident`.</p>
                    </div>

                    <form action="{{ route('admin.contacts.import.store') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                        @csrf
                        <div class="form-group">
                            <label for="file-contacts">Fichier des contacts</label>
                            <input type="file" name="file-contacts" class="form-control-file" id="file-contacts" required>
                            <small class="form-text text-muted">Formats : .xlsx, .xls.</small>
                        </div>
                        <button type="submit" class="btn btn-warning mt-3">
                            <i class="fas fa-users mr-2"></i>Importer
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
