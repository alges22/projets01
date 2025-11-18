<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Liste des demandes | Simveb </title>

    <link rel="stylesheet" href="{{ public_path('assets/css/order-invoice.css') }}">
</head>

<body>
    <div class="">
        <div class="px-14 pt-14">
            <table class="w-full table-fixed">
                <tr>
                    <td>
                        <img src="{{ public_path('img/logo.png') }}" alt="logo.png" class="">
                    </td>
                </tr>
            </table>

            <div class="mt-10">
                <span>Généré le {{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
            </div>

            <div class="mt-10">
                <table class="w-full table-auto">
                    <thead>
                    <tr class="border">
                        <th class="py-2"><b>Référence</b></th>
                        <th class="py-2"><b>Soumise le</b></th>
                        <th class="py-2"><b>VIN</b></th>
                        <th class="py-2"><b>Service</b></th>
                        <th class="py-2"><b>Durée Normale</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($demands as $demand)
                        <tr class="border bg-slate-100">
                            <td class="py-2">{{$demand->reference}}</td>
                            <td class="py-2">{{$demand->submitted_at}}</td>
                            <td class="py-2">{{$demand->vehicle?->vin ?? 'N/A'}}</td>
                            <td class="py-2">{{$demand->service?->name ?? 'N/A'}}</td>
                            <td class="py-2">{{$demand->duration}}</td>
                            <td class="py-2"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="text-center mt-16 pb-2">
            <span class="text-gray-600">
                © 2024 Simveb BJ
            </span>
        </footer>
    </div>
</body>

</html>
