@extends('layouts.master')

@section('css')
    {{-- Vos CSS existants --}}
    <link href="{{URL::asset('assets/plugins/iconfonts/plugin.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    {{-- Ajout de Google Fonts pour une typographie professionnelle --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body, h1, h2, h3, h4, h5, h6, .main-content-title, p, span, div, .tx-13, .tx-12 {
            font-family: 'Cairo', sans-serif !important;
        }

        /* ----- Style Général ----- */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease-in-out;
            background-color: #fff;
            height: 100%; /* Assurer que toutes les cartes ont la même hauteur */
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
        }
        .card-title {
            font-weight: 700;
            color: #1a2130;
            margin-bottom: 0;
        }
        .step-number {
            background-color: #3b82f6;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-left: 10px; /* Adapté pour RTL */
        }

        /* ----- Style des formulaires d'import ----- */
        .custom-file-upload {
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .custom-file-upload:hover {
            background-color: #f9fafb;
            border-color: #3b82f6;
        }
        .custom-file-upload .upload-icon {
            font-size: 2.5rem;
            color: #9ca3af;
        }
        .custom-file-upload p {
            margin-bottom: 0;
            font-weight: 600;
            color: #6b7280;
        }
        input[type="file"] {
            display: none;
        }
        .btn-import {
            width: 100%;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.75rem;
        }

        /* ----- Alertes améliorées ----- */
        .alert {
            border-radius: 10px;
        }
        .alert h5 {
            font-weight: 700;
        }
        .skipped-details ul {
            padding-right: 20px;
        }
    </style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between" dir="rtl">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الإدارة</h4>
            <span class="text-muted mt-1 tx-13 mx-2 mb-0">/</span>
            <span class="content-title mb-0 my-auto">استيراد البيانات</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="row">
        {{-- CARTE POUR L'IMPORT DES ENTREPRISES --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <span class="step-number">1</span>
                    <h4 class="card-title">استيراد الشركات</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    @if(session('success_entreprises'))
                        <div class="alert alert-success">{!! session('success_entreprises') !!}</div>
                    @endif
                    @if(session('error_entreprises'))
                        <div class="alert alert-danger font-weight-bold">{{ session('error_entreprises') }}</div>
                    @endif
                    <form action="{{ route('admin.entreprises.import.store') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                        @csrf
                        <div class="form-group">
                            <label for="file-entreprises" class="custom-file-upload">
                                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                <p>اختر ملف الشركات</p>
                            </label>
                            <input type="file" name="file-entreprises" id="file-entreprises" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-import mt-3"><i class="fas fa-upload mr-2"></i>استيراد</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- CARTE POUR L'IMPORT DES TÉLÉPHONES --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <span class="step-number" style="background-color: #22c55e;">2</span>
                    <h4 class="card-title">استيراد الهواتف</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    @if(session('success_telephones'))
                        <div class="alert alert-success">{!! session('success_telephones') !!}</div>
                    @endif
                    @if(session('error_telephones'))
                        <div class="alert alert-danger font-weight-bold">{{ session('error_telephones') }}</div>
                    @endif
                    <form action="{{ route('admin.entreprises.import.telephones') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                        @csrf
                        <div class="form-group">
                             <label for="file-telephones" class="custom-file-upload">
                                <i class="fas fa-phone upload-icon"></i>
                                <p>اختر ملف الهواتف</p>
                            </label>
                            <input type="file" name="file-telephones" id="file-telephones" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-import mt-3"><i class="fas fa-phone mr-2"></i>استيراد</button>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- CARTE POUR L'IMPORT DES EMAILS --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <span class="step-number" style="background-color: #0ea5e9;">3</span>
                    <h4 class="card-title">استيراد الإيميلات</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    @if(session('success_emails'))
                        <div class="alert alert-success">{!! session('success_emails') !!}</div>
                    @endif
                    @if(session('error_emails'))
                        <div class="alert alert-danger font-weight-bold">{{ session('error_emails') }}</div>
                    @endif
                    @if(session('skipped_emails') && count(session('skipped_emails')) > 0)
                        <div class="alert alert-warning skipped-details">
                            <h5><i class="fas fa-exclamation-triangle"></i> تفاصيل الإيميلات التي لم يتم استيرادها</h5>
                            <div style="max-height: 150px; overflow-y: auto;">
                                <ul class="list-group list-group-flush">
                                    @foreach(session('skipped_emails') as $skipped)
                                        <li class="list-group-item bg-transparent">
                                            <strong>الإيميل:</strong> {{ $skipped['email'] }} <br>
                                            <strong class="text-danger">السبب:</strong> {{ $skipped['raison'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    <form action="{{ route('admin.entreprises.import.emails') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                        @csrf
                        <div class="form-group">
                            <label for="file-emails" class="custom-file-upload">
                                <i class="fas fa-envelope upload-icon"></i>
                                <p>اختر ملف الإيميلات</p>
                            </label>
                            <input type="file" name="file-emails" id="file-emails" required>
                        </div>
                        <button type="submit" class="btn btn-info btn-import mt-3"><i class="fas fa-envelope mr-2"></i>استيراد</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- CARTE POUR L'IMPORT DES CONTACTS --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <span class="step-number" style="background-color: #f97316;">4</span>
                    <h4 class="card-title">استيراد جهات الاتصال</h4>
                </div>
                <div class="card-body d-flex flex-column">
                    @if(session('success_contacts'))
                        <div class="alert alert-success">{!! session('success_contacts') !!}</div>
                    @endif
                    @if(session('error_contacts'))
                        <div class="alert alert-danger font-weight-bold">{{ session('error_contacts') }}</div>
                    @endif
                    <div class="alert alert-warning p-3 mb-4">
                        <p class="mb-1"><strong>تعليمات:</strong> الأعمدة المطلوبة هي:</p>
                        <code class="text-dark font-weight-bold d-block" style="font-size: 0.85rem;">
                            entident, nom, prenom, civilite, fonction, telephone, email
                        </code>
                    </div>
                    <form action="{{ route('admin.contacts.import.store') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                        @csrf
                        <div class="form-group">
                            <label for="file-contacts" class="custom-file-upload">
                                <i class="fas fa-users upload-icon"></i>
                                <p>اختر ملف جهات الاتصال</p>
                            </label>
                            <input type="file" name="file-contacts" id="file-contacts" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-import mt-3">
                            <i class="fas fa-users mr-2"></i>استيراد
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
