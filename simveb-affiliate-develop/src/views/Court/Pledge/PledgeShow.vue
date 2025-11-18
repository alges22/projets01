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
    import PledgeUpdateModal from "@/views/Court/Pledge/PledgeUpdateModal.vue";
    import {usePledgeStore} from "@/stores/pledge.js";

    const updateModal = ref(false)

    const pledgeStore = usePledgeStore()

    const router = useRouter()

	const route = useRoute();
	const id = route.params.id;

	const pledge = ref(null);
	const loading = ref(true);

	const { can } = userHasPermissions();

    const update = () => {
        pledgeStore.updatePledge(id)
            .then((response) => response.data)
            .then((response) => {
                Alert.success("Gage modifié avec succès")

                updateModal.value  = false
                router.push('/court-pledges')
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
                    url: `/pledge/reject/${id}`,
                    data: {
                        rejected_reasons: result.value,
                    },
                })
                    .then((response) => {
                        Alert.success("Rejet effectué avec succès");

                        router.push('/court-pledges')
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
                    url: `/pledge/validate/${id}`,
                })
                    .then((response) => {
                        Alert.success("Validation effectuée avec succès");

                        router.push('/court-pledges')
                    })
                    .finally(() => {
                        Swal.close();
                    });
            }
        })

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
    <PledgeUpdateModal
        v-if="can('update-pledge')"
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
                        <span class="font-bold text-lg text-blue-900 rounded-md">Informations sur la demande gage</span>
                    </div>
                    <table class="table">
                        <tr>
                            <th>Référence</th>
                            <th>{{ pledge?.reference }}}</th>
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

                <div class="bg-white mt-8 rounded-md shadow-xl">
                    <div class="bg-blue-100 p-4">
                        <span class="font-bold text-lg text-blue-900 rounded-md">Statut de la demande de gage</span>
                    </div>

                    <table class="table">
                        <tr>
                            <th>Statut</th>
                            <th><StatusComponent :status="pledge.status" :status-text="pledge.status_label" /></th>
                        </tr>
                        <tr v-if="pledge.active_treatment.rejected_reasons">
                            <td> Raison du rejet </td>
                            <th> {{ pledge.active_treatment.rejected_reasons }}</th>
                        </tr>
                        <tr v-if="pledge.active_treatment.affected_to_clerk">
                            <td> Greffier </td>
                            <th> {{ pledge.active_treatment.affected_to_clerk.identity.fullName }}</th>
                        </tr>
                    </table>
                </div>

                <div class="mt-4">
                    <BasicButton
                        v-if="can('validate-pledge-by-institution')
                            && pledge?.financial_institution && (pledge.active_treatment.status === statuses.EMITTED || pledge.active_treatment.status === statuses.REMITTED)"
                        @click="validate('Etes-vous sûr de vouloir valider cette demande de mise de gage?')"
                        class="bg-green-500 text-white mx-1"
                    >
                        Valider
                    </BasicButton>
                    <BasicButton
                        v-if="can('reject-pledge-by-institution')
                            && pledge?.financial_institution && (pledge.active_treatment.status === statuses.EMITTED || pledge.active_treatment.status === statuses.REMITTED)"
                        class="btn-danger mx-1"
                        @click="reject('Etes-vous sûr de vouloir rejeter cette demande de mise de gage?')"
                    >
                        Rejeter
                    </BasicButton>

                    <BasicButton
                        v-if="can('validate-pledge-by-justice')
                            && (pledge.financial_institution ? pledge.active_treatment.status === statuses.INSTITUTION_VALIDATED : pledge.active_treatment.status === statuses.EMITTED)"
                        @click="validate('Etes-vous sûr de vouloir valider cette demande de mise de gage?')"
                        class="bg-green-500 text-white mx-1"
                    >
                        Valider
                    </BasicButton>

                    <BasicButton
                        v-if="can('reject-pledge-by-justice')
                            && (pledge.financial_institution ? pledge.active_treatment.status === statuses.INSTITUTION_VALIDATED : pledge.active_treatment.status === statuses.EMITTED)"
                        @click="reject('Etes-vous sûr de vouloir rejeter cette demande de mise de gage?')"
                        class="btn-danger mx-1"
                    >
                        Rejeter
                    </BasicButton>

                    <BasicButton
                        class="bg-yellow-500 mx-1"
                        @click="updateModal = true"
                        v-if="can('update-pledge') && pledge.can_update">
                        <i class="fa fa-edit mr-2"></i>
                        Modifier le gage
                    </BasicButton>

                    <RouterLink
                        v-if="can('lift-pledge')
                            && pledge.active_treatment.status === statuses.ANATT_VALIDATED && !pledge.pledge_lift"
                        :to="'/pledge/lift/' + id"
                        class="bg-green-500 text-white mx-1 p-2"
                    >
                        Demande de levée de gage
                    </RouterLink>
                </div>


<!--                <div class="bg-white mt-4 rounded-md shadow-xl">-->
<!--                    <div class="bg-blue-100 p-4">-->
<!--                        <span class="font-bold text-lg text-blue-900 rounded-md">Pièces jointes</span>-->
<!--                    </div>-->
<!--                    <table class="table">-->
<!--                        <tr>-->
<!--                            <th><i class="fa fa-link"></i> Documents justificatifs</th>-->
<!--                            <th></th>-->
<!--                        </tr>-->

<!--                        <tr>-->
<!--                            <th><i class="fa fa-link"></i> Consemtement du propriétaire</th>-->
<!--                            <th></th>-->
<!--                        </tr>-->
<!--                    </table>-->
<!--                </div>-->

                <div class="bg-white mt-8 rounded-md shadow-xl">
                    <div class="bg-blue-100 p-4">
                        <span class="font-bold text-lg text-blue-900 rounded-md">Informations du propriétaire</span>
                    </div>
                    <table class="table">
                        <template v-if="pledge.vehicle_owner.identity">
                            <tr>
                                <th>Nom</th>
                                <th>{{ pledge?.vehicle_owner.identity.firstname	 }}</th>
                            </tr>

                            <tr>
                                <th>Prénoms</th>
                                <th>{{ pledge?.vehicle_owner.identity.lastname }}</th>
                            </tr>

                            <tr>
                                <th>NPI</th>
                                <th>{{ pledge?.vehicle_owner.identity.npi }}</th>
                            </tr>

                            <tr>
                                <th>Téléphone</th>
                                <th>{{ pledge?.vehicle_owner.identity.telephone }}</th>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <th>Entreprise</th>
                                <th>{{ pledge.vehicle_owner.institution?.name }}</th>
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
                            <th>{{ pledge?.vehicle.vin }}</th>
                        </tr>

                        <tr>
                            <th>Marque</th>
                            <th></th>
                        </tr>

                        <tr>
                            <th>Modèle du véhicule</th>
                            <th>{{ pledge?.vehicle.vehicle_model }}</th>
                        </tr>

                        <tr>
                            <th>Energie</th>
                            <th></th>
                        </tr>

                        <tr>
                            <th>Nombre de places assises</th>
                            <th>{{ pledge?.vehicle.number_of_seats }}</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
	</div>
</template>

<style lang="scss" scoped></style>
