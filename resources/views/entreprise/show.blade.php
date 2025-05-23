<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'entreprise</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6 text-center">Détails de l'entreprise</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 mx-auto max-w-2xl" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Enterprise Details -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Entreprise</h3>
            <ul class="list-none space-y-2">
                <li><span class="font-medium">Code National:</span> {{ $entreprise->code_national }}</li>
                <li><span class="font-medium">Nom:</span> {{ $entreprise->nom_entreprise }}</li>
                <li><span class="font-medium">Activité:</span> {{ $entreprise->libelle_activite }}</li>
                <li><span class="font-medium">Adresse:</span> {{ $entreprise->numero_rue }} {{ $entreprise->nom_rue }}, {{ $entreprise->ville }}, {{ $entreprise->gouvernorat }}</li>
                <li><span class="font-medium">Statut:</span> {{ $entreprise->statut }}</li>
                @if ($entreprise->adresse_cnss)
                    <li><span class="font-medium">Adresse CNSS:</span> {{ $entreprise->adresse_cnss }}</li>
                @endif
                @if ($entreprise->localite_cnss)
                    <li><span class="font-medium">Localité CNSS:</span> {{ $entreprise->localite_cnss }}</li>
                @endif
            </ul>
        </div>

        <!-- Phone Numbers -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Numéros de téléphone</h3>
            @if ($telephones->isEmpty())
                <p class="text-gray-600">Aucun numéro de téléphone enregistré.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-blue-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-blue-800 border-b border-blue-200">Numéro</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-blue-800 border-b border-blue-200">Principal</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-blue-800 border-b border-blue-200">Source</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($telephones as $telephone)
                                <tr class="{{ $loop->even ? 'bg-blue-50' : 'bg-white' }} hover:bg-blue-100 transition-colors">
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $telephone->numero }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $telephone->est_primaire ? 'Oui' : 'Non' }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $telephone->source ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Contacts -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Personnes de contact</h3>
            @if ($contacts->isEmpty())
                <p class="text-gray-600">Aucune personne de contact enregistrée.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-green-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-green-800 border-b border-green-200">Civilité</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-green-800 border-b border-green-200">Prénom</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-green-800 border-b border-green-200">Nom</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-green-800 border-b border-green-200">Poste</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-green-800 border-b border-green-200">Email</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-green-800 border-b border-green-200">Téléphone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contacts as $contact)
                                <tr class="{{ $loop->even ? 'bg-green-50' : 'bg-white' }} hover:bg-green-100 transition-colors">
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $contact->civilite ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $contact->prenom }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $contact->nom }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $contact->poste ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $contact->email ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $contact->telephone ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Rendez-vous -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Rendez-vous</h3>
            @if ($rendezVous->isEmpty())
                <p class="text-gray-600">Aucun rendez-vous enregistré.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-purple-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-purple-800 border-b border-purple-200">Heure de début</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-purple-800 border-b border-purple-200">Heure de fin</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-purple-800 border-b border-purple-200">Statut</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-purple-800 border-b border-purple-200">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rendezVous as $rdv)
                                <tr class="{{ $loop->even ? 'bg-purple-50' : 'bg-white' }} hover:bg-purple-100 transition-colors">
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $rdv->heure_debut ? \Carbon\Carbon::parse($rdv->heure_debut)->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $rdv->heure_fin ? \Carbon\Carbon::parse($rdv->heure_fin)->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $rdv->statut }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 text-sm text-gray-700">{{ $rdv->notes ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Buttons -->
        <div class="flex justify-center space-x-4">
            <button onclick="openModal('telephoneModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Ajouter un numéro de téléphone</button>
            <button onclick="openModal('contactModal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">Ajouter une personne de contact</button>
            <button onclick="openModal('rendezVousModal')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md">Ajouter un rendez-vous</button>
        </div>

        <!-- Modal pour ajouter un numéro de téléphone -->
        <div id="telephoneModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Ajouter un numéro de téléphone</h3>
                <form action="{{ route('entreprise.telephone.store', $entreprise->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="numero" class="block text-sm font-medium text-gray-700">Numéro</label>
                        <input type="text" name="numero" id="numero" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="source" class="block text-sm font-medium text-gray-700">Source</label>
                        <input type="text" name="source" id="source" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="est_primaire" class="inline-flex items-center">
                            <input type="checkbox" name="est_primaire" id="est_primaire" value="1" class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Numéro principal</span>
                        </label>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('telephoneModal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md">Annuler</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal pour ajouter une personne de contact -->
        <div id="contactModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Ajouter une personne de contact</h3>
                <form action="{{ route('entreprise.contact.store', $entreprise->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="civilite" class="block text-sm font-medium text-gray-700">Civilité</label>
                        <input type="text" name="civilite" id="civilite" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input type="text" name="prenom" id="prenom" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" name="nom" id="nom" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="poste" class="block text-sm font-medium text-gray-700">Poste</label>
                        <input type="text" name="poste" id="poste" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('contactModal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md">Annuler</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal pour ajouter un rendez-vous -->
        <div id="rendezVousModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Ajouter un rendez-vous</h3>
                <form action="{{ route('entreprise.rendezvous.store', $echantillon->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="heure_debut" class="block text-sm font-medium text-gray-700">Heure de début</label>
                        <input type="datetime-local" name="heure_debut" id="heure_debut" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="heure_fin" class="block text-sm font-medium text-gray-700">Heure de fin</label>
                        <input type="datetime-local" name="heure_fin" id="heure_fin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="statut" id="statut" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="Planifié">Planifié</option>
                            <option value="Confirmé">Confirmé</option>
                            <option value="Annulé">Annulé</option>
                            <option value="Terminé">Terminé</option>
                        </select>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('rendezVousModal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md">Annuler</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>
</body>
</html>