@extends('layouts.master')
@section('title', 'استيراد عينة')

@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الإدارة</h4><span class="text-muted mt-1 tx-13 ms-2 mb-0">/ استيراد عينة</span>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">استيراد عينة من الشركات</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{!! session('success') !!}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
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

                <div class="card card-body border-primary mb-4">
                    <h5 class="card-title">تعليمات</h5>
                    <p class="card-text">
                        1. اختر المسح الذي ترغب في استيراد عينة له.<br>
                        2. قم بإعداد ملف إكسل (.xlsx, .xls) أو CSV (.csv).<br>
                        3. يجب أن يحتوي الملف على عمود واحد فقط مع ترويسة (في السطر الأول) : <strong>entident</strong>.<br>
                        4. يجب أن تحتوي الأسطر التالية على <strong>المعرفات الفريدة (ID)</strong> للشركات.
                    </p>
                </div>

                <form action="{{ route('admin.echantillons.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="enquete_id">اختر مسحًا <span class="text-danger">*</span></label>
                        <select name="enquete_id" id="enquete_id" class="form-control" required>
                            <option value="">-- اختر مسحًا --</option>
                            @foreach($enquetes as $enquete)
                                <option value="{{ $enquete->id }}">{{ $enquete->titre }} ({{ $enquete->statut }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mt-3">
                        <label for="echantillon_file">ملف العينة <span class="text-danger">*</span></label>
                        <input type="file" name="echantillon_file" class="form-control-file" id="echantillon_file" required>
                        <small class="form-text text-muted">الملفات المدعومة: .xlsx, .xls, .csv.</small>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="fas fa-upload mr-2"></i>استيراد العينة
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
