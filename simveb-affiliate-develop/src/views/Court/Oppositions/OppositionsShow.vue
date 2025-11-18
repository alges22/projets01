<script setup>
    import client from "@/assets/js/axios/client.js";
    import {useRoute, useRouter} from "vue-router";
    import {ref, onMounted} from "vue";
    import StatusComponent from "@/components/StatusComponent.vue";
    import dayjs from "dayjs";
    import BasicButton from "@/components/BasicButton.vue";
    import Swal from "sweetalert2";
    import Alert from "@/components/notification/alert.js";
    import {userHasPermissions} from "@/helpers/permissions.js";
    import statuses from "@/data/statuses.js";
    import {useAuthStore} from "@/stores/auth.js";
    import {storeToRefs} from "pinia";
    import OppositionUpdateModal from "@/views/Court/Oppositions/OppositionUpdateModal.vue";
    import {useOppositionStore} from "@/stores/opposition.js";

    const oppositionStore = useOppositionStore()
    const authStore = useAuthStore();
    const { online_profile } = storeToRefs(authStore);

    const router = useRouter()

    const { can } = userHasPermissions();

    const route = useRoute()
    const id = route.params.id

    const opposition = ref(null)
    const loading = ref(true)

    const updateModal = ref(false)

    const loadOpposition = () => {
        loading.value = true

        client({
            method: 'GET',
            url: `/oppositions/${id}`
        }).then((response) => response.data)
            .then((response) => {
                opposition.value = response
                loading.value = false
            })
    }

    const update = () => {
        oppositionStore.updateOpposition(id)
            .then((response) => response.data)
            .then((response) => {
                Alert.success("Opposition modifiée avec succès")

                updateModal.value = false

                router.push('/oppositions')
            })
    }

    const reject = (message) => {
        Swal.fire({
            title: "Rejeter la demande",
            input: "text",
            text: message,
            inputLabel: "Raison du rejet",
            inputPlaceholder: "Entrez la raison ici...",
            showCancelButton: true,
            confirmButtonText: "Soumettre",
            cancelButtonText: "Anuuler",
            inputValidator: (value) => {
                if (!value) {
                    return "Veuillez saisir la raison du rejet";
                }
            },
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Rejet en cours..",
                    text: "Patientez pendant le traitement de votre requête",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                client({
                    method: "PUT",
                    url: `/opposition/reject/${id}`,
                    data: {
                        rejected_reason: result.value,
                    },
                })
                    .then((response) => {
                        Alert.success("Rejet effectué avec succès");

                        router.push('/oppositions')
                    })
                    .finally(() => {
                        Swal.close();
                    });
            }
        });
    };

    const validate = (message) => {
        Swal.fire({
            title: "Valider la demande",
            text: message,
            showCancelButton: true,
            confirmButtonText: "Oui, Valider!",
            cancelButtonText: "Non , j'annule!",
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Validation en cours..",
                    text: "Patientez pendant le traitement de votre requête",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                client({
                    method: "PUT",
                    url: `/opposition/validate/${id}`,
                })
                    .then((response) => {
                        Alert.success("Validation effectuée avec succès");

                        router.push('/oppositions')
                    })
                    .finally(() => {
                        Swal.close();
                    });
            }
        })

    };

    const lift = () => {
        Swal.fire({
            title: "Lever une opposition",
            text: "Etes-vous sûr de vouloir êmettre cette demande de lévée d'opposition?",
            html: `
                <input multiple type="file" id="fileInput" class="swal2-file" />
                <p style="margin-top: 10px;">Veuillez joindre un fichier</p>
            `,
            showCancelButton: true,
            confirmButtonText: "Oui, Valider!",
            cancelButtonText: "Non , j'annule!",
            preConfirm: () => {
                const fileInput = document.getElementById('fileInput');
                if (fileInput.files.length === 0) {
                    Swal.showValidationMessage('Veuillez sélectionner un fichier');
                }
                return fileInput.files;
            }
        }).then((result) => {
            if (result.isConfirmed) {
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
                    url: `/opposition/lift/${id}`,
                    data: {
                        opposition_file : result.value,
                        _method: 'PUT'
                    },
                    headers: {
                        ...client.defaults.headers,
                        "Content-Type": "multipart/form-data",
                    },
                })
                    .then((response) => {
                        Alert.success("Levée de l'opposition émise avec succès");

                        loadOpposition();
                    })
                    .finally(() => {
                        Swal.close();
                    });
            }
        })

    };

    onMounted(() => {
        loadOpposition()
    })
</script>

<template>

    <OppositionUpdateModal
        :updateModal="updateModal"
        @close="updateModal = false"
        @update="update"
    />


    <div v-if="!loading">
        <div class="bg-white mt-2 p-2 xl:p-4 rounded-md">
            <div class="bg-blue-100 p-4">
                <span class="font-bold text-lg text-blue-500 rounded-md">Informations sur la demande de mise en opposition</span>
            </div>
            <table class="table mt-5">
                <tr>
                    <th>Référence</th>
                    <th> {{ opposition?.reference }}}</th>
                </tr>

                <tr>
                    <th>Propriétaire</th>
                    <th> {{ opposition?.owner.identity ? opposition.owner.identity.fullName : opposition?.owner.institution?.name }}</th>
                </tr>

