@extends('layouts.master')

@section('css')
    {{-- Réutilisez la même section CSS que votre page de rendez-vous --}}
    {{-- ... collez ici la section @section('css') de votre fichier indexRDV.blade.php ... --}}
    <style>
        /* ... collez ici tout le contenu de la balise <style> de votre fichier indexRDV.blade.php ... */
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1" dir="rtl">Échantillons Partiels</h2>
                <p class="mg-b-0" dir="rtl">Liste des enquêtes enregistrées comme partielles.</p>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div><label class="tx-13" dir="rtl">Nombre d'entreprises répondues</label><h5>{{ $nombreEntreprisesRepondues ?? '0' }}</h5></div>
            <div><label class="tx-13" dir="rtl">Nombre d'entreprises attribuées</label><h5>{{ $nombreEntreprisesAttribuees ?? '0' }}</h5></div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
        {{-- Affichage des messages de succès et d'erreur --}}
        @if (session('success'))
            <div class="alert alert-success-custom mg-b-20 text-right auto-hide alert-custom" role="alert">
                {{ session('success') }} <i class="fas fa-check-circle"></i>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger-custom mg-b-20 text-right auto-hide alert-custom" role="alert">
                {{ session('error') }} <i class="fas fa-times-circle"></i>
            </div>
        @endif

        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card card-rdv">
                    <div class="card-header card-header-custom text-center">
                        <h4 class="card-title mg-b-0">Mes Échantillons Partiels <i class="fas fa-edit"></i></h4>
                    </div>
                    <div class="card-body card-body-custom">
                        {{-- Formulaire de recherche --}}
                        <form method="GET" action="{{ route('echantillons.partiels') }}" class="mb-4 search-form">
                            <div class="row">
                                <div class="col-md-10 mb-3">
                                    <label for="search_entreprise_input" class="form-label">Rechercher une entreprise :</label>
                                    <input type="text" name="search_term" id="search_entreprise_input" class="form-control form-control-sm" placeholder="Nom de l'entreprise..." value="{{ request('search_term') }}">
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button class="btn btn-info btn-sm w-100" type="submit">
                                        <i class="fas fa-search"></i> Rechercher
                                    </button>
                                </div>
                            </div>
                            @if(request('search_term'))
                            <div class="row mt-2">
                                <div class="col-12 text-center">
                                    <a href="{{ route('echantillons.partiels') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times"></i> Effacer la recherche
                                    </a>
                                </div>
                            </div>
                            @endif
                        </form>

                        @if($echantillons->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover mg-b-0 text-md-nowrap table-rdv">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%;">Entreprise</th>
                                            <th style="width: 30%;">Dernière mise à jour</th>
                                            <th style="width: 30%;">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($echantillons as $echantillon)
                                            <tr title="Continuer l'enquête pour : {{ $echantillon->entreprise->nom_entreprise }}"
                                                onclick="window.location='{{ route('echantillons.show', ['echantillon' => $echantillon->id]) }}'">
                                                <td class="company-name">
                                                    <i class="fas fa-building"></i>
                                                    {{ $echantillon->entreprise->nom_entreprise }}
                                                </td>
                                                <td>
                                                    {{ $echantillon->updated_at->format('d/m/Y H:i') }}
                                                    <br><small class="text-muted">({{ $echantillon->updated_at->locale('fr')->diffForHumans() }})</small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning">{{ $echantillon->statut }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            @if ($echantillons->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $echantillons->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        @else
                            <div class="empty-state-rdv">
                                <i class="fas fa-folder-open"></i>
                                @if(request('search_term'))
                                    <p>Aucun échantillon partiel ne correspond à votre recherche.</p>
                                    <a href="{{ route('echantillons.partiels') }}" class="alert-link" style="text-decoration: underline;">Afficher tous les échantillons partiels</a>
                                @else
                                    <p>Vous n'avez aucun échantillon avec le statut "Partiel" pour le moment.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- Réutilisez la même section JS que votre page de rendez-vous --}}
    {{-- ... collez ici la section @section('js') de votre fichier indexRDV.blade.php ... --}}
@endsection