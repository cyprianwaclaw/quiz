<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kod weryfikacyjny</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Manrope', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            text-align: center;
            cursor: default;
        }

        .prevent-select {
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .allow-select {
            -webkit-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff !important;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #222;
            font-weight: 700;
        }

        h3 {
            color: #0066cc !important;
            font-size: 24px;
            font-weight: 700;
            background: #e6f0ff;
            padding: 10px 16px;
            display: inline-block;
            border-radius: 5px;
            cursor: text;
        }

        p {
            font-size: 16px;
            font-weight: 400;
            margin-bottom: 20px;
        }

        .link {
            font-size: 15px;
            font-weight: 400;
            margin-bottom: 20px;
        }

        .link-click {
            color: #0066cc;
            font-size: 16px;
            font-weight: 600;
            text-decoration: underline;
            cursor: pointer;
        }

        .footer {
            margin-top: 24px;
            font-size: 12px;
            color: #666;
        }

    </style>
</head>

<body>
    <div class="container prevent-select">
        <h2>Witaj {{ $userName }}!</h2>
        <p>Twój kod weryfikacyjny to:</p>
        <h3 class="allow-select">{{ $verificationCode }}</h3>

{{--
        <p>Wprowadź ten kod w wyznaczonym miejscu.</p>
        @if(($pageName == 'rejestracja'))
        <p class="link">Lub kliknij <a href="http://localhost:3000/rejestracja?email={{ $userEmail }}&verification_code={{ $verificationCode }}" class="link-click">tutaj</a></p>
        @endif --}}

        <p>Wprowadź ten kod w wyznaczonym miejscu.</p>
        @if($pageName == 'rejestracja')
        <p class="link">
            Lub kliknij
            <a href="{{ env('FRONT_DOMAIN', 'http://localhost:3000') }}/rejestracja?email={{ $userEmail }}&verification_code={{ $verificationCode }}" class="link-click">tutaj</a>
        </p>
        @endif


    </div>
</body>


</html>
