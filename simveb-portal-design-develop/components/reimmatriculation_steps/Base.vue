<script lang="ts" setup>
    import {useReimmatriculationStore} from "~/stores/reimmatriculation";
    import {ErrorMessage, Field, Form} from "vee-validate";
    import {useApi} from "~/helpers/useApi";
    import * as yup from "yup";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    const store = useReimmatriculationStore()

    let { vehicule_infos, owner, reimmatriculation_reason, base_infos } = storeToRefs(store)

    const userStore = useUserStore()

    let { user } = storeToRefs(userStore)

    const loading = ref(false)

    const modal = ref(false)

    const { $awn } = useNuxtApp()

    const default_values = ref({
        vin: '',
        // vin: '36987451',
    })

    const api = useApi()

    const schema = yup.object({
        vin: yup
            .string()
            .required("Veuillez renseinger le VIN du véhicule")
    });

    const onSubmit = (values:any) => {
        loading.value = true

        api({
            method: 'GET',
            url: `/client/demands/verify-vehicle-situation/${values.vin}`
        })
            .then((response) => response.data)
            .then((response) => {
                modal.value = true

                reimmatriculation_reason.value = response.reimmatriculation_reason

                vehicule_infos.value = response.vehicle

                base_infos.value.vin = values.vin
            })
            .catch((error) => {
                $awn.alert(error.response.data.message)
            })
            .finally(() => {
                loading.value = false
            })


        if (!owner.value){
            store.getOwner(user.value.identity.npi)
        }
    }
</script>

<template>
    <ModalComponent :open="modal" @close="modal = false" v-if="reimmatriculation_reason">
        <div class="card  p-4 md:py-16 md:px-8 lg:px-16 xl:px-32 mt-4 text-center">
            <h4 class="text-blue font-bold text-4xl mb-4">
                {{ reimmatriculation_reason.title }}
            </h4>

            <img :src="reimmatriculation_reason.img_url" alt="Véhicule reformé" class="mx-auto" style="width: 700px">

            <button class="btn-blue mx-4 w-full" @click="store.nextStep()">
                <span class="font-bold text-2xl">Suivant</span>
            </button>
        </div>
    </ModalComponent>

    <div class="card p-4 md:p-16 mt-4 text-center">
        <font-awesome-icon v-if="loading" :icon="['far', 'spinner-third']" spin size="2xl" style="color: #146FEB; height: 150px" class="mt-8" />

        <div v-else>
            <h4 class="text-blue font-bold text-4xl">Base</h4>

            <Form :validation-schema="schema" @submit="onSubmit" :initial-values="default_values" class="w-full md:w-2/3 lg:w-2/3 xl:w-1/3 text-left mx-auto">
                <div class="mt-8">
                    <label for="immatriculation_vin">VIN (Numéro de châssis)</label>
                    <Field name="vin" id="immatriculation_vin" type="text" class="form-control" placeholder="VIN (Numéro de châssis)"/>
                    <ErrorMessage name="vin" class="form-invalid" />
                </div>

                <button class="btn btn-blue w-full mt-8" type="submit">
                    Vérifications
                </button>
            </Form>
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
</style>