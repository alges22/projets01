<script setup>
    import {useSellStore} from "~/stores/myCarSell"
    import PersonnePhysiqueBeninois from "./form/PersonnePhysiqueBeninois.vue"
    import PersonneMorale from "./form/PersonneMorale.vue"
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    const store = useSellStore()

    let {choixPersonne, nationalite} = storeToRefs(store)
</script>

<template>
    <div class="card p-10 mt-4">
        <div class="w-9/12 mx-auto">
            <div class="text-center">
                <p class="font-bold text-blue-500 rounded-md text-3xl mt-4 mb-4">Chosissez le type d'acheteur</p>
                <table class="table">
                    <tr class="flex justify-center">
                        <td class="text-center me-5">
                            <button
                                :class="{ 'border-b-4 border-blue-500 font-semibold text-blue-500': choixPersonne === 'personne physique' }"
                                class="btn rounded text-lg py-3"
                                @click="choixPersonne = 'personne physique'">
                                Personne physique
                            </button>
                        </td>

                        <td class="text-center ms-5">
                            <button
                                :class="{ 'border-b-4 border-blue-500 font-semibold text-blue-500': choixPersonne === 'personne morale' }"
                                class="btn rounded text-lg py-3"
                                @click="choixPersonne = 'personne morale'">
                                Personne morale
                            </button>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="py-10 px-14 mx-auto w-10/12 rounded-lg bg-[#EFF3F9]">
<!--                <div class="w-full flex flex-row justify-center mb-8">-->
<!--                    <div :class="{ isSelected: nationalite === 'beninois' }"-->
<!--                         class="mt-3  p-5 inline-block bg-white rounded-lg me-5">-->
<!--                        <input id="depot_de_titre" v-model="nationalite" class="me-3" name="depot_ou_reprise"-->
<!--                               placeholder="Numéro du certificat" type="radio" value="beninois">-->
<!--                        <label :class="{ 'font-semibold text-blue-500': nationalite === 'beninois' }"-->
<!--                               class="text-lg"-->
<!--                               for="depot_de_titre">Béninois</label>-->
<!--                    </div>-->
<!--                    <div :class="{ isSelected: nationalite === 'non beninois' }"-->
<!--                         class="mt-3 inline-block p-5 float-right bg-white rounded-lg ms-5">-->
<!--                        <input id="reprise_de_titre" v-model="nationalite" class="me-3" name="depot_ou_reprise"-->
<!--                               placeholder="Numéro du certificat" type="radio" value="non beninois">-->
<!--                        <label :class="{ 'font-semibold text-blue-500': nationalite === 'non beninois' }"-->
<!--                               class="text-lg"-->
<!--                               for="reprise_de_titre">Non Béninois</label>-->
<!--                    </div>-->
<!--                </div>-->

                <PersonnePhysiqueBeninois v-if="choixPersonne === 'personne physique' && nationalite === 'beninois'" />
                <PersonneMorale v-if="choixPersonne === 'personne morale' && nationalite === 'beninois'" />
            </div>

            <div class="mt-16 flex-row flex">
                <button class="text-blue font-bold text-2xl" @click="store.previousStep()">
                    <font-awesome-icon class="mx-4" :icon="['fas', 'arrow-left']" />
                    Précédent
                </button>
            </div>
        </div>
    </div>
</template>

<style lang="css" scoped>
    .isSelected {
        @apply border border-blue-300
    }
</style>