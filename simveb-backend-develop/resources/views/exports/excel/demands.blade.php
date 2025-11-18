<table>
    <thead>
    <tr>
        <th><b>Référence</b></th>
        <th><b>Soumise le</b></th>
        <th><b>VIN</b></th>
        <th><b>Service</b></th>
        <th><b>Durée Normale</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($demands as $demand)
        <tr>
            <td>{{$demand->reference}}</td>
            <td>{{$demand->submitted_at}}</td>
            <td>{{$demand->vehicle?->vin ?? 'N/A'}}</td>
            <td>{{$demand->service?->name ?? 'N/A'}}</td>
            <td>{{$demand->duration}}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>
