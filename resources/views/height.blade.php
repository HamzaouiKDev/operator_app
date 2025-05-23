<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Première Entreprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Détails de l'Entreprise</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Carte pour les détails de l'entreprise -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>{{ $entreprise->nom_entreprise }}</h2>
            </div>
            <div class="card-body">
                <p><strong>Code National :</strong> {{ $entreprise->code_national }}</p>
                <p><strong>Activité :</strong> {{ $entreprise->libelle_activite }}</p>
                <p><strong>Adresse :</strong> {{ $entreprise->numero_rue }} {{ $entreprise->nom_rue }}, {{ $entreprise->ville }}</p>
                <p><strong>Gouvernorat :</strong> {{ $entreprise->gouvernorat }}</p>
                <p><strong>Statut :</strong> {{ $entreprise->statut }}</p>
                @if ($entreprise->adresse_cnss)
                    <p><strong>Adresse CNSS :</strong> {{ $entreprise->adresse_cnss }}, {{ $entreprise->localite_cnss }}</p>
                @endif
            </div>
        </div>

        <!-- Boutons pour actions -->
        <div class="mb-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTelephoneModal">Ajouter un numéro de téléphone</button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createRendezVousModal">Créer un rendez-vous</button>
        </div>

        <!-- Tableau des téléphones -->
        <h3>Téléphones</h3>
        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Source</th>
                    <th>Principal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entreprise->telephones as $telephone)
                    <tr>
                        <td>{{ $telephone->numero }}</td>
                        <td>{{ $telephone->source ?? '-' }}</td>
                        <td>{{ $telephone->est_primaire ? 'Oui' : 'Non' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Aucun numéro de téléphone</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tableau des emails -->
        <h3>Emails</h3>
        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Source</th>
                    <th>Principal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entreprise->emails as $email)
                    <tr>
                        <td>{{ $email->email }}</td>
                        <td>{{ $email->source ?? '-' }}</td>
                        <td>{{ $email->est_primaire ? 'Oui' : 'Non' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Aucun email</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tableau des contacts -->
        <h3>Contacts</h3>
        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Poste</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entreprise->contacts as $contact)
                    <tr>
                        <td>{{ $contact->prenom }}</td>
                        <td>{{ $contact->nom }}</td>
                        <td>{{ $contact->email ?? '-' }}</td>
                        <td>{{ $contact->telephone ?? '-' }}</td>
                        <td>{{ $contact->poste ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Aucun contact</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tableau des échantillons d'enquêtes -->
        <h3>Échantillons d'Enquêtes</h3>
        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Enquête</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entreprise->echantillons as $echantillon)
                    <tr>
                        <td>{{ $echantillon->id }}</td>
                        <td>{{ $echantillon->enquete->titre }}</td>
                        <td>{{ $echantillon->statut }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Aucun échantillon</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Modal pour ajouter un numéro de téléphone -->
        <div class="modal fade" id="addTelephoneModal" tabindex="-1" aria-labelledby="addTelephoneModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTelephoneModalLabel">Ajouter un numéro de téléphone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('telephones.store', $entreprise) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="numero" class="form-label">Numéro de téléphone</label>
                                <input type="text" class="form-control" id="numero" name="numero" required>
                            </div>
                            <div class="mb-3">
                                <label for="source" class="form-label">Source (optionnel)</label>
                                <input type="text" class="form-control" id="source" name="source">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="est_primaire" name="est_primaire">
                                <label class="form-check-label" for="est_primaire">Définir comme principal</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal pour créer un rendez-vous -->
        <div class="modal fade" id="createRendezVousModal" tabindex="-1" aria-labelledby="createRendezVousModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createRendezVousModalLabel">Créer un rendez-vous</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('rendezvous.store', $entreprise) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="echantillon_enquete_id" class="form-label">Échantillon d'Enquête</label>
                                <select class="form-select" id="echantillon_enquete_id" name="echantillon_enquete_id" required>
                                    <option value="">Sélectionner un échantillon</option>
                                    @foreach ($entreprise->echantillons as $echantillon)
                                        <option value="{{ $echantillon->id }}">{{ $echantillon->enquete->titre }} (ID: {{ $echantillon->id }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="heure_debut" class="form-label">Heure de début</label>
                                <input type="datetime-local" class="form-control" id="heure_debut" name="heure_debut" required>
                            </div>
                            <div class="mb-3">
                                <label for="heure_fin" class="form-label">Heure de fin</label>
                                <input type="datetime-local" class="form-control" id="heure_fin" name="heure_fin" required>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes (optionnel)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-success">Créer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
?>