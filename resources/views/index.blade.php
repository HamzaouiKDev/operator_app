@extends('layouts.master')
@section('css')
<!--  Owl-carousel css-->
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<div>
						  <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Hi, welcome back!</h2>
						  <p class="mg-b-0">Sales monitoring dashboard template.</p>
						</div>
					</div>
					<div class="main-dashboard-header-right">
						<div>
							<label class="tx-13">Customer Ratings</label>
							<div class="main-star">
								<i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star"></i> <span>(14,873)</span>
							</div>
						</div>
						<div>
							<label class="tx-13">Online Sales</label>
							<h5>563,275</h5>
						</div>
						<div>
							<label class="tx-13">Offline Sales</label>
							<h5>783,675</h5>
						</div>
					</div>
				</div>
				<!-- /breadcrumb -->
@endsection
@section('content')
				<!-- row -->
				<div class="row row-sm">
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-primary-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-3 tx-12 text-white">TODAY ORDERS</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">$5,74.12</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last week</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-up text-white"></i>
											<span class="text-white op-7"> +427</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-danger-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-3 tx-12 text-white">TODAY EARNINGS</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">$1,230.17</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last week</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-down text-white"></i>
											<span class="text-white op-7"> -23.09%</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline2" class="pt-1">3,2,4,6,12,14,8,7,14,16,12,7,8,4,3,2,2,5,6,7</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-success-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-3 tx-12 text-white">TOTAL EARNINGS</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">$7,125.70</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last week</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-up text-white"></i>
											<span class="text-white op-7"> 52.09%</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline3" class="pt-1">5,10,5,20,22,12,15,18,20,15,8,12,22,5,10,12,22,15,16,10</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-warning-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-3 tx-12 text-white">PRODUCT SOLD</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">$4,820.50</h4>
											<p class="mb-0 tx-12 text-white op-7">Compared to last week</p>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-down text-white"></i>
											<span class="text-white op-7"> -152.3</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline4" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
						</div>
					</div>
				</div>
				<!-- row closed -->

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
		<!-- Container closed -->
@endsection
@section('js')
<!--Internal  Chart.bundle js -->
<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<!-- Moment js -->
<script src="{{URL::asset('assets/plugins/raphael/raphael.min.js')}}"></script>
<!--Internal  Flot js-->
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.pie.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.resize.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.categories.js')}}"></script>
<script src="{{URL::asset('assets/js/dashboard.sampledata.js')}}"></script>
<script src="{{URL::asset('assets/js/chart.flot.sampledata.js')}}"></script>
<!--Internal Apexchart js-->
<script src="{{URL::asset('assets/js/apexcharts.js')}}"></script>
<!-- Internal Map -->
<script src="{{URL::asset('assets/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<script src="{{URL::asset('assets/js/modal-popup.js')}}"></script>
<!--Internal  index js -->
<script src="{{URL::asset('assets/js/index.js')}}"></script>
<script src="{{URL::asset('assets/js/jquery.vmap.sampledata.js')}}"></script>	
@endsection