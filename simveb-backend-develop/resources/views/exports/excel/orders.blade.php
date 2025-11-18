<table>
    <thead>
    <tr>
        <th><b>Npi du demandeur</b></th>
        <th><b>Ifu du demandeur</b></th>
        <th><b>Référence</b></th>
        <th><b>Créé le</b></th>
        <th><b>Montant Total</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($orders as $order)
        <tr>
            <td>{{$order->profile?->identity?->npi}}</td>
            <td>{{$order->profile?->institution?->ifu}}</td>
            <td>{{$order->reference}}</td>
            <td>{{$order->created_at}}</td>
            <td>{{$order->transaction->total_amount}}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>
