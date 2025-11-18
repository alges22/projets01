<script setup>
    import {useApi} from "~/helpers/useApi";

    useHead({
        title: 'SIMVEB - Mes achats',
    })

    const api = useApi()

    const purchases = ref([])

    onMounted(() => {
        api({
            method: 'GET',
            url: '/client/get-bought-vehicles'
        }).then((response) => response.data)
            .then((response) => {
                purchases.value = response.data
            })
    })
</script>

<template>
    <div>
        <h4 class="text-center text-primary text-3xl font-bold mt-8">Mes achats de véhciule</h4>

        <div class="flex flex-row mt-8">
            <h4 class="text-center text-primary text-xl font-bold flex items-center">Tous les véhicules</h4>
            <div class="flex ms-auto">
                <input class="form-control" placeholder="Recherche.." type="text"/>
            </div>
        </div>
        <div class="card p-8 mt-8">
            <table>
                <thead>
                <tr>
                    <td>Images</td>
                    <td>Ref N° de vente</td>
                    <td>Ref certificat de cession</td>
                    <td>Marque / Modèle</td>
                    <td>Date de mise en circulation</td>
                    <td>Numéro chassis</td>
                    <td>Immatriculation</td>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="purchase in purchases" :key="purchase.id">
                        <td>
                            <img alt="Véhicule" class="rounded-md" src="/vehicule.jpeg" style="width: 70px">
                        </td>
                        <td>{{ purchase.vehicle.demands[0].reference }}</td>
                        <td>{{ purchase.vehicle.demands[0].model.reference }}</td>
                        <td>{{ purchase.vehicle.brand.name }} / {{ purchase.vehicle.vehicle_model }}</td>
                        <td>{{ purchase.vehicle.first_circulation_year }}</td>
                        <td>{{ purchase.vehicle.vin	}}</td>
                        <td>
                            <span class="rounded-md bg-gray-200 font-bold px-4 py-2">
                                {{ purchase.vehicle.immatriculation.number_label}}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="purchases.length === 0">
                        <td colspan="9">
                            Aucun achat de véhicule
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
table {
    @apply w-full
}

thead {
    @apply font-bold bg-gray-100 text-blue-900 text-sm
}

tr > td,
tr > th {
    padding: 20px;
    text-align: left;
}
</style>