<!--                <tr>-->
<!--                    <td>Statut</td>-->
<!--                    <th>-->
<!--                        <StatusComponent :status="opposition.status" :status-text="opposition.status_label"/>-->
<!--                    </th>-->
<!--                </tr>-->

                <tr>
                    <td>Raison de l'opposition</td>
                    <th>{{ opposition.title_reason.label }}</th>
                </tr>

                <tr>
                    <td>Date de soumission</td>
                    <th>{{ dayjs(opposition.created_at).format("DD/MM/YYYY H:m") }}</th>
                </tr>

                <tr>
                    <td>Soumis par : </td>
                    <th>{{ opposition?.author?.identity.fullName }}</th>
                </tr>
            </table>
        </div>

        <div class="bg-white mt-2 p-2 xl:p-4 rounded-md">
            <div class="bg-blue-100 p-4">
                <span class="font-bold text-lg text-blue-500 rounded-md">Véhicules mis en opposition</span>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <td>Véhicule</td>
                        <td>VIN</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="vehicle in opposition.vehicles">
                        <td><img style="width: 100px; height: 50px" :src="vehicle.vehicle_image" :alt="vehicle.vin"></td>
                        <td>{{ vehicle.vin }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-white mt-2 p-2 xl:p-4 rounded-md">
            <div class="bg-blue-100 p-4">
                <span class="font-bold text-lg text-blue-500 rounded-md">Statut de l'opposition</span>
            </div>

            <table class="table mt-5">
                <tr>
                    <td>Statut</td>
                    <th>
                        <StatusComponent :status="opposition.active_treatment.status" :status-text="opposition.active_treatment.status_label"/>
                    </th>
                </tr>

                <tr v-if="opposition.active_treatment.affected_to_clerk">
                    <td> Greffier </td>
                    <th> {{ opposition.active_treatment.affected_to_clerk.identity.fullName }}</th>
                </tr>

                <tr v-if="opposition.active_treatment.rejected_reason">
                    <td> Raison du rejet </td>
                    <th> {{ opposition.active_treatment.rejected_reason	 }}</th>
                </tr>

                <tr v-if="opposition.active_treatment.affected_to_judge">
                    <td> Juge d'instruction </td>
                    <th> {{ opposition.active_treatment.affected_to_judge.identity.fullName }}</th>
                </tr>

                <tr v-if="opposition.active_treatment.issued_at">
                    <td> Emis le </td>
                    <th> {{ dayjs(opposition.active_treatment.issued_at).format("DD/MM/YYYY H:m") }} </th>
                </tr>

                <tr v-if="opposition.active_treatment.lifting_at">
                    <td> Levé le </td>
                    <th> {{ dayjs(opposition.active_treatment.lifting_at).format("DD/MM/YYYY H:m") }} </th>
                </tr>
            </table>

            <div class="mt-4">
                <BasicButton
                    v-if="can('validate-opposition-by-clerk') && opposition.active_treatment.status === statuses.OPPOSITION_EMITTED"
                    @click="validate('Etes-vous sûr de vouloir valider la demande de mise en opposition?')"
                    class="bg-green-500 text-white mx-1"
                >
                    Valider la demande d'opposition
                </BasicButton>
                <BasicButton
                    class="btn-danger mx-1"
                    @click="reject('Etes-vous sûr de vouloir rejeter la demande de mise en opposition?')"
                    v-if="can('reject-opposition-by-clerk') && opposition.active_treatment.status === statuses.OPPOSITION_EMITTED"
                >
                    Rejeter
                </BasicButton>
                <BasicButton
                    class="bg-green-500 text-white mx-1"
                    @click="lift"
                    v-if="can('lift-opposition')  && opposition.active_treatment.status === statuses.CLERK_VALIDATED"
                >
                    Lever l'opposition
                </BasicButton>
                <BasicButton
                    class="bg-green-500 text-white mx-1"
                    @click="validate('Etes-vous sûr de vouloir valider la lévée le l\'opposition?')"
                    v-if="can('validate-opposition-by-judge') && opposition.active_treatment.status === statuses.OPPOSITION_LIFTED_EMITED"
                >
                    Valider la levée de l'opposition
                </BasicButton>
                <BasicButton
                    class="btn-danger mx-1"
                    @click="reject('Etes-vous sûr de vouloir rejeter la demande de levée de l\'opposition?')"
                    v-if="can('reject-opposition-by-judge') && opposition.active_treatment.status === statuses.OPPOSITION_LIFTED_EMITED"
                >
                    Rejeter la levée de l'opposition
                </BasicButton>

                <BasicButton
                    class="bg-yellow-500 mx-1"
                    @click="updateModal = true"
                    v-if="
                        online_profile.id === opposition.author_id && can('update-opposition')
                        && (opposition.active_treatment.status ===  statuses.CLERK_REJECTED || opposition.active_treatment.status ===  statuses.JUDGE_REJECTED)
                    ">
                    <i class="fa fa-edit mr-2"></i>
                    Modifier l'opposition
                </BasicButton>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">

</style>