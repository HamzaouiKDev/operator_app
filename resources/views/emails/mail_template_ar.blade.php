<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sujet }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');

        body {
            margin: 0;
            padding: 0;
            background-color: #f2f4f6;
            font-family: 'Cairo', Arial, sans-serif;
            color: #333333;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f2f4f6;
            padding: 40px 20px;
        }
        .email-container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border: 1px solid #e1e8ed;
        }
        .header-table {
            width: 100%;
            padding: 30px;
            border-bottom: 1px solid #e1e8ed;
        }
        .header-right {
            text-align: right;
            font-size: 14px;
            line-height: 1.5;
            color: #555;
        }
        .header-left {
            text-align: left;
        }
        .header-left img {
            max-width: 90px;
        }
        .email-body {
            padding: 30px 40px;
            text-align: right;
            font-family: 'Cairo', 'Times New Roman', Times, serif;
            font-size: 16px;
            line-height: 1.8;
        }
        .date-section {
            text-align: left;
            padding-bottom: 30px;
            font-size: 15px;
        }
        .address-section {
            padding-bottom: 20px;
        }
        .subject-section {
            padding: 20px 0;
            font-weight: bold;
            font-size: 17px;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 25px;
        }
        .body-content p {
            margin: 0 0 1.2em 0;
            text-align: right; /* CORRECTION: Alignement à droite */
        }
        .closing-section {
            padding-top: 30px;
            text-align: left;
        }
        .footer-cell {
            padding: 30px;
            font-size: 12px;
            text-align: center;
            color: #8899a6;
            background-color: #f8f9fa;
        }
        .footer-cell a {
            color: #3498db;
            text-decoration: none;
        }
        .ltr-text {
            direction: ltr;
            unicode-bidi: bidi-override;
            display: inline-block;
        }

    </style>
</head>
<body>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table class="email-container" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <!-- En-tête -->
                    <tr>
                        <td>
                            <table class="header-table">
                                <tr>
                                    <td class="header-right">
                                        <strong>وزارة الإقتصاد والتخطيط</strong><br>
                                        المعهد الوطني للإحصاء
                                    </td>
                                    <td class="header-left">
                                        <img src="https://www.ins.tn/themes/custom/ins/logo.png" alt="Logo INS">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Corps du message -->
                    <tr>
                        <td class="email-body">
                            <div class="date-section">
                              <span class="ltr-text">{{ \Carbon\Carbon::now()->format('Y/m/d') }}</span>:  تونس في: 
                            </div>

                            <div class="address-section">
                                من المدير العام للمعهد الوطني للإحصاء<br>
                                إلى السيّد الرئيس المدير العام<br>
                                @if(isset($entreprise) && $entreprise)
                                    <strong>{{ $entreprise->nom_entreprise }}</strong>
                                @endif
                            </div>

                            <div class="subject-section">
                                <u>الموضوع: {{ $sujet }}</u><br>
                                <u>المرفقات: إستمارة المسح.</u>
                            </div>

                            <div class="body-content">
                                <p>{!! nl2br(e($corps)) !!}</p>
                            </div>

                            <div class="closing-section">
                                 وتقبّلوا السيّد الرّئيس المدير العام فائق عبارات التقدير والاحترام.
                                 <br><br>
                                 <strong>والسّــــلام</strong>
                            </div>
                        </td>
                    </tr>
                    <!-- Pied de page -->
                     <tr>
                        <td class="footer-cell">
                            70 نهج الشام  - 1002 تونس بلفدير – الجمهوريّة التونسيّة  <br>
                           الهاتف: <a href="tel:+21671891002" class="ltr-text">(+216) 71 891 002</a> | الفاكس: <span class="ltr-text">(+216) 71 792 559</span> | البريد الإلكتروني: <a href="mailto:INS@ins.tn">INS@ins.tn</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
