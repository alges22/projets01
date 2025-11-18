<script lang="ts" setup>
    import {useApi} from "~/helpers/useApi";
    import dayjs from "dayjs";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    useHead({
        title: 'SIMVEB - Mes certificats',
    })

    const loading = ref(true)

    const type_choice = ref('certficat de cession')
    const show = ref('tableau')

    const api = useApi()

    const certificats = ref([])

    const generateCertificate = (id) => {
        api({
            method: 'GET',
            url: `/client/certificates/${id}`,
            responseType: "blob",
        }).then((response) => {
            const href = URL.createObjectURL(response.data);
            const link = document.createElement("a");
            link.href = href;
            link.setAttribute(
                "download",
                `${id}.pdf`
            );
            document.body.appendChild(link);
            link.click();

            document.body.removeChild(link);
            URL.revokeObjectURL(href);
        })
    }

    onMounted(() => {
        api({
            method: 'GET',
            url: '/client/certificates'
        }).then((response) => response.data)
            .then((response) => {
                certificats.value = response

                loading.value = false
        })
    })

</script>

<template>
    <div class="card p-16" v-if="show == 'tableau'">
        <div class="w-full border-b pb-4">
            <div class="inline-block p-5 border-x border-b" @click="type_choice='certficat de cession'" :class="type_choice=='certficat de cession' ? 'font-bold ' : 'bg-gray-100'">
                <button type="button">Certificat de cession</button>
            </div>
<!--            <div class="inline-block p-5 border-x border-b" :class="type_choice=='gages' ? 'font-bold ' : 'bg-gray-100'" @click="type_choice='gages'">-->
<!--                <button type="button">Gages</button>-->
<!--            </div>-->
<!--            <div class="inline-block float-end">-->
<!--                <button class="p-5 bg-blue-500 text-white rounded-lg font-bold text-xl" @click="show = 'choix de certificat'">Générer un certificat de cession</button>-->
<!--            </div>-->
        </div>
<!--        <div>-->
<!--            <div class="flex flex-row">-->
<!--                <p class="text-center font-bold flex items-center">Filtrer par</p>-->
<!--                <div class="ml-3">-->
<!--                    <select type="text" placeholder="Recherche.." class="form-control" >-->
<!--                        <option value="tout" :selected="true">Tout</option>-->
<!--                    </select>-->
<!--                </div>-->
<!--                <div class="ml-3">-->
<!--                    <input type="text" placeholder="Rechercher" class="form-control rounded-lg" />-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
        <div v-if="loading" class="flex w-full justify-center mt-4">
            <Loader />
        </div>
        <div class="p-8 mt-5" v-else>
            <table v-if="type_choice == 'certficat de cession'">
                <thead>
                    <tr>
<!--                        <td class="flex justify-center">-->
<!--                            <input type="checkbox" />-->
<!--                        </td>-->
                        <td>#</td>
                        <td>REF</td>
                        <td>ANCIEN PROPRIÉTAIRE</td>
                        <td>NOUVEAU PROPRIÉTAIRE</td>
                        <td>IMMATRICULATION</td>
                        <td>DATE ET HEURE</td>
                        <td>Actions</td>
<!--                        <td>ACTION</td>-->
<!--                        <td></td>-->
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(certificat, index) in certificats">
<!--                        <td class="flex justify-center">-->
<!--                            <input type="checkbox" />-->
<!--                        </td>-->
                        <td>{{ index + 1 }}</td>
                        <td>{{ certificat.reference }}</td>
                        <td>{{ certificat.old_owner.firstname }} {{ certificat.old_owner.lastname }}</td>
                        <td>{{ certificat.new_owner?.data?.firstname }} {{ certificat.new_owner?.data?.lastname }}</td>
                        <td>{{ certificat.vehicle[0].immatriculation }}</td>
                        <td>{{ dayjs(certificat.created_at).format('DD/MM/YYYY hh:mm') }}</td>
                        <td>
                            <button class="btn-sm btn btn-primary" @click="generateCertificate(certificat.id)">
                                <FontAwesomeIcon icon="fal fa-file-certificate" size="2xl" />
                            </button>
                        </td>
