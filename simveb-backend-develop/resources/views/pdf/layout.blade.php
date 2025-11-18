<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dépot de titre</title>
    <style>
        /* Styles CSS2 */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #000000;
        }

        .container {
            width: 100%;
            max-width: 1024px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        #logo {
            float: left;
        }

        .h-20 {
            height: 80px; /* équivalent à 28 rem en supposant que 1rem = 4px */
        }

        header {
            margin-bottom: 20px;
        }

        .px-4 {
            padding-left: 20px;
            padding-right: 20px;
        }

        .bg-blue-100 {
            background-color: #ebf8ff;
        }

        .text-blue-600 {
            color: #3182ce;
        }

        .text-2xl {
            font-size: 24px; /* équivalent à 1.5 rem en supposant que 1rem = 16px */
            font-weight: bold;
        }

        .text-base {
            font-size: 16px; /* équivalent à 1 rem en supposant que 1rem = 16px */
        }

        .table-fixed {
            width: 100%;
            border-collapse: collapse;
        }

        .border-b {
            border-bottom: 1px solid #000000;
        }

        .p-4 {
            padding: 16px; /* équivalent à 1 rem en supposant que 1rem = 16px */
        }

        .font-semibold {
            font-weight: bold;
        }

        /* Float-based layout */
        .left-side {
            float: left;
            width: 48%; /* ajuster selon les besoins */
            margin-right: 4%; /* ajuster selon les besoins */
        }

        .right-side {
            float: right; /* Changer float: left à float: right */
            width: 48%; /* ajuster selon les besoins */
        }

        .mt-2 {
            margin-top: 32px; /* equivalent à 2rem avec 1rem = 16px */
        }

        .mt-3 {
            margin-top: 48px; /* equivalent à 3rem avec 1rem = 16px */
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: end;
        }

    </style>
</head>
<body class="">
<header class="">
    <div id="logo">
        <img class="h-20" src="{{ public_path('assets/img/anatt.png') }}">
    </div>
</header>

@yield('content')

</body>
</html>
