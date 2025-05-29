@extends('layouts.master')

@section('content')
    <div class="container-fluid" dir="rtl">
        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card mg-b-20 shadow-sm" style="border: none; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                    <div class="card-header pb-0 text-center text-white" style="background-color: #27ae60; padding: 15px 0; border-radius: 10px 10px 0 0;">
                        <h4 class="card-title mg-b-0 tx-28">مواعيد الشركة: {{ $entreprise->nom_entreprise ?? 'غير محدد' }}</h4>
                    </div>
                    <div class="card-body text-right" style="background-color: #f8f9fa; padding: 20px;">
                        @if (session('success'))
                            <div class="alert alert-success mg-b-0 text-right" role="alert" style="background-color: #2ecc71; border: none; color: white; border-radius: 5px; margin-bottom: 15px; padding: 12px;">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger mg-b-0 text-right" role="alert" style="background-color: #e74c3c; border: none; color: white; border-radius: 5px; margin-bottom: 15px; padding: 12px;">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Détails complets de l'entreprise -->
                        <div class="entreprise-details mb-4" style="background-color: #e9ecef; padding: 15px; border-radius: 8px; color: #2c3e50;">
                            <h5 style="margin-bottom: 10px; font-weight: 600;">تفاصيل الشركة:</h5>
                            <p style="margin-bottom: 5px;"><strong>الاسم:</strong> {{ $entreprise->nom_entreprise ?? 'غير محدد' }}</p>
                            <p style="margin-bottom: 5px;"><strong>العنوان:</strong> {{ $entreprise->adresse ?? 'غير محدد' }}</p>
                            <p style="margin-bottom: 5px;"><strong>الهاتف:</strong> {{ $entreprise->telephone ?? 'غير محدد' }}</p>
                            <p style="margin-bottom: 5px;"><strong>البريد الإلكتروني:</strong> {{ $entreprise->email ?? 'غير محدد' }}</p>
                        </div>

                        @if(isset($rendezVous) && $rendezVous->isNotEmpty())
                            @foreach($rendezVousGrouped as $echantillonId => $rdvs)
                                <h5 class="mt-4" style="color: #34495e; font-weight: 600; margin-bottom: 15px;">عينة رقم: {{ $echantillonId }}</h5>
                                <div class="table-responsive mb-4">
                                    <table class="table table-striped mg-b-0 text-md-nowrap" style="background-color: white; border-radius: 8px; overflow: hidden; border: none;">
                                        <thead style="background-color: #ecf0f1; color: #2c3e50;">
                                            <tr>
                                                <th class="tx-16 fw-bold" style="padding: 12px; border: none;">التاريخ والوقت</th>
                                                <th class="tx-16 fw-bold" style="padding: 12px; border: none;">المكان</th>
                                                <th class="tx-16 fw-bold" style="padding: 12px; border: none;">ملاحظات</th>
                                            </tr>
                                        </thead>
                                        <tbody style="color: #7f8c8d;">
                                            @foreach($rdvs as $rdv)
                                                <tr style="border: none; border-bottom: 1px solid #e0e0e0;">
                                                    <td style="padding: 12px; border: none;">{{ $rdv->heure_debut ?? 'غير محدد' }}</td>
                                                    <td style="padding: 12px; border: none;">{{ 'غير محدد' }}</td>
                                                    <td style="padding: 12px; border: none;">{{ $rdv->notes ?? 'غير متوفرة' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach

                            <!-- Ajout des liens de pagination avec style flat -->
                            <div class="pagination justify-content-center mt-4">
                                {{ $rendezVous->links('pagination::bootstrap-4') }}
                                <style>
                                    .pagination .page-item .page-link {
                                        background-color: #ecf0f1;
                                        color: #2c3e50;
                                        border: none;
                                        border-radius: 5px;
                                        margin: 0 3px;
                                        padding: 8px 12px;
                                        transition: background-color 0.3s ease;
                                    }
                                    .pagination .page-item.active .page-link {
                                        background-color: #27ae60;
                                        color: white;
                                    }
                                    .pagination .page-item .page-link:hover {
                                        background-color: #bdc3c7;
                                        color: #2c3e50;
                                    }
                                </style>
                            </div>
                        @else
                            <p class="text-muted" style="color: #95a5a6; font-size: 16px; margin-top: 20px;">لا توجد مواعيد مسجلة لهذه الشركة في الوقت الحالي.</p>
                        @endif
                        <!-- Bouton de retour à la liste principale des rendez-vous -->
                        <div class="mt-4 text-center">
                            <a href="{{ route('rendezvous.index') }}" class="btn text-white" style="background-color: #27ae60; border: none; border-radius: 5px; padding: 10px 20px;">العودة إلى القائمة الرئيسية</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
