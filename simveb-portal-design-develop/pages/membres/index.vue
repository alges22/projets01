<script setup>
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import * as yup from "yup";
    import {ErrorMessage, Field, Form} from "vee-validate";
    import {useApi} from "~/helpers/useApi";
    import dayjs from "dayjs";
    import Swal from "sweetalert2";

    const { $awn } = useNuxtApp()

    const api = useApi()

    const open = ref(false)
    const submitting = ref(false)

    const loading = ref(true)

    const members = ref([])

    const schema = yup.object({
        npi: yup
            .string()
            .length(10, "Le NPI doit être de 10 caractères")
            .required("Veuillez renseigner le NPI")
    });

    function onSubmit(values) {
        submitting.value = true

        api({
            method: 'POST',
            url: '/invitations',
            data: values
        }).then(response => {
                $awn.success('Invitation envoyée avec succès')

                fetchMembers()

                open.value = !open.value
            })
            .catch((error) => {
                $awn.alert(error.response.data.message)
            })
            .finally(() => {
                submitting.value = false
            })
    }

    onMounted(() => {
        fetchMembers()
    })

    const fetchMembers = () => {
        loading.value = true

        api({
            method: 'GET',
            url: '/profile-types/members'
        }).then(response => response.data)
            .then(response => {
                members.value = response.data

                loading.value = false
            })
    }

    const denyMember = (id) => {
        Swal.fire({
            title: "Voulez-vous effectuer cette action?",
            showDenyButton: true,
            confirmButtonColor: "#172554",
            denyButtonColor: "#EF4444",
            confirmButtonText: "Oui! Je confirme",
            denyButtonText: `Non! J'annule`
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading()

                api({
                    method: 'PUT',
                    url: '/profile-types/members/toggle-status',
                    data: {
                        profile_id: id,
                    },
                }).then(response => response.data)
                    .then(response => {
                        $awn.success(response.message)

                        fetchMembers()
                    })
                    .catch((error) => {
                        $awn.alert(error.response.data.message)
                    })
            } else if (result.isDenied) {
                Swal.close()
            }
        });
    }
</script>

<template>
    <ModalComponent :open="open" @close="open = !open">
        <div class="card mx-auto p-8">
            <Form :validation-schema="schema" @submit="onSubmit">
                <div>
                    <label for="npi">NPI</label>
                    <Field class="form-control" placeholder="Entrez l'NPI" name="npi" />
                    <ErrorMessage name="npi" class="form-invalid" />
                </div>

                <button class="btn-primary w-full" :disabled="submitting">
                    <template v-if="submitting">...</template>
                    <template v-else>Ajouter</template>
                </button>
            </Form>
        </div>
    </ModalComponent>

    <div class="card p-16 mt-8">
        <div class="flex-row flex">
            <h4 class="font-bold text-blue-900 text-2xl">Membres</h4>

            <div class="ms-auto">
                <button class="btn btn-primary" @click="open = !open">Ajouter un membre</button>
            </div>
        </div>

        <div v-if="loading" class="flex justify-center w-full mt-8">
            <Loader />
        </div>
        <template v-else>
            <table class="classic-table mt-8">
                <thead>
                <tr>
                    <th>NPI</th>
                    <th>Nom et Prénoms</th>
                    <th>Roles</th>
                    <th>Date d'ajout</th>
                    <th>Suspendu</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(member, index) in members">
                    <td> {{ member.identity.npi }} </td>
                    <td> {{ member.identity.fullName }} </td>
                    <td>
                        <div class="font-bold text-gray-600">
                            <template v-for="(role, index) in member.roles">
                                {{ role.label }}{{ index < member.roles.length - 1 ? ", " : "" }}
                            </template>
                        </div>
                    </td>
                    <td> {{ member.created_at }} </td>
                    <td> {{ member.suspended  ? "Oui" : "Non"}} </td>
                    <td class="text-center">
                        <button @click="denyMember(member.id)">
                            <font-awesome-icon :icon="['fas', 'times']" class="icon" />
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </template>
    </div>
</template>

<style scoped>
    .table{
        @apply flex-row flex text-gray-400
    }

    .table > span{
        @apply py-4 px-8
    }

    .classic-table td,
    .classic-table th {
        @apply pt-2 pb-1 border-gray-200 border-b-2
    }

    .classic-table{
        @apply text-gray-500 text-left w-full
    }


    .icon{
        @apply p-2 bg-red-500 px-4 text-white
    }
</style>