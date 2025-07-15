@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #435ebe; --primary-light: #eef0ff;
            --success-color: #10b981; --success-light: #dcfce7;
            --warning-color: #f59e0b; --warning-light: #fefce8;
            --danger-color: #ef4444;  --danger-light: #fee2e2;
            --info-color: #3b82f6;    --info-light: #dbeafe;
            --purple-color: #8b5cf6;  --purple-light: #f5f3ff;
            --teal-color: #14b8a6;    --teal-light: #ccfbf1;
            --gray-color: #6b7280;    --gray-light: #f3f4f6;

            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --bg-white: #ffffff;
            --border-color: #e5e7eb;
        }
        body, h1, h2, h3, h4, h5, h6, p, span, div { font-family: 'Cairo', sans-serif !important; }

        .kpi-card {
            background-color: var(--bg-white);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            padding: 1.25rem;
            transition: all 0.3s ease;
        }
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.07);
            border-color: var(--primary-color);
        }
        .kpi-card .icon-wrapper {
            flex-shrink: 0;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            margin-left: 1rem; /* Pour RTL */
            font-size: 1.5rem;
        }
        .kpi-card .kpi-content h3 {
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }
        .kpi-card .kpi-content h6 {
            font-weight: 600;
            color: var(--text-muted);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .chart-card {
            background-color: var(--bg-white);
            border-radius: 1rem;
            border: 1px solid var(--border-color);
            padding: 1.5rem;
        }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between" dir="rtl">
        <div class="left-content">
            <h2 class="main-content-title tx-24 mg-b-1">لوحة تحكم الناظر</h2>
            <p class="mg-b-0 text-muted">نظرة عامة على أداء مركز النداء</p>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid" dir="rtl">


    <h4 class="mb-4 fw-bold">
        @if($selectedTeleoperateur)
            نتائج المشغل : <span style="color:var(--primary-color)">{{ $selectedTeleoperateur->name }}</span>
        @else
            الملخص العام
        @endif
    </h4>

    <div class="row g-4">
        <div class="col-lg-3 col-md-6"><div class="kpi-card"><div class="icon-wrapper" style="background-color: var(--primary-light); color: var(--primary-color);"><i class="fas fa-layer-group"></i></div><div class="kpi-content"><h6>@if($selectedTeleoperateur) العينات الموكلة @else الإجمالي @endif</h6><h3>{{ $totalEchantillons ?? 0 }}</h3></div></div></div>
        <div class="col-lg-3 col-md-6"><div class="kpi-card"><div class="icon-wrapper" style="background-color: var(--success-light); color: var(--success-color);"><i class="fas fa-check-circle"></i></div><div class="kpi-content"><h6>مكتمل</h6><h3>{{ $nombreEchantillonsComplets ?? 0 }}</h3></div></div></div>
        <div class="col-lg-3 col-md-6"><div class="kpi-card"><div class="icon-wrapper" style="background-color: var(--warning-light); color: var(--warning-color);"><i class="fas fa-puzzle-piece"></i></div><div class="kpi-content"><h6>جزئي</h6><h3>{{ $nombreEchantillonsPartiels ?? 0 }}</h3></div></div></div>
        <div class="col-lg-3 col-md-6"><div class="kpi-card"><div class="icon-wrapper" style="background-color: var(--danger-light); color: var(--danger-color);"><i class="fas fa-ban"></i></div><div class="kpi-content"><h6>رفض</h6><h3>{{ $nombreEchantillonsRefus ?? 0 }}</h3></div></div></div>
        <div class="col-lg-3 col-md-6"><div class="kpi-card"><div class="icon-wrapper" style="background-color: var(--info-light); color: var(--info-color);"><i class="fas fa-calendar-check"></i></div><div class="kpi-content"><h6>موعد (بدون جزئي)</h6><h3>{{ $nombreRdvSansPartiel ?? 0 }}</h3></div></div></div>
        <div class="col-lg-3 col-md-6"><div class="kpi-card"><div class="icon-wrapper" style="background-color: var(--teal-light); color: var(--teal-color);"><i class="fas fa-calendar-plus"></i></div><div class="kpi-content"><h6>موعد (مع جزئي)</h6><h3>{{ $nombreRdvAvecPartiel ?? 0 }}</h3></div></div></div>
        <div class="col-lg-3 col-md-6"><div class="kpi-card"><div class="icon-wrapper" style="background-color: var(--purple-light); color: var(--purple-color);"><i class="fas fa-phone-volume"></i></div><div class="kpi-content"><h6>إعادة إتصال</h6><h3>{{ $nombreEchantillonsSuivi ?? 0 }}</h3></div></div></div>
        <div class="col-lg-3 col-md-6"><div class="kpi-card"><div class="icon-wrapper" style="background-color: var(--gray-light); color: var(--gray-color);"><i class="fas fa-phone-slash"></i></div><div class="kpi-content"><h6>إستحالة</h6><h3>{{ $nombreEchantillonsImpossible ?? 0 }}</h3></div></div></div>
    </div>
 ...
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('supervisor.dashboard') }}" method="GET" class="row align-items-end gy-2">
            <div class="col-md-5 col-lg-4">
                <label for="teleoperateur_id" class="form-label fw-bold">عرض حسب المشغل:</label>
                <select name="teleoperateur_id" id="teleoperateur_id" class="form-select form-select-lg" onchange="this.form.submit()">
                    <option value="">-- عرض الإحصائيات العامة --</option>
                    @foreach($teleoperateurs as $teleoperateur)
                        <option value="{{ $teleoperateur->id }}" @if(optional($selectedTeleoperateur)->id == $teleoperateur->id) selected @endif>{{ $teleoperateur->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-lg-2">
                <a href="{{ route('supervisor.dashboard') }}" class="btn btn-outline-secondary btn-lg w-100">إعادة تعيين</a>
            </div>
            
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('supervisor.report.pdf') }}" class="btn btn-danger btn-lg w-100">
                    <i class="fas fa-file-pdf me-2"></i> تحميل التقرير (PDF)
                </a>
            </div>
        </form>
    </div>
</div>
...
    <div class="mt-4">
        @if(isset($statutsChartData))
            <div class="chart-card">
                <h5 class="fw-bold mb-3">توزيع الحالات</h5>
                <div style="height: 450px; max-width: 900px; margin: auto;">
                    <canvas id="statutsGlobalChart"></canvas>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(isset($statutsChartData))
        const pieCtx = document.getElementById('statutsGlobalChart');
        if (pieCtx) {
            const pieData = @json($statutsChartData);
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: pieData.labels,
                    datasets: [{
                        data: pieData.data,
                        backgroundColor: pieData.colors,
                        borderColor: '#fff',
                        borderWidth: 4,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            rtl: true,
                            labels: {
                                font: { family: 'Cairo', size: 14, weight: 600 },
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            rtl: true,
                            titleFont: { family: 'Cairo', size: 14 },
                            bodyFont: { family: 'Cairo', size: 12 },
                            padding: 12
                        }
                    }
                }
            });
        }
    @endif
});
</script>
@endsection