<!--                        <td>-->
<!--                            <span class="text-blue-500 font-bold">Voir les détails</span>-->
<!--                        </td>-->
<!--                        <td>-->
<!--                        </td>-->
                    </tr>
                    <tr v-if="certificats.length === 0">
                        <td colspan="9">
                            Aucun certificat disponible
                        </td>
                    </tr>
                </tbody>
            </table>
            <table v-if="type_choice=='gages'">
                <thead>
                    <tr>
                        <td class="flex justify-center">
                            <input type="checkbox" />
                        </td>
                        <td>#</td>
                        <td>REF</td>
                        <td>TYPE DE GAGE</td>
                        <td>NOM DU PROPRIÉTAIRE</td>
                        <td>IMMATRICULATION</td>
                        <td>DATE ET HEURE</td>
                        <td>STATUT</td>
                        <td>ACTION</td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="flex justify-center">
                            <input type="checkbox" />
                        </td>
                        <td>1</td>
                        <td>175357FG</td>
                        <td>NON-OPPOSITION</td>
                        <td>AVOUN CÉSAR</td>
                        <td>RBDLZ23</td>
                        <td>11/10/24, 12:30</td>
                        <td>
                            <span class="text-white font-bold bg-red-100 p-3 rounded-lg">expirer</span>
                        </td>
                        <td><p class="font-bold text-blue-500">Voir détails</p></td>
                        <td>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card p-16" v-if="show == 'choix de certificat'">
        <div class="p-16 mt-4 text-center">
            <!-- <h4 class="text-blue font-bold text-4xl">Base</h4> -->
            <p class="font-bold text-3xl text-blue-500">Choisissez le type de Certificat</p>
            <div class="w-10/12 text-left mx-auto">
                <div class="w-full flex flex-row justify-around">
                    <div class="mt-8  p-6 border">
                        <input id="depot_de_titre" class="" :disabled="true" placeholder="Numéro du certificat" type="radio" value="depot" name="depot_ou_reprise" >
                        <label for="depot_de_titre" class="ml-5">Certificat de non gage </label>
                    </div>
                    <div class="mt-8  p-6 border">
                        <input id="reprise_de_titre" class="" placeholder="Numéro du certificat" type="radio" :checked="true" value="retrait" name="depot_ou_reprise" >
                        <label for="reprise_de_titre" class="ml-5 text-blue-500 font-bold">Certificat de non opposition</label>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn btn-blue w-3/12 mx-auto" @click="show = 'choix de voiture'">Suivant</button>

                </div>
            </div>
        </div>
    </div>
    <div class="card p-16" v-if="show == 'choix de voiture'">
        <div class=" text-center">
            <!-- <h4 class="text-blue font-bold text-4xl">Sélection</h4> -->
            <p class="font-bold text-3xl text-blue-500">Choisissez le véhicule concerné</p>
            
            <div class="w-full p-8 mt-8">
                <table class="w-full">
                    <thead>
                        <tr>
                            <td></td>
                            <td>Numéro chassis</td>
                            <td>Immatriculation</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <img alt="Véhicule" class="rounded-md" src="/vehicule.jpeg" style="width: 70px">
                            </td>

                            <td>HKM17TL4577RREFF</td>
                            <td>
                                <span class="rounded-md bg-gray-200 font-bold px-4 py-2">
                                    CB 0124
                                </span>
                            </td>
                            <td>
                                <input type="radio" value="" name="choice">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img alt="Véhicule" class="rounded-md" src="/vehicule.jpeg" style="width: 70px">
                            </td>

                            <td>HKM17TL4577RREFF</td>
                            <td>
                                <span class="rounded-md bg-gray-200 font-bold px-4 py-2">
                                    CB 0124
                                </span>
                            </td>
                            <td>
                                <input type="radio" value="" name="choice">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img alt="Véhicule" class="rounded-md" src="/vehicule.jpeg" style="width: 70px">
                            </td>

                            <td>HKM17TL4577RREFF</td>
                            <td>
                                <span class="rounded-md bg-gray-200 font-bold px-4 py-2">
                                    CB 0124
                                </span>
                            </td>
                            <td>
                                <input type="radio" value="" name="choice">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img alt="Véhicule" class="rounded-md" src="/vehicule.jpeg" style="width: 70px">
                            </td>

                            <td>HKM17TL4577RREFF</td>
                            <td>
                                <span class="rounded-md bg-gray-200 font-bold px-4 py-2">
                                    CB 0124
                                </span>
                            </td>
                            <td>
                                <input type="radio" value="" name="choice">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img alt="Véhicule" class="rounded-md" src="/vehicule.jpeg" style="width: 70px">
                            </td>

                            <td>HKM17TL4577RREFF</td>
                            <td>
                                <span class="rounded-md bg-gray-200 font-bold px-4 py-2">
                                    CB 0124
                                </span>
                            </td>
                            <td>
                                <input type="radio" value="" name="choice">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img alt="Véhicule" class="rounded-md" src="/vehicule.jpeg" style="width: 70px">
                            </td>

                            <td>HKM17TL4577RREFF</td>
                            <td>
                                <span class="rounded-md bg-gray-200 font-bold px-4 py-2">
                                    CB 0124
                                </span>
                            </td>
                            <td>
                                <input type="radio" value="" name="choice">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center">
                    <button class="btn btn-blue w-3/12 mx-auto" @click="show = 'voir le certificat'">Générer le certificat</button>

                </div>
            </div>
        </div>
    </div>
    <div class="card p-16" v-if="show == 'voir le certificat'">
        <table>
                    <tr class="border-b-2 font-bold">
                        <td>Certificat</td>
                        <td>Numéro du certificat</td>
                        <td>Date</td>
                        <td>Statut</td>
                        <td>Imprimer</td>
                        <td>Télécharger</td>
                    </tr>
                <tbody>
                    <tr>
                        <td>Certificat de cession</td>
                        <td>BG210255585665254</td>
                        <td>2023-02-05</td>
                        <td>
                            <span class="bg-green-100 p-3 text-green-900 font-bold rounded">validé</span>
                        </td>
                        <td><button class="bg-green-900 px-3 py-1 text-white font-bold rounded text-xs">Imprimer</button></td>
                        <td><button class="bg-green-900 px-3 py-1 text-white font-bold rounded text-xs">Télécharger</button></td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-5 float-end">
                <button class="text-xl" @click="show = 'tableau'">Voir tous les certificats &gt;</button>
            </div>
    </div>
</template>

<style scoped>
table{
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