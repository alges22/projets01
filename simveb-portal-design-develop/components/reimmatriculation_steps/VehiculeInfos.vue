<script lang="ts" setup>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import {useReimmatriculationStore} from "~/stores/reimmatriculation";

    const store = useReimmatriculationStore()

    const open = ref(false)
</script>

<template>
    <div class="card py-16 px-32 mt-4 text-center">
        <ModalComponent :open="open" @close="open = !open">
            <div class="card px-32 py-16 text-left">
                <div>
                    <label for="quittance_douane">Ce véhicule à subit un dépôt de titre, veuillez entrer la quittance de douane</label>
                    <input type="text" class="form-control" placeholder="Entrez la quittance de douane" id="quittance_douane">
                </div>

                <button class="btn btn-blue w-full" @click="store.nextStep()">Suivant</button>
            </div>  
        </ModalComponent>
        <h4 class="text-blue font-bold text-4xl">Informations</h4>

        <div class="px-8 py-4 text-left bg-blue-50 mt-8">
            <h4 class="text-blue text-2xl font-bold">Informations du véhicule</h4>
        </div>

        <div class="px-8 py-4 grid grid-cols-2 gap-16">
            <table class="table-black">
                <tr>
                    <td>Pays d'origine du véhicule</td>
                    <th>Canada</th>
                </tr>
                <tr>
                    <td>Numéro du chassis</td>
                    <th>JKLm014REF4485</th>
                </tr>
                <tr>
                    <td>Marque</td>
                    <th>BMV</th>
                </tr>
                <tr>
                    <td>Modèle du véhicule</td>
                    <th>Canada</th>
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
                    <th>2400</th>
                </tr>
                <tr>
                    <td>Poids à vide (en kg)</td>
                    <th>1300</th>
                </tr>
                <tr>
                    <td>Parc d'achat du véhicule</td>
                    <th>Wanted</th>
                </tr>
                <tr>
                    <td>Année de 1ere mise en circulation</td>
                    <th>2014</th>
                </tr>
                <tr>
                    <td>Type du véhicule</td>
                    <th>SUV</th>
                </tr>
            </table>
        </div>

        <div class="mt-16 flex-row flex">
            <button class="text-blue font-bold text-2xl" @click="store.previousStep()">
                <font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
                Précédent
            </button>

            <div class="ms-auto">
                <button class="btn-outline-blue mx-4">
                    <span class="font-bold text-2xl">Enregistrer</span>
                </button>

                <button class="btn-blue mx-4" @click="open = !open">
                    <span class="font-bold text-2xl">Suivant</span>
                </button>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>