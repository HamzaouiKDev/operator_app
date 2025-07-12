<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl"> {{-- Supposant une configuration RTL pour l'arabe --}}
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>تسجيل الدخول - المعهد الوطني للاحصاء</title>
    <link rel="icon" href="{{ URL::asset('assets/img/brand/ins.svg') }}" type="image/svg+xml"/>!

    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

    {{-- <link rel="dns-prefetch" href="//fonts.gstatic.com"> --}}
    {{-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> --}}

    <link href="{{URL::asset('assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css')}}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; /* Assurez-vous qu'il n'y a pas de marges par défaut */
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-row {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            overflow: hidden;
            background: white;
            display: flex; /* Pour que les colonnes s'alignent correctement */
        }
        .login-image-section {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 20px;
            color: white;
            position: relative;
            overflow: hidden; /* Important pour s'assurer que ::after ne dépasse pas */
            width: 58.333333%; /* col-xl-7 */
            /* L'animation pulse a été retirée d'ici */
        }
        /* Pas de keyframes pulse ici */

        .login-image-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('{{URL::asset("assets/img/media/ins.svg")}}') no-repeat center center;
            background-size: 80%;
            opacity: 0.3;
            z-index: 1; /* Derrière le texte */
        }
        .login-image-section h2,
        .login-image-section p {
            font-weight: 700;
            margin-bottom: 15px;
            z-index: 2; /* Au-dessus de ::after */
            text-align: center;
            position: relative; /* Pour s'assurer que z-index fonctionne correctement */
        }
        .login-image-section p {
            opacity: 0.9; /* Opacité par défaut du paragraphe */
        }

        /* Animation pour le texte dans la section image */
        /* animate__delay-0.5s sera appliqué via une classe directement sur l'élément p */
        .animate__delay-0_5s { /* Correction du nom de la classe pour correspondre à l'usage */
             animation-delay: 0.5s;
        }

        .login-form-section {
            padding: 40px 30px;
            display: flex;
            align-items: center;
            justify-content: center; /* Centrer la carte de connexion */
            background: white;
            width: 41.666667%; /* col-xl-5 */
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        .login-logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            justify-content: center;
        }
        .login-logo img {
            height: 50px;
            margin-left: 10px; /* Ajustement pour RTL */
        }
        .login-logo h1 {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }
        .login-title {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
            text-align: center;
        }
        .login-subtitle {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            color: #2c3e50;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background-color: #f9f9f9;
            box-sizing: border-box;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        .invalid-feedback {
            font-size: 13px;
            margin-top: 5px;
            color: #e74c3c;
        }
        .btn-main-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            font-weight: 600;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            font-size: 16px;
        }
        .btn-main-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
            background: linear-gradient(135deg, #2980b9 0%, #1c6ea4 100%);
        }
        .form-check {
            margin-top: 15px;
            display: flex;
            align-items: center;
        }
        .form-check-input {
            margin-left: 10px;
            margin-right: 0;
            width: auto;
            height: auto;
        }
        .form-check-label {
            font-size: 14px;
            color: #7f8c8d;
            cursor: pointer;
        }
        /* Pour simuler les classes de colonnes Bootstrap si non utilisées globalement */
        .row.no-gutter {
            margin-left: 0;
            margin-right: 0;
        }
        .row.no-gutter > [class*='col-'] {
            padding-left: 0;
            padding-right: 0;
        }

        @media (max-width: 767px) {
            .login-row {
                flex-direction: column;
            }
            .login-image-section {
                display: none;
            }
            .login-form-section {
                min-height: 100vh;
                width: 100%;
                padding: 30px 15px;
            }
            .login-card {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="login-container animate__animated animate__fadeIn"> {{-- Animation globale pour la page --}}
            <div class="login-row no-gutter">
                <div class="login-image-section">
                    {{-- Animation appliquée directement sur les éléments de texte --}}
                    <h2 class="animate__animated animate__fadeIn">المعهد الوطني للاحصاء</h2>
                    <p class="animate__animated animate__fadeIn animate__delay-0_5s">مرحبًا بكم في تطبيقة إدارة مركز النداء</p>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-5 login-form-section">
                    <div class="login-card">
                        <div class="login-logo">
                            <a href="{{ url('/' . ($page='Home' ?? '')) }}">
                                <img src="{{URL::asset('assets/img/brand/ins.svg')}}" alt="logo">
                            </a>
                        </div>
                        <h2 class="login-title">مرحبا بك</h2>
                        <p class="login-subtitle">تسجيل الدخول إلى حسابك</p>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group">
                                <label for="email">البريد الإلكتروني</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">كلمة المرور</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('تذكرني') }}
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-main-primary">
                                {{ __('تسجيل الدخول') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Login page loaded with modern design.');
        });
    </script>
</body>
</html>