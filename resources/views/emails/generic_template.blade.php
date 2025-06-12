<!DOCTYPE html>
<html lang="fr" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sujet }}</title>
</head>
<body style="font-family: sans-serif; text-align: right;">
    {{-- La fonction nl2br transforme les sauts de ligne en <br> HTML --}}
    {!! nl2br(e($contenuMessage)) !!}
</body>
</html>