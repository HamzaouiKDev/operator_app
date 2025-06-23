@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="titre">عنوان المسح <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="titre" name="titre" value="{{ old('titre', $enquete->titre) }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="statut">الحالة <span class="text-danger">*</span></label>
            <select name="statut" id="statut" class="form-control" required>
                <option value="brouillon" @selected(old('statut', $enquete->statut) == 'brouillon')>مسودة</option>
                <option value="active" @selected(old('statut', $enquete->statut) == 'active')>نشط</option>
                <option value="terminee" @selected(old('statut', $enquete->statut) == 'terminee')>منتهي</option>
            </select>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="description">الوصف</label>
    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $enquete->description) }}</textarea>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="date_debut">تاريخ البدء <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ old('date_debut', $enquete->date_debut ? $enquete->date_debut->format('Y-m-d') : '') }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="date_fin">تاريخ الانتهاء <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ old('date_fin', $enquete->date_fin ? $enquete->date_fin->format('Y-m-d') : '') }}" required>
        </div>
    </div>
</div>

<hr>
<h5 class="mb-3">البريد الإلكتروني للتقديم</h5>

<div class="form-group">
    <label for="titre_mail">موضوع البريد الإلكتروني</label>
    <input type="text" class="form-control" id="titre_mail" name="titre_mail" value="{{ old('titre_mail', $enquete->titre_mail) }}">
</div>
<div class="form-group">
    <label for="corps_mail">محتوى البريد الإلكتروني</label>
    <textarea name="corps_mail" id="corps_mail" class="form-control" rows="5">{{ old('corps_mail', $enquete->corps_mail) }}</textarea>
</div>
<hr>



<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
    <button type="submit" class="btn btn-primary">حفظ</button>
</div>
