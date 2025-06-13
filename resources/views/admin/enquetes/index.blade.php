@extends('layouts.master')
@section('title', 'إدارة المسوح')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الإدارة</h4><span class="text-muted mt-1 tx-13 ms-2 mb-0">/ إدارة المسوح</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <button id="addEnqueteBtn" class="btn btn-primary ml-2"><i class="fas fa-plus"></i> مسح جديد</button>
    </div>
</div>
<!-- breadcrumb -->
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mg-b-0">كل المسوح</h4>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped mg-b-0 text-md-nowrap">
                        <thead>
                            <tr>
                                <th>المعرف</th>
                                <th>العنوان</th>
                                <th>تاريخ البدء</th>
                                <th>تاريخ الانتهاء</th>
                                <th>الحالة</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enquetes as $enquete)
                                <tr>
                                    <th scope="row">{{ $enquete->id }}</th>
                                    <td>{{ $enquete->titre }}</td>
                                    <td>{{ $enquete->date_debut->format('d/m/Y') }}</td>
                                    <td>{{ $enquete->date_fin->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $enquete->statut == 'active' ? 'success' : ($enquete->statut == 'terminee' ? 'danger' : 'warning') }}">
                                            @if($enquete->statut == 'active')
                                                نشط
                                            @elseif($enquete->statut == 'terminee')
                                                منتهي
                                            @else
                                                مسودة
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info edit-btn" data-id="{{ $enquete->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        {{-- MODIFICATION : Le bouton de suppression déclenche maintenant la modale --}}
                                        <button class="btn btn-sm btn-danger delete-btn" 
                                                data-url="{{ route('admin.enquetes.destroy', $enquete->id) }}" 
                                                data-toggle="modal" 
                                                data-target="#deleteModal">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لم يتم العثور على أي مسح.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $enquetes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modale pour Ajout/Modification -->
<div class="modal fade" id="enqueteModal" tabindex="-1" aria-labelledby="enqueteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enqueteModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="enqueteForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="form-method"></div>
                    @include('admin.enquetes._form', ['enquete' => new \App\Models\Enquete()])
                </form>
            </div>
        </div>
    </div>
</div>

<!-- AJOUT : Modale de Confirmation de Suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في حذف هذا المسح؟</p>
                <p class="text-danger"><strong>انتباه:</strong> سيؤدي هذا الإجراء إلى حذف جميع العينات المرتبطة بهذا المسح أيضًا.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var modal = $('#enqueteModal');
    var form = $('#enqueteForm');
    var modalLabel = $('#enqueteModalLabel');

    modal.on('hidden.bs.modal', function () {
        form.trigger('reset');
        $('#current-file-container').hide();
        $('#current-file').text('');
    });

    $('#addEnqueteBtn').on('click', function() {
        form.trigger('reset');
        form.attr('action', "{{ route('admin.enquetes.store') }}");
        $('#form-method').empty();
        modalLabel.text('إضافة مسح جديد');
        modal.modal('show');
    });

    $('.edit-btn').on('click', function() {
        var enqueteId = $(this).data('id');
        var url = "{{ route('admin.enquetes.show', ':id') }}".replace(':id', enqueteId);
        
        $.get(url, function(data) {
            form.trigger('reset');
            var updateUrl = "{{ route('admin.enquetes.update', ':id') }}".replace(':id', data.id);
            form.attr('action', updateUrl);
            $('#form-method').html('@method("PUT")');
            modalLabel.text('تعديل المسح: ' + data.titre);

            $('#titre').val(data.titre);
            $('#description').val(data.description);
            $('#statut').val(data.statut);
            $('#titre_mail').val(data.titre_mail);
            $('#corps_mail').val(data.corps_mail);
            
            if(data.date_debut) $('#date_debut').val(data.date_debut.split('T')[0]);
            if(data.date_fin) $('#date_fin').val(data.date_fin.split('T')[0]);
            
            if(data.piece_jointe_path) {
                $('#current-file').text(data.piece_jointe_path);
                $('#current-file-container').show();
            }
            
            modal.modal('show');
        }).fail(function() {
            alert('خطأ أثناء جلب بيانات المسح.');
        });
    });

    // AJOUT : Script pour la modale de suppression
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Bouton qui a déclenché la modale
        var url = button.data('url');        // Extraire l'URL de l'attribut data-url
        var form = $(this).find('#deleteForm'); // Trouver le formulaire dans la modale
        form.attr('action', url);            // Mettre à jour l'action du formulaire
    });
});
</script>
@endsection
