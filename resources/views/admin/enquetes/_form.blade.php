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

<div class="row">
    {{-- MODÈLE FRANÇAIS --}}
    <div class="col-md-6">
        <h6>Modèle en Français</h6>
        <div class="form-group">
            <label for="titre_mail_fr">Sujet de l'e-mail (FR)</label>
            <input type="text" class="form-control" id="titre_mail_fr" name="titre_mail_fr" value="{{ old('titre_mail_fr', $enquete->titre_mail_fr) }}">
        </div>
        <div class="form-group">
            <label for="corps_mail_fr">Contenu de l'e-mail (FR)</label>
            <textarea name="corps_mail_fr" id="corps_mail_fr" class="form-control" rows="5">{{ old('corps_mail_fr', $enquete->corps_mail_fr) }}</textarea>
        </div>
    </div>

    {{-- MODÈLE ARABE --}}
    <div class="col-md-6 text-right" dir="rtl">
        <h6>النموذج بالعربية</h6>
        <div class="form-group">
            <label for="titre_mail_ar">موضوع البريد الإلكتروني (AR)</label>
            <input type="text" class="form-control" id="titre_mail_ar" name="titre_mail_ar" value="{{ old('titre_mail_ar', $enquete->titre_mail_ar) }}">
        </div>
        <div class="form-group">
            <label for="corps_mail_ar">محتوى البريد الإلكتروني (AR)</label>
            <textarea name="corps_mail_ar" id="corps_mail_ar" class="form-control" rows="5">{{ old('corps_mail_ar', $enquete->corps_mail_ar) }}</textarea>
        </div>
    </div>
</div>
<hr>



<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
    <button type="submit" class="btn btn-primary">حفظ</button>
</div>
