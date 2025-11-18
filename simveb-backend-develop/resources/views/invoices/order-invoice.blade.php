<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Facture de commande | Simveb </title>

    <link rel="stylesheet" href="{{ public_path('assets/css/order-invoice.css') }}">
</head>

<body>
    <div>
        <div>
            <table class="w-full table-fixed">
                <tr>
                    <td>
                        <div>
                            <div class="font-bold text-xl">Facture de commande</div>
                            <div>Référence: {{ $invoice->reference }}</div>
                        </div>
                    </td>
                    <td class="text-right">
                        <img src="{{ public_path('img/logo.png') }}" alt="logo.png" width="230px">
                    </td>
                </tr>
            </table>

            <table class="mt-10 w-full table-fixed">
                <tr>
                    <td>
                        <span class="font-medium">
                            {{ !empty($invoice->model->profile) ? $invoice->model->profile->identity->fullName : $invoice->model->institution->name }}
                        </span>
                    </td>
                    <td></td>
                    <td>
                        <table class="w-full table-fixed bg-slate-100 px-2 py-1">
                            <tr>
                                <td>Montant:</td>
                                <td class="text-right">{{ amountFormat($invoice->amount) . ' FCFA' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div>
                Payé le {{ $invoice->created_at->format('d/m/Y à H:i') }}
            </div>

            <div class="mt-10">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="border">
                            <th class="py-2">Référence</th>
                            <th class="border">Service</th>
                            <th class="border">Quantité</th>
                            <th class="border">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->model->demands as $index => $demand)
                            <tr class="border text-center @if ($index % 2 == 0) bg-slate-100 @endif">
                                <td class="border py-2">
                                    {{ $demand->reference }}
                                </td>
                                <td class="border">
                                    {{ $demand->service->name }}
                                </td>
                                <td class="border">
                                    1
                                </td>
                                <td class="border">
                                    {{ $demand->pivot->amount }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="border text-center">
                            <td class="py-2 uppercase font-medium">
                                TOTAL
                            </td>
                            <td></td>
                            <td></td>
                            <td class="border">
                                {{ amountFormat($invoice->amount) . ' FCFA' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="text-center mt-32 pb-2">
            <span class="text-gray-600">
                © 2024 Simveb BJ
            </span>
        </footer>
    </div>
</body>

</html>
