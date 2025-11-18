<script setup>
    import {useRoute, useRouter} from "vue-router";
    import StatusComponent from "@/components/StatusComponent.vue";
    import client from "@/assets/js/axios/client.js";
    import {onMounted, ref, onBeforeMount} from "vue";
    import FileInputGroup from "@/components/Form/FileInputGroup.vue";
    import BasicButton from "@/components/BasicButton.vue";
    import Swal from "sweetalert2";
    import Alert from "@/components/notification/alert.js";
    import dayjs from "dayjs";
    import OtpModalForm from "@/views/OtpModalForm.vue";
    import {useOtpStore} from "@/stores/otp.js";
    import {storeToRefs} from "pinia";

    const router = useRouter()

    const route = useRoute();
    const id = route.params.id;

    const pledge = ref(null);
    const loading = ref(true);
    const files = ref([])

    const otpModalOpen = ref(false);

    const otpStore = useOtpStore();
    const { loading: loadingOtp } = storeToRefs(otpStore);

    onBeforeMount(() => {
        loadingOtp.value = false
    })

    const liftPledge = (authorization_id) => {
        Swal.fire({
            title: "Validation en cours..",
            text: "Patientez pendant le traitement de votre requête",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        client({
            method: "POST",
            url: `/pledge/lift/${id}`,
            data: {
                authorization_id: authorization_id,
                pledge_file: files.value,
                _method: 'PUT',
            },
            headers: {
                ...client.defaults.headers,
                "Content-Type": "multipart/form-data",
            },
        })
            .then((response) => response.data)
            .then((response) => {
                Alert.success("Demande de levée de gage émise avec succès");

                router.push('/court-pledges')
            })
            .finally(() => {
                Swal.close();
            });
    };

    const loadPledge = () => {
        loading.value = true;

        client({
            method: "GET",
            url: `/pledge/${id}`,
        })
            .then((response) => response.data)
            .then((response) => {
                pledge.value = response;
                loading.value = false;
            });
    };

    onMounted(() => {
        loadPledge();
    });
</script>

<template>
    <div v-if="!loading">
        <div class="w-full mx-auto py-8">
            <h4 class="font-weight-bold text-2xl text-blue-900">Récapitulatif de la demande de mise en gage</h4>
            <div class="bg-white mt-4 rounded-md">
                <div class="bg-blue-100 p-4">
                    <span class="font-bold text-lg text-blue-900 rounded-md">Référence</span>
                </div>
                <table class="table">
                    <tr>
                        <th>Référence</th>
                        <th>{{ pledge?.reference }}}</th>
                    </tr>
                    <tr>
                        <th>Statut</th>
                        <th>
                            <StatusComponent :status="pledge.status" :status-text="pledge.status_label"/>
                        </th>
                    </tr>
                    <tr>
                        <th>Date de soumission</th>
                        <th>{{ dayjs(pledge?.created_at).format("DD/MM/YYYY H:m") }}</th>
                    </tr>
                    <tr>
                        <th>Banque</th>
                        <th>{{ pledge?.financial_institution?.name ?? pledge?.institution_emitted.name }}</th>
                    </tr>
                    <tr v-if="pledge?.financial_institution">
                        <th>Concessionnaire</th>
                        <th>{{ pledge?.institution_emitted.name }}</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6 ">
        <form class="intro-y col-span-12" @submit.prevent="otpModalOpen = !otpModalOpen">
            <div class="intro-y box p-5">
                <div>
                    <div class="sm:grid grid-cols-1 gap-8 mb-8">
                        <div>
                            <FileInputGroup
                                v-model="files"
                                :disabled="loading"
                                :multiple="true"
                                add-class="w-full"
                                auto-complete="files"
                                label="Fichiers de levée de gage"
                                name="files"
                                required
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right mt-5">
                <button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
                    Annuler
                </button>
                <BasicButton :loading="loadingOtp" class="btn-primary w-36" type="submit"> Confirmer</BasicButton>
            </div>
        </form>
    </div>

    <OtpModalForm
        :is-company="false"
        :npi="pledge?.vehicle_owner.identity.npi"
        :open="otpModalOpen"
        @close="otpModalOpen = false"
        @submit="(authorization_id) => liftPledge(authorization_id)"
    />
</template>

<style lang="scss" scoped></style>
