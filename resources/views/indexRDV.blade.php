@extends('layouts.master')

@section('content')
    <div class="container-fluid" dir="rtl">
        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card mg-b-20 shadow-sm" style="border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
                    <div class="card-header pb-0 text-center text-white" style="padding: 20px 0; border-radius: 15px 15px 0 0;">
                        <h4 class="card-title mg-b-0 tx-28" style="font-weight: 700; letter-spacing: 1px;">قائمة المواعيد</h4>
                    </div>
                    <div class="card-body text-right" style="background-color: #ecf0f5; padding: 30px; min-height: 400px;">
                        @if (session('success'))
                            <div class="alert alert-success mg-b-0 text-right" role="alert" style="background-color: #2ecc71; border: none; color: white; border-radius: 8px; margin-bottom: 20px; padding: 15px; box-shadow: 0 4px 10px rgba(46, 204, 113, 0.3);">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger mg-b-0 text-right" role="alert" style="background-color: #e74c3c; border: none; color: white; border-radius: 8px; margin-bottom: 20px; padding: 15px; box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3);">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(isset($rendezVous) && $rendezVous->isNotEmpty())
                            @foreach($rendezVousGroupedByEntreprise as $entrepriseId => $rdvs)
                                @if($rdvs->first()->echantillonEnquete && $rdvs->first()->echantillonEnquete->entreprise)
                                    <div class="entreprise-card mb-5" style="background-color: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer;" onclick="window.location='{{ route('rendezvous.entreprise', $rdvs->first()->id) }}'" title="عرض تفاصيل الشركة والمواعيد">
                                        <div class="entreprise-header" style="background-color: #3498db; padding: 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                                            <h5 style="color: white; font-weight: 700; margin: 0; display: flex; align-items: center;">
                                                <i class="typcn typcn-building" style="font-size: 24px; margin-left: 10px; color: #f1c40f;"></i>
                                                {{ $rdvs->first()->echantillonEnquete->entreprise->nom_entreprise ?? 'غير محدد' }}
                                            </h5>
                                        </div>
                                        <div class="entreprise-details" style="padding: 20px; background-color: #f8f9fa;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p style="margin-bottom: 10px; color: #2c3e50; font-size: 15px;"><strong>العنوان:</strong> {{ $rdvs->first()->echantillonEnquete->entreprise->adresse ?? 'غير محدد' }}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p style="margin-bottom: 10px; color: #2c3e50; font-size: 15px;"><strong>الهاتف:</strong> {{ $rdvs->first()->echantillonEnquete->entreprise->telephone ?? 'غير محدد' }}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p style="margin-bottom: 10px; color: #2c3e50; font-size: 15px;"><strong>البريد الإلكتروني:</strong> {{ $rdvs->first()->echantillonEnquete->entreprise->email ?? 'غير محدد' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="rendezvous-list" style="padding: 0 20px 20px 20px;">
                                            <h6 style="color: #2c3e50; font-weight: 600; margin: 15px 0 10px; border-bottom: 2px solid #3498db; padding-bottom: 5px;">المواعيد المرتبطة</h6>
                                            <div class="table-responsive">
                                                <table class="table table-striped mg-b-0 text-md-nowrap" style="background-color: white; border-radius: 8px; overflow: hidden; border: none;">
                                                    <thead style="background-color: #1abc9c; color: white;">
                                                        <tr>
                                                            <th class="tx-16 fw-bold" style="padding: 15px; border: none;">التاريخ والوقت</th>
                                                            <th class="tx-16 fw-bold" style="padding: 15px; border: none;">المكان</th>
                                                            <th class="tx-16 fw-bold" style="padding: 15px; border: none;">ملاحظات</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody style="color: #2c3e50;">
                                                        @foreach($rdvs as $rdv)
                                                            <tr style="border: none; border-bottom: 1px solid #e0e0e0; cursor: pointer; transition: background-color 0.2s ease;" onclick="window.location='{{ route('rendezvous.entreprise', $rdv->id) }}'" title="عرض تفاصيل الشركة">
                                                                <td style="padding: 15px; border: none;">{{ $rdv->heure_debut ?? 'غير محدد' }}</td>
                                                                <td style="padding: 15px; border: none;">{{ 'غير محدد' }}</td>
                                                                <td style="padding: 15px; border: none;">{{ $rdv->notes ?? 'غير متوفرة' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="entreprise-card mb-5" style="background-color: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); overflow: hidden;">
                                        <div class="entreprise-header" style="background-color: #e74c3c; padding: 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                                            <h5 style="color: white; font-weight: 700; margin: 0;">شركة غير محددة</h5>
                                            <p class="text-muted" style="color: rgba(255, 255, 255, 0.8); margin-top: 5px;">عينة غير مرتبطة بشركة</p>
                                        </div>
                                        <div class="rendezvous-list" style="padding: 0 20px 20px 20px;">
                                            <h6 style="color: #2c3e50; font-weight: 600; margin: 15px 0 10px; border-bottom: 2px solid #e74c3c; padding-bottom: 5px;">المواعيد المرتبطة</h6>
                                            <div class="table-responsive">
                                                <table class="table table-striped mg-b-0 text-md-nowrap" style="background-color: white; border-radius: 8px; overflow: hidden; border: none;">
                                                    <thead style="background-color: #e67e22; color: white;">
                                                        <tr>
                                                            <th class="tx-16 fw-bold" style="padding: 15px; border: none;">التاريخ والوقت</th>
                                                            <th class="tx-16 fw-bold" style="padding: 15px; border: none;">المكان</th>
                                                            <th class="tx-16 fw-bold" style="padding: 15px; border: none;">ملاحظات</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody style="color: #2c3e50;">
                                                        @foreach($rdvs as $rdv)
                                                            <tr style="border: none; border-bottom: 1px solid #e0e0e0; cursor: pointer; transition: background-color 0.2s ease;" onclick="window.location='{{ route('rendezvous.entreprise', $rdv->id) }}'" title="عرض تفاصيل الشركة">
                                                                <td style="padding: 15px; border: none;">{{ $rdv->heure_debut ?? 'غير محدد' }}</td>
                                                                <td style="padding: 15px; border: none;">{{ 'غير محدد' }}</td>
                                                                <td style="padding: 15px; border: none;">{{ $rdv->notes ?? 'غير متوفرة' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <!-- Ajout des liens de pagination avec style flat -->
                            <div class="pagination justify-content-center mt-4" style="margin-top: 30px;">
                                {{ $rendezVous->links('pagination::bootstrap-4') }}
                                <style>
                                    .pagination .page-item .page-link {
                                        background-color: #f1c40f;
                                        color: #2c3e50;
                                        border: none;
                                        border-radius: 8px;
                                        margin: 0 5px;
                                        padding: 10px 15px;
                                        transition: background-color 0.3s ease, transform 0.2s ease;
                                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                                    }
                                    .pagination .page-item.active .page-link {
                                        background-color: #27ae60;
                                        color: white;
                                        transform: scale(1.05);
                                        box-shadow: 0 4px 10px rgba(39, 174, 96, 0.3);
                                    }
                                    .pagination .page-item .page-link:hover {
                                        background-color: #f39c12;
                                        color: #2c3e50;
                                        transform: scale(1.03);
                                        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
                                    }
                                </style>
                            </div>
                        @else
                            <div class="empty-state text-center" style="margin-top: 50px; padding: 30px; background-color: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);">
                                <i class="typcn typcn-calendar-outline" style="font-size: 60px; color: #9b59b6; margin-bottom: 15px;"></i>
                                <p class="text-muted" style="color: #2c3e50; font-size: 18px; margin: 0;">لا توجد مواعيد مسجلة في الوقت الحالي.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Page chargée, initialisation des événements...');
            // Faire disparaître les messages de succès ou d'erreur après 5 secondes
            const alertSuccess = document.querySelector('.alert-success');
            const alertDanger = document.querySelector('.alert-danger');
            if (alertSuccess || alertDanger) {
                setTimeout(function () {
                    if (alertSuccess) alertSuccess.style.display = 'none';
                    if (alertDanger) alertDanger.style.display = 'none';
                }, 5000);
            }

            // Ajouter un effet de survol aux cartes d'entreprise
            const entrepriseCards = document.querySelectorAll('.entreprise-card');
            if (entrepriseCards) {
                entrepriseCards.forEach(card => {
                    card.addEventListener('mouseenter', function () {
                        this.style.transform = 'translateY(-5px)';
                        this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.15)';
                    });
                    card.addEventListener('mouseleave', function () {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.08)';
                    });
                });
            }

            // Ajouter un effet de survol aux lignes de tableau
            const tableRows = document.querySelectorAll('tbody tr');
            if (tableRows) {
                tableRows.forEach(row => {
                    row.addEventListener('mouseenter', function () {
                        this.style.backgroundColor = 'rgba(52, 152, 219, 0.1)';
                    });
                    row.addEventListener('mouseleave', function () {
                        this.style.backgroundColor = 'transparent';
                    });
                });
            }
        });
    </script>
@endsection
