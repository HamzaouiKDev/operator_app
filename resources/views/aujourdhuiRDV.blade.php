@extends('layouts.master')

@section('content')
    <div class="container-fluid" dir="rtl">
        <div class="row row-sm">
            <div class="col-lg-12">
                {{-- Titre de la page mis à jour --}}
                <div class="card mg-b-20 shadow-sm" style="border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                    <div class="card-header pb-0 text-center text-white" style="padding: 20px 0; border-radius: 15px 15px 0 0;">
                        <h4 class="card-title mg-b-0 tx-28" style="font-weight: 700; letter-spacing: 1px;">قائمة مواعيد اليوم</h4>
                    </div>
                    <div class="card-body text-right" style="background-color: #ecf0f5; padding: 30px; min-height: 400px;">
                        @if (session('success'))
                            <div class="alert alert-success mg-b-0 text-right" role="alert" style="...">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger mg-b-0 text-right" role="alert" style="...">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{-- Formulaire de recherche (action pointe vers la nouvelle route) --}}
                        <form method="GET" action="{{ route('rendezvous.aujourdhui') }}" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search_entreprise" class="form-control" placeholder="البحث عن شركة..." value="{{ request('search_entreprise') }}" aria-label="البحث عن شركة">
                                <div class="input-group-append">
                                    <button class="btn btn-info" type="submit" style="border-radius: 0 8px 8px 0;">
                                        <i class="typcn typcn-zoom-outline"></i> بحث
                                    </button>
                                </div>
                            </div>
                        </form>
                        {{-- Fin du formulaire de recherche --}}

                        {{-- Message si recherche pour aujourd'hui ne donne rien --}}
                        @if(request('search_entreprise') && $rendezVous->isEmpty())
                            <div class="alert alert-warning text-right mt-3" role="alert">
                                لم يتم العثور على شركات تطابق بحثك لمواعيد اليوم: "{{ request('search_entreprise') }}".
                                <a href="{{ route('rendezvous.aujourdhui') }}" class="alert-link" style="text-decoration: underline;">إظهار كافة مواعيد اليوم</a>.
                            </div>
                        @endif

                        @if($rendezVous->isNotEmpty())
                            <div class="table-responsive" style="background-color: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); overflow: hidden;">
                                <table class="table table-hover mg-b-0 text-md-nowrap" style="border: none;">
                                    <thead style="background-color: #2c3e50; color: white;"> {{-- Couleur d'en-tête un peu différente pour distinguer --}}
                                        <tr>
                                            <th class="tx-16 fw-bold" style="padding: 15px; border: none;">الشركة / تفاصيل الموعد</th>
                                            <th class="tx-16 fw-bold" style="padding: 15px; border: none;">العنوان / التاريخ والوقت</th>
                                            <th class="tx-16 fw-bold" style="padding: 15px; border: none;">الهاتف / المكان</th>
                                            <th class="tx-16 fw-bold" style="padding: 15px; border: none;">البريد الإلكتروني / ملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody style="color: #2c3e50;">
                                        @foreach($rendezVousGroupedByEntreprise as $entrepriseId => $rdvs)
                                            @php
                                                $firstRdvForEntreprise = $rdvs->first();
                                                $entreprise = $firstRdvForEntreprise->echantillonEnquete && $firstRdvForEntreprise->echantillonEnquete->entreprise ? $firstRdvForEntreprise->echantillonEnquete->entreprise : null;
                                            @endphp

                                            @if($entreprise)
                                                <tr class="entreprise-header-row"
                                                    style="background-color: #f8f9fa; font-weight: bold; border-top: 2px solid #2c3e50; cursor: pointer; transition: background-color 0.2s ease;"
                                                    onclick="window.location='{{ route('rendezvous.entreprise', $firstRdvForEntreprise->id) }}'"
                                                    title="عرض تفاصيل الشركة والمواعيد">
                                                    <td style="padding: 15px; border: none; color: #2980b9;">
                                                        <i class="typcn typcn-building" style="font-size: 20px; margin-left: 8px; color: #f1c40f;"></i>
                                                        {{ $entreprise->nom_entreprise ?? 'غير محدد' }}
                                                    </td>
                                                    <td style="padding: 15px; border: none;">{{ $entreprise->adresse ?? 'غير محدد' }}</td>
                                                    <td style="padding: 15px; border: none;">{{ $entreprise->telephone ?? 'غير محدد' }}</td>
                                                    <td style="padding: 15px; border: none;">{{ $entreprise->email ?? 'غير محدد' }}</td>
                                                </tr>
                                            @else
                                                {{-- Cas où le RDV n'est pas lié à une entreprise --}}
                                                <tr class="entreprise-header-row" style="background-color: #ffebee; font-weight: bold; border-top: 2px solid #e74c3c;">
                                                    <td colspan="4" style="padding: 15px; border: none; color: #e74c3c;">
                                                        <i class="typcn typcn-warning" style="font-size: 20px; margin-left: 8px;"></i>
                                                        شركة غير محددة (عينة غير مرتبطة بشركة)
                                                    </td>
                                                </tr>
                                            @endif

                                            @foreach($rdvs as $rdv)
                                                <tr class="rendezvous-row" style="border-bottom: 1px solid #e0e0e0; transition: background-color 0.2s ease;">
                                                    <td style="padding: 15px; border: none;"></td>
                                                    <td style="padding: 15px; border: none;">{{ $rdv->heure_debut ? \Carbon\Carbon::parse($rdv->heure_debut)->format('H:i') : 'غير محدد' }} (اليوم)</td> {{-- Affiche seulement l'heure --}}
                                                    <td style="padding: 15px; border: none;">{{ $rdv->lieu ?? 'غير محدد' }}</td>
                                                    <td style="padding: 15px; border: none;">{{ $rdv->notes ?? 'غير متوفرة' }}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="pagination justify-content-center mt-4" style="margin-top: 30px;">
                                {{ $rendezVous->links('pagination::bootstrap-4') }}
                                <style>
                                    /* ... vos styles de pagination ... */
                                </style>
                            </div>
                        @elseif(!request('search_entreprise')) {{-- S'affiche seulement s'il n'y a aucun RDV pour aujourd'hui ET aucune recherche active --}}
                            <div class="empty-state text-center" style="margin-top: 50px; padding: 30px; background-color: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);">
                                <i class="typcn typcn-bell" style="font-size: 60px; color: #27ae60; margin-bottom: 15px;"></i> {{-- Icône différente --}}
                                <p class="text-muted" style="color: #2c3e50; font-size: 18px; margin: 0;">لا توجد مواعيد مسجلة لليوم.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- Le JavaScript peut être le même que pour indexRDV.blade.php --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ... (code JS existant pour les alertes et les survols de lignes)
        });
    </script>
@endsection