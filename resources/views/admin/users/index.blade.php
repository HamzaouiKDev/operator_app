@extends('layouts.master')

@section('page-header')
<div class="breadcrumb-header justify-content-between" dir="rtl">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الإدارة</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إدارة المستخدمين</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">قائمة المستخدمين</h4>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createUserModal">
                        <i class="fas fa-plus ml-2"></i>إضافة مستخدم
                    </button>
                </div>
                <div class="card-body">
                    <div id="success-alert" class="alert alert-success" style="display: none;"></div>
                    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الدور</th>
                                    <th class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="badge badge-info">{{ $user->getRoleNames()->implode(', ') }}</span></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-info edit-user-btn"
                                                    data-toggle="modal" data-target="#editUserModal"
                                                    data-fetch-url="{{ route('admin.users.edit', $user->id) }}">
                                                تعديل
                                            </button>
                                            
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('هل أنت متأكد من أنك تريد حذف هذا المستخدم؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">لا يوجد مستخدمون لعرضهم.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <form id="createUserForm" action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="modal-header"><h5 class="modal-title">إضافة مستخدم جديد</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
                <div id="createUserErrors" class="alert alert-danger" style="display: none;"></div>
                <div class="form-group"><label>الاسم</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label>البريد الإلكتروني</label><input type="email" name="email" class="form-control" required></div>
                <div class="form-group"><label>كلمة المرور</label><input type="password" name="password" class="form-control" required></div>
                <div class="form-group"><label>تأكيد كلمة المرور</label><input type="password" name="password_confirmation" class="form-control" required></div>
                <div class="form-group"><label>الدور</label><select name="role" class="form-control" required><option value="">-- اختر دورًا --</option>@foreach($roles as $role)<option value="{{ $role->name }}">{{ $role->name }}</option>@endforeach</select></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">إنشاء</button></div>
        </form>
    </div></div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <form id="editUserForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header"><h5 class="modal-title" id="editUserModalLabel">تعديل المستخدم</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
                <div id="editUserErrors" class="alert alert-danger" style="display: none;"></div>
                <div class="form-group"><label for="edit_name">الاسم</label><input type="text" name="name" id="edit_name" class="form-control" required></div>
                <div class="form-group"><label for="edit_email">البريد الإلكتروني</label><input type="email" name="email" id="edit_email" class="form-control" required></div>
                <div class="form-group"><label for="edit_password">كلمة مرور جديدة (اتركه فارغًا لعدم التغيير)</label><input type="password" name="password" id="edit_password" class="form-control"></div>
                <div class="form-group"><label for="edit_password_confirmation">تأكيد كلمة المرور الجديدة</label><input type="password" name="password_confirmation" id="edit_password_confirmation" class="form-control"></div>
                <div class="form-group"><label for="edit_role">الدور</label><select name="role" id="edit_role" class="form-control" required></select></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary">تحديث</button></div>
        </form>
    </div></div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Fonction générique pour gérer la soumission des formulaires AJAX
    const handleFormSubmit = (formId, errorDivId) => {
        const form = document.getElementById(formId);
        if (!form) return;
        
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(this);
            const actionUrl = this.getAttribute('action');
            const errorDiv = document.getElementById(errorDivId);
            const submitButton = this.querySelector('button[type="submit"]');

            submitButton.disabled = true;
            submitButton.innerHTML = 'جاري المعالجة...';
            errorDiv.style.display = 'none';

            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.errors && data.success) {
                    location.reload(); // Recharger la page en cas de succès
                } else {
                    let errorHtml = '<ul>';
                    for (const error in data.errors) {
                        errorHtml += '<li>' + data.errors[error][0] + '</li>';
                    }
                    errorHtml += '</ul>';
                    errorDiv.innerHTML = errorHtml;
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                errorDiv.innerHTML = 'Une erreur de communication est survenue.';
                errorDiv.style.display = 'block';
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = formId === 'createUserForm' ? 'إنشاء' : 'تحديث';
            });
        });
    };

    handleFormSubmit('createUserForm', 'createUserErrors');
    handleFormSubmit('editUserForm', 'editUserErrors');

    // Remplir la modale d'édition lors de son ouverture
    $('#editUserModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var fetchUrl = button.data('fetch-url');
        var modal = $(this);

        // Définir l'URL de mise à jour pour le formulaire
        modal.find('form').attr('action', fetchUrl.replace('/edit', ''));

        fetch(fetchUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
        .then(response => response.json())
        .then(data => {
            // Remplir les champs du formulaire
            modal.find('#edit_name').val(data.user.name);
            modal.find('#edit_email').val(data.user.email);
            modal.find('#edit_password, #edit_password_confirmation').val('');
            
            // Gérer le menu déroulant des rôles
            const roleSelect = modal.find('#edit_role');
            roleSelect.empty();
            data.roles.forEach(role => {
                const isSelected = data.userRoles.includes(role.name);
                roleSelect.append(new Option(role.name, role.name, isSelected, isSelected));
            });
        });
    });
});
</script>
@endsection