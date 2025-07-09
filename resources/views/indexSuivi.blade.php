@extends('layouts.master')
@section('css')
    {{-- (CSS personnalisé si besoin) --}}
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between" dir="rtl">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">قائمة المتابعة</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ عرض الكل</span>
            </div>
        </div>
    </div>
    @endsection

@section('content')
    <div class="row" dir="rtl">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0">قائمة العينات التي تتطلب متابعة</h4>
                    </div>
                    <p class="tx-12 tx-gray-500 mb-2">هذه القائمة تعرض فقط العينات التي حالتها "إعادة إتصال".</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mg-b-0 text-md-nowrap">
                            <thead>
                                <tr>
                                    <th>اسم الشركة</th>
                                    <th>سبب آخر متابعة</th>
                                    <th>ملاحظة آخر متابعة</th>
                                    <th>تاريخ التحديث</th>
                                    <th>الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($suivis as $echantillon)
                                    <tr>
                                        <td>{{ $echantillon->entreprise->nom_entreprise ?? 'شركة غير محددة' }}</td>
                                        <td>{{ $echantillon->suivis->first()->cause_suivi ?? 'لا يوجد' }}</td>
                                        <td>{{ $echantillon->suivis->first()->note ?? '' }}</td>
                                        <td>{{ $echantillon->updated_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('echantillons.show', $echantillon->id) }}" class="btn btn-primary btn-sm">
                                                معالجة العينة
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">لا توجد عينات للمتابعة حالياً.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{-- Pagination --}}
                        {{ $suivis->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection