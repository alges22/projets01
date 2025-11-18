<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Liste des commandes | Simveb </title>

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
                        <th class="py-2"><b>Npi du demandeur</b></th>
                        <th class="py-2"><b>Ifu du demandeur</b></th>
                        <th class="py-2"><b>Référence</b></th>
                        <th class="py-2"><b>Créé le</b></th>
                        <th class="py-2"><b>Montant Total</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($orders as $order)
                        <tr class="border bg-slate-100">
                            <td class="py-2">{{$order->profile?->identity?->npi}}</td>
                            <td class="py-2">{{$order->profile?->institution?->ifu}}</td>
                            <td class="py-2">{{$order->reference}}</td>
                            <td class="py-2">{{$order->created_at}}</td>
                            <td class="py-2">{{$order->transaction->total_amount}}</td>
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
