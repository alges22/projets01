<script lang="ts" setup>
    import {useReimmatriculationStore} from "~/stores/reimmatriculation";
    // import PrestigeLabel from "~/components/immatriculation_prestige_steps/tabs/PrestigeLabel.vue";
    // import PrestigeNumero from "~/components/immatriculation_prestige_steps/tabs/PrestigeNumero.vue";
    // import PrestigeNumeroLabel from "~/components/immatriculation_prestige_steps/tabs/PrestigeNumeroLabel.vue";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    const store = useReimmatriculationStore()

    const simpleChecked = ref(false);
    const prestigeChecked = ref(false);


    watchEffect(() => {
        if (simpleChecked.value && prestigeChecked.value) {
            prestigeChecked.value = false;
        }
    });

    watchEffect(() => {
        if (prestigeChecked.value && simpleChecked.value) {
            simpleChecked.value = false;
        }
    });

    const tabs = [
        {
            title: 'Prestige Label',
            content: 'PrestigeLabel'
        },
        {
            title: 'Prestige Numéro',
            content: 'PrestigeNumero'
        },
        {
            title: 'Numéro + Label',
            content: 'PrestigeNumeroLabel'
        },
    ]

    const selectedTab = ref(0)
</script>

<template>
    <div class="card p-16 mt-4 text-center">
        <h4 class="text-blue font-bold text-4xl">Type d'immatriculation</h4>

        <div class="w-1/3 text-left mx-auto">
            <div class="grid grid-cols-2 gap-8 mt-12">
                <div class="flex items-center">
                    <input class="rounded-full border-2 border-gray-400 appearance-none checked:border-8 h-6 w-6"
                           type="checkbox" name="ownership" v-model="simpleChecked"/>

                    <span class="mx-4">Simple</span>
                </div>

                <div class="flex items-center">
                    <input class="rounded-full border-2 border-gray-400 appearance-none checked:border-8 h-6 w-6"
                           type="checkbox" name="ownership" v-model="prestigeChecked"/>

                    <span class="mx-4">Prestige</span>
                </div>
            </div>
        </div>

        <div v-if="prestigeChecked">
            <div class="w-1/2 mx-auto">
                <div class="tab-container cursor-pointer">
                    <div v-for="(tab, index) in tabs" @click="selectedTab = index" :class="{ 'tab': true, 'active text-blue-500': index === selectedTab }">
                        {{ tab.title }}
                    </div>
                </div>

                <div class="w-full p-4">
                    <component :is="tabs[selectedTab].content" />
                </div>
            </div>
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

                <button class="btn-blue mx-4" @click="store.nextStep()">
                    <span class="font-bold text-2xl">Suivant</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
    /* Style for table rows */
    tr {
        background-color: #f2f2f2; /* Alternate row background color */
        border-bottom: 1px solid #ddd; /* Border between rows */
    }

    /* Style for table cells */
    td {
        padding: 10px; /* Add padding to cells for spacing */
    }

    .row.active {
        @apply bg-gray-100 rounded-xl shadow-md
    }

     .tab-container {
         @apply justify-center flex flex-row w-full mt-8 border-gray-400 border-b
     }

    .tab {
        @apply py-2 px-4 text-lg
    }

    .tab.active {
        @apply border-b-4 border-blue-500
    }
</style>