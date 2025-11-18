<script setup>
    import client from "@/assets/js/axios/client.js";
    import {useRoute, useRouter} from "vue-router";
    import { onMounted, ref } from "vue";
    import StatusComponent from "@/components/StatusComponent.vue";
    import dayjs from "dayjs";
    import BasicButton from "@/components/BasicButton.vue";
    import Swal from "sweetalert2";
    import Alert from "@/components/notification/alert.js";
    import { userHasPermissions } from "@/helpers/permissions.js";
    import statuses from "@/data/statuses.js";
    import {usePledgeStore} from "@/stores/pledge.js";
    import PledgeUpdateModal from "@/views/Court/Pledge/PledgeUpdateModal.vue";

    const route = useRoute();
    const id = route.params.id;

    const updateModal = ref(false)

    const pledgeStore = usePledgeStore()

    const router = useRouter()

    const pledgeLift = ref(null);
    const loading = ref(true);

    const { can } = userHasPermissions();

    const update = () => {
        pledgeStore.updatePledgeLift(id)
            .then((response) => response.data)
            .then((response) => {
                Alert.success("Gage modifié avec succès")

                updateModal.value  = false

                router.push('/pledge-lifts')
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
                    url: `/pledge-lift/reject/${id}`,
                    data: {
                        rejected_reasons: result.value,
                    },
                })
                    .then((response) => {
                        Alert.success("Rejet effectué avec succès");

                        router.push('/pledge-lifts')
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
                    url: `/pledge-lift/validate/${id}`,
                })
                    .then((response) => {
                        Alert.success("Validation effectuée avec succès");

                        router.push('/pledge-lifts')
                    })
                    .finally(() => {
                        Swal.close();
                    });
            }
        })

    };

    const loadPledgeLift = () => {
        loading.value = true;

        client({
            method: "GET",
            url: `/pledge-lift/${id}`,
        })
            .then((response) => response.data)
            .then((response) => {
                pledgeLift.value = response;
                loading.value = false;
            });
    };

    onMounted(() => {
        loadPledgeLift();
    });
</script>

