<script lang="ts" setup>
    import { useRegisterStore } from '~/stores/register'
    import ModalComponent from "~/components/ModalComponent.vue";
    import {ErrorMessage, Field, Form} from "vee-validate";
    import {useApi} from "~/helpers/useApi";
    import * as yup from "yup";

    const { $awn } = useNuxtApp()

    const store = useRegisterStore()

    const api = useApi()
    const formDataHeaders = {
      ...api.defaults.headers,
      "Content-Type": "multipart/form-data",
    };

    const open = ref(false)

    const requiredDocumentTypes = ref([])

    const submitting = ref(false)

    const fileValidators = ref({});
    requiredDocumentTypes.value.forEach(document => {
        fileValidators[document.document_type.code] = yup
            .mixed()
            .required(`Le fichier ${document.document_type.description} est requis`);
        // Add additional validation rules if needed
    });

    const schema = yup.object({
        npi: yup
            .string()
            .length(10, "L'NPI doit être de 10 caractères")
            .required("Veuillez renseigner l'NPI de l'administrateur"),
        ...fileValidators.value // Spread the file validators into the main schema
    });

    function onSubmit(values : any) {
        const documents = requiredDocumentTypes.value.map(document => ({
            type_id: document.document_type.id,
            file: values[document.document_type.code]
        }));

        const formData = {
            person_type: "moral",
            ifu: store.ifu,
            first_member_npi: values.npi,
            documents: documents
        };


        submitting.value = true

        api({
            method : 'POST',
            url : '/register/store',
            headers: formDataHeaders,
            data: formData
        }).then((response) => {
            if (response.status === 200){
                $awn.success(response.data.message)

                navigateTo('/auth/login')
            }
        }).catch((error) => {
            $awn.alert(error.response.data.message)
        }).finally(() => {
            submitting.value = false
        })
    }

    onMounted(() => {
        api({
            method: 'GET',
            url: '/register/affiliate-documents'
        }).then(response => response.data)
           .then(response => {
               requiredDocumentTypes.value = response.required_document_types
           })
    })
</script>

<template>
    <div class="flex flex-col">
        <ModalComponent :open="open" @close="open = !open">
            <div class="px-4 md:px-32 py-16">
                <div class="text-center mb-10">
                    <span class="text-2xl text-blue-900 font-bold mt-8"> Complément d'informations </span>
                </div>

                <Form :validation-schema="schema" @submit="onSubmit">
                    <div>
                        <label class="block text-lg font-bold text-black">NPI de l'administrateur</label>
                        <Field name="npi" class="form-control" />
                        <ErrorMessage name="npi" class="form-invalid" />
                    </div>

                    <div v-for="requestDocument in requiredDocumentTypes" class="mt-4">
                        <label class="block text-lg font-bold text-black">{{ requestDocument?.document_type?.description }}</label>
                        <Field type="file" :name="requestDocument?.document_type?.code" class="form-control" />
                        <ErrorMessage :name="requestDocument?.document_type.code" class="form-invalid" />
                    </div>

                    <button class="btn-primary w-full" :disabled="submitting">
                        <template v-if="submitting">...</template>
                        <template v-else>S'inscrire</template>
                    </button>
                </Form>
            </div>
        </ModalComponent>

        <div class="w-full text-center md:w-2/3 lg:w-2/3 xl:w-full px-8 md:px-16 lg:px-32 2xl:px-64 mx-auto mt-8">
            <span class="text-4xl text-blue-900 font-bold">Confirmation des informations</span>

            <div class="card w-full flex flex-row mt-8 p-4">
                <table class="table">
                    <tr>
                        <td>IFU</td>
                        <th>{{ store.company_data.ifu }}</th>
                    </tr>
                    <tr>
                        <td>Raison Sociale</td>
                        <th>{{ store.company_data.social_reason }}</th>
                    </tr>
                    <tr>
                        <td>Adresse</td>
                        <th>{{ store.company_data.seat }}</th>
                    </tr>
                    <tr>
                        <td>Téléphone</td>
                        <th>{{ store.company_data.telephone }}</th>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <th>{{ store.company_data.email }}</th>
                    </tr>
                </table>
            </div>
        </div>

        <div class="w-full text-center md:w-2/3 lg:w-2/3 xl:w-full px-8 md:px-16 lg:px-32 2xl:px-64 mx-auto">
            <span class="text-xl">Confirmez-vous les informations ci dessus? </span>

            <button class="btn-primary w-full mt-4"  @click="open = !open">
                Je confirme
            </button>
        </div>
    </div>
</template>

<style lang="css" scoped>
</style>