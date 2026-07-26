<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ url('assets/media/logos/favicon.ico') }}" />
    <title>Company dashboard</title>
    <style>
        body {
            background-color: white;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 90%;
            max-width: 400px;
            border: none;
            border-radius: 20px;
            padding: 40px; /* Increased padding for mobile devices */
            text-align: center;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            margin: 20px;
        }

        .logo {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin-bottom: 10px;
            font-size: 16px;
            background-color: #f0f0f0;
            border-radius: 8px;
            padding: 15px; /* Increased padding for better spacing */
        }

        li a {
            text-decoration: none;
            color: #000;
        }

        /* Media query for mobile devices */
        @media (max-width: 480px) {
            .container {
                padding: 30px; /* Further increased padding for smaller screens */
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <img class="logo" src="{{ url('assets/media/logos/demo39.svg') }}" alt="Logo">
        <h1>Choose Account</h1>
        <ul>
            @foreach ($user as $value)
                <li><a href="{{route('company.dashboard.change', [$value->id])}}">{{ $value->comp_name }}</a></li>
            @endforeach
        </ul>
        <hr class="mb-2">
        <ul class="mb-3">
            <li><a href="{{route('company.dashboard.change', [Auth::user()->id])}}">My dashboard</a></li>
        </ul>
    </div>

</body>

</html>