<template>
    <PledgeUpdateModal
        v-if="can('update-pledge-lift')"
        :updateModal="updateModal"
        @close="updateModal = false"
        @update="update"
    />

    <div v-if="!loading">
        <div v-if="!loading" class="bg-white">
            <div class="sm:w-full md:w-2/3 w-1/2 mx-auto px-16 py-8">
                <h4 class="font-weight-bold text-2xl text-blue-900">Récapitulatif de la demande de mise en gage</h4>
                <div class="bg-white mt-4 rounded-md shadow-xl">
                    <div class="bg-blue-100 p-4">
                        <span class="font-bold text-lg text-blue-900 rounded-md">Informations sur la demande de levée gage</span>
                    </div>
                    <table class="table">
                        <tr>
                            <th>Référence de la demande de gage</th>
                            <th>{{ pledgeLift.pledge?.reference }}}</th>
                        </tr>
                        <tr>
                            <th>Date de soumission</th>
                            <th>{{ dayjs(pledgeLift?.created_at).format("DD/MM/YYYY H:m") }}</th>
                        </tr>
                        <tr>
                            <th>Banque</th>
                            <th>{{ pledgeLift?.financial_institution?.name ?? pledge?.institution_emitted.name }}</th>
                        </tr>
                        <tr v-if="pledge?.financial_institution">
                            <th>Concessionnaire</th>
                            <th>{{ pledgeLift?.institution_emitted.name }}</th>
                        </tr>
                    </table>
                </div>

                <div class="bg-white mt-8 rounded-md shadow-xl">
                    <div class="bg-blue-100 p-4">
                        <span class="font-bold text-lg text-blue-900 rounded-md">Statut de la demande de evée gage</span>
                    </div>

                    <table class="table">

                        <tr>
                            <th>Statut</th>
                            <th><StatusComponent :status="pledgeLift.status" :status-text="pledgeLift.status_label" /></th>
                        </tr>
                        <tr v-if="pledgeLift.active_treatment.rejected_reasons">
                            <td> Raison du rejet </td>
                            <th> {{ pledgeLift.active_treatment.rejected_reasons }}</th>
                        </tr>
                        <tr v-if="pledgeLift.active_treatment.affected_to_clerk">
                            <td> Greffier </td>
                            <th> {{ pledgeLift.active_treatment.affected_to_clerk.identity.fullName }}</th>
                        </tr>
                    </table>
                </div>

                <div class="mt-4">
                    <BasicButton
                        v-if="can('validate-pledge-lift-by-justice')
                            && (pledgeLift.active_treatment.status === statuses.EMITTED || pledgeLift.active_treatment.status === statuses.REMITTED)"
                        @click="validate('Etes-vous sûr de vouloir valider cette demande de levée de gage?')"
                        class="bg-green-500 text-white mx-1"
                    >
                        Valider
                    </BasicButton>
                    <BasicButton
                        v-if="can('reject-pledge-lift-by-justice')
                            && (pledgeLift.active_treatment.status === statuses.EMITTED || pledgeLift.active_treatment.status === statuses.REMITTED)"
                        class="btn-danger mx-1"
                        @click="reject('Etes-vous sûr de vouloir rejeter cette demande de levée de gage?')"
                    >
                        Rejeter
                    </BasicButton>

                    <BasicButton
                        class="bg-yellow-500 mx-1"
                        @click="updateModal = true"
                        v-if="can('update-pledge-lift') && pledgeLift.can_update">
                        <i class="fa fa-edit mr-2"></i>
                        Modifier la demande de levée de gage
                    </BasicButton>
                </div>

                <div class="bg-white mt-8 rounded-md shadow-xl">
                    <div class="bg-blue-100 p-4">
                        <span class="font-bold text-lg text-blue-900 rounded-md">Informations du propriétaire</span>
                    </div>
                    <table class="table">
                        <template v-if="pledgeLift.pledge?.vehicle_owner.identity">

                            <tr>
                                <th>Nom</th>
                                <th>{{ pledgeLift.pledge?.vehicle_owner.identity.firstname}}</th>
                            </tr>

                            <tr>
                                <th>Prénoms</th>
                                <th>{{ pledgeLift.pledge?.vehicle_owner.identity.lastname }}</th>
                            </tr>

                            <tr>
                                <th>NPI</th>
                                <th>{{ pledgeLift.pledge?.vehicle_owner.identity.npi }}</th>
                            </tr>

                            <tr>
                                <th>Téléphone</th>
                                <th>{{ pledgeLift.pledge?.vehicle_owner.identity.telephone }}</th>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <th>Entreprise</th>
                                <th>{{ pledgeLift.pledge.vehicle_owner.institution?.name }}</th>
                            </tr>
                        </template>
                    </table>
                </div>


                <div class="bg-white mt-8 rounded-md shadow-xl">
                    <div class="bg-blue-100 p-4">
                        <span class="font-bold text-lg text-blue-900 rounded-md">Informations du véhicule</span>
                    </div>
                    <table class="table">
                        <tr>
                            <th>Pays d'origine du véhicule</th>
                            <th></th>
                        </tr>

                        <tr>
                            <th>Numéro du chassis</th>
                            <th>{{ pledgeLift.pledge?.vehicle.vin }}</th>
                        </tr>

                        <tr>
                            <th>Marque</th>
                            <th></th>
                        </tr>

                        <tr>
                            <th>Modèle du véhicule</th>
                            <th>{{ pledgeLift.pledge?.vehicle.vehicle_model }}</th>
                        </tr>

                        <tr>
                            <th>Energie</th>
                            <th></th>
                        </tr>

                        <tr>
                            <th>Nombre de places assises</th>
                            <th>{{ pledgeLift.pledge?.vehicle.number_of_seats }}</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped></style>
