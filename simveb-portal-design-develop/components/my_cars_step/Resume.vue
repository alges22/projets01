<script setup>
    import {useSellStore} from "~/stores/myCarSell"
    import {useUserStore} from "~/stores/user.js"
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    const { $awn } = useNuxtApp()
    const store = useSellStore()
    const userStore = useUserStore()
    const {loading, buyer} = storeToRefs(store)

    const submit = () => {
        store.addToCart()
            .then((response) => {
                $awn.success('Demande enregistrée avec succès')

                store.nextStep()
            })
            .catch((error) => {
                $awn.alert(error)
            })
    }
</script>

<template>
    <div class="card py-16 px-32 mt-4 text-center">
        <h4 class="text-blue font-bold text-4xl">Récapitulatif de la vente</h4>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Informations sur le véhicule</h4>
        </div>

        <div class="px-8 py-4 grid grid-cols-2 gap-16">
            <table class="table-black">
                <tr>
                    <td>Pays d'origine du véhicule</td>
                    <th>Canada</th>
                </tr>
                <tr>
                    <td>Numéro du chassis</td>
                    <th>142424771</th>
                </tr>
                <tr>
                    <td>Marque</td>
                    <th>BMW</th>
                </tr>
                <tr>
                    <td>Modèle du véhicule</td>
                    <th>Corolla</th>
                </tr>
                <tr>
                    <td>Energie</td>
                    <th>Essence</th>
                </tr>
                <tr>
                    <td>Nombre de places assises</td>
                    <th>5 places max</th>
                </tr>
            </table>

            <table class="table-black">
                <tr>
                    <td>Numéro moteur</td>
                    <th>Illisible</th>
                </tr>
                <tr>
                    <td>Poids total (en kg)</td>
                    <th>1400</th>
                </tr>
                <tr>
                    <td>Poids à vide (en kg)</td>
                    <th>2300</th>
                </tr>
                <tr>
                    <td>Parc d'achat du véhicule</td>
                    <th>Wanted</th>
                </tr>
                <tr>
                    <td>Année de 1ere mise en circulation</td>
                    <th>2012</th>
                </tr>
                <tr>
                    <td>Type du véhicule</td>
                    <th>SUV</th>
                </tr>
            </table>
        </div>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Informations sur l'acheteur</h4>
        </div>
        <div class="px-8 py-4 grid grid-cols-2 gap-16">
            <table class="table-black">
                <tr>
                    <td>Nom</td>
                    <th>{{ buyer.firstname ? buyer.firstname : buyer.ifu }}</th>
                </tr>
                <tr>
                    <td>Prénoms</td>
                    <th>{{ buyer.lastname ? buyer.lastname : buyer.social_reason }}</th>
                </tr>
                <tr>
                    <td>Date de naissance</td>
                    <th>{{ buyer.birth_date }}</th>
                </tr>
                <tr>
                    <td>Lieu de naissance</td>
                    <th>{{ buyer.birth_place ? buyer.birth_place : buyer.seat }}</th>
                </tr>
                <tr>
                    <td>Nationalité</td>
                    <th>{{ buyer.origin_country }}</th>
                </tr>
                <tr>
                    <td>Sexe</td>
                    <th>{{ buyer.gender }}</th>
                </tr>
            </table>

            <table class="table-black">
                <tr>
                    <td>NPI</td>
                    <th>{{ store.nipBuyer }}</th>
                </tr>
                <tr>
                    <td>Référence de BFU</td>
                    <th>{{ buyer.bfu }}</th>
                </tr>
                <tr>
                    <td>Téléphone</td>
                    <th>{{ buyer.telephone }}</th>
                </tr>
                <tr>
                    <td>Adresse email</td>
                    <th>{{ buyer.email }}</th>
                </tr>
                <tr>
                    <td>Domicile</td>
                    <th>{{ buyer.address }}</th>
                </tr>
            </table>
        </div>


        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Informations sur propriétaire</h4>
        </div>
        <div class="px-8 py-4 grid grid-cols-2 gap-16">
            <table class="table-black">
                <tr>
                    <td>Nom</td>
                    <th>{{ userStore.user.identity.lastname }}</th>
                </tr>
                <tr>
                    <td>Prénoms</td>
                    <th>{{ userStore.user.identity.firstname }}</th>
                </tr>

                <tr>
                    <td>Date de naissance</td>
                    <th>{{ userStore.user.identity.birthdate }}</th>
                </tr>
                <tr>
                    <td>Lieu de naissance</td>
                    <th>{{ userStore.user.identity.birth_place }}</th>
                </tr>
                <tr>
                    <td>Nationalité</td>
                    <th>Béninoise</th>
                </tr>
                <tr>
                    <td>Sexe</td>
                    <th></th>
                </tr>
            </table>

            <table class="table-black">
                <tr>
                    <td>NPI</td>
                    <th>{{ userStore.user.identity.npi }}</th>
                </tr>
                <tr>
                    <td>Référence de BFU</td>
                    <th>{{ userStore.user.identity.bfu }}</th>
                </tr>
                <tr>
                    <td>Téléphone</td>
                    <th>{{ userStore.user.identity.telephone }}</th>
                </tr>
                <tr>
                    <td>Adresse email</td>
                    <th>{{ userStore.user.identity.email }}</th>
                </tr>
                <tr>
                    <td>Domicile</td>
                    <th>{{ userStore.user.identity.address }}</th>
                </tr>
            </table>
        </div>

        <div class="mt-16 flex-row flex">
            <button class="text-blue font-bold text-2xl" @click="store.previousStep()">
                <font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
                Précédent
            </button>

            <div class="ms-auto">
                <button class="btn-blue mx-4" :disabled="loading" @click="submit">
                    Enregistrer la demande
                </button>
            </div>
        </div>
    </div>
</template>


<style lang="css" scoped>

</style>