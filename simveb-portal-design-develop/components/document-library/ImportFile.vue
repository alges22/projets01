<script setup>
    import ModalComponent from "~/components/ModalComponent.vue";
    import {ErrorMessage, Field, Form} from "vee-validate";
    import * as yup from "yup";
    import {useApi} from "~/helpers/useApi";

    const api = useApi()
    const {$awn} = useNuxtApp()

    const emit = defineEmits(["saved"]);

    const props = defineProps({
        open: Boolean,
        file : Object
    });

    const submitting = ref(false)

    const formDataHeaders = {
        ...api.defaults.headers,
        "Content-Type": "multipart/form-data",
    };

    const create = ref({
        document_types: []
    })

    const defaultValues = {
        type_id: props.file?.file_type_id
    }

    const schema = yup.object({
        type_id: yup
            .string()
            .required("Veuillez renseigner le type de fichier"),

        file: yup
            .mixed()
            .required("Veuillez sélectionner un fichier")
            .test("fileSize", "Le fichier est trop volumineux", (value) => {
                return value && value.size <= 5000000; // 5MB limit
            })
            .test("fileType", "Le format du fichier n'est pas valide", (value) => {
                return value && ["application/pdf", "image/jpeg", "image/png"].includes(value.type);
            })
    });

    onMounted(() => {
        api({
            method: 'GET',
            url: '/simveb-file/create'
        }).then((response) => response.data)
            .then((response) => {
                create.value = response
            })
    })

    function onSubmit(values) {
        submitting.value = true

        api({
            method: 'POST',
            url: '/simveb-file/',
            headers: formDataHeaders,
            data: {
                documents : [values]
            }
        }).then((response) => response.data)
            .then((response) => {
                create.value = response

                $awn.success('Fichier enregistré avec succès')

                emit('saved')

            })
            .finally(() => {
                submitting.value = false
            })
    }
</script>


<template>
    <ModalComponent :open="open" @close="$emit('close')">
        <div class="px-16 py-16">
            <div class="text-center mb-10">
                <span class="text-2xl text-blue-900 font-bold mt-8"> Téléverser un document </span>
            </div>

            <Form :validation-schema="schema" :initial-values="defaultValues" @submit="onSubmit">
                <div class="grid grid-cols-1 gap-2">
                    <div>
                        <label class="block text-lg font-bold text-black">Type de fichier</label>
                        <Field as="select" class="form-control" name="type_id">
                            <option value="">Selectionnez le département</option>
                            <option v-for="type in create.document_types" :value="type.id"> {{ type.description }}</option>
                        </Field>
                        <ErrorMessage class="form-invalid" name="type_id"/>
                    </div>

                    <div class="mt-4">
                        <label class="block text-lg font-bold text-black">Joindre le fichier</label>
                        <Field name="file" v-slot="{ handleChange, handleBlur }">
                            <input class="form-control" type="file" @change="handleChange" @blur="handleBlur" />
                        </Field>
                        <ErrorMessage class="form-invalid" name="file"/>
                    </div>
                </div>

                <button :disabled="submitting" class="btn-primary w-full mt-8">
                    <template v-if="submitting">...</template>
                    <template v-else>Ajouter le fichier</template>
                </button>
            </Form>
        </div>
    </ModalComponent>
</template>