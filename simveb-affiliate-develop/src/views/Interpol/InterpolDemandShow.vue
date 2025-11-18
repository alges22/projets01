<template>
	<div class="intro-y flex items-center mt-4">
		<div v-if="demand?.is_blacklisted" class="alert show flex items-center mb-2 alert-danger" role="alert">
			<i class="fa fa-exclamation-triangle fa-2xl me-2" aria-hidden="true" />
			Cet véhicule est en liste noire. &nbsp;
			<router-link
				v-if="can('show-vehicle')"
				:to="{ name: 'vehicle-view', params: { vin: demand.vehicle.vin } }"
				class="underline"
			>
				Voir les détails du véhicule
			</router-link>
		</div>
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<template v-if="demand?.status !== 'affected_to_interpol'">
				<button
					v-if="can('interpol-validate-im-demand')"
					class="btn btn-outline-success shadow-md mr-4"
					@click="handleValidation(true)"
				>
					<i class="fa-light fa-check w-4 h-4 me-2"></i>
					Valider
				</button>
				<button
					v-if="can('reject-interpol-im-demand')"
					class="btn btn-outline-danger shadow-md mr-4"
					@click="handleValidation(false)"
				>
					<i class="fa-light fa-x w-4 h-4 me-2"></i>
					Rejeter
				</button>
			</template>
		</div>
	</div>

	<LoaderSpinner v-if="loading" type="block" />

	<AdminImmatriculationDemandShow
		v-else
		:steps="demand.steps"
		:demand="demand.demand"
		:files="demand.files"
		:owner="demand.vehicle_owner"
		:vehicle="demand.vehicle"
	/>

	<div class="intro-y col-span-12">
		<div class="flex align-center justify-end mt-4">
			<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.back()">
				Retour
			</button>
		</div>
	</div>
</template>

<script setup>
	import { useCrudStore } from "@/stores/crud.js";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onMounted } from "vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import Swal from "sweetalert2";
	import Alert from "@/components/notification/alert.js";
	import AdminImmatriculationDemandShow from "@/views/global/AdminImmatriculationDemandShow.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const { loading, row: demand, url } = storeToRefs(crudStore);

	const props = defineProps({
		id: {
			type: String,
			required: true,
		},
	});

	const handleValidation = (accept = true) => {
		Swal.fire({
			title: accept ? "Êtes-vous sûr ?" : "Rejeter cette demande",
			text: accept
				? "Vous confirmer qu'il n'y a pas de problème avec cette demande?"
				: "Décrivez les raisons du rejet de la demande",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Oui, Je confirme",
			cancelButtonText: "Non, Annuler",
			input: accept ? undefined : "textarea",
			inputPlaceholder: "Raison du rejet",
			inputValidator: (value) => {
				if (!value) {
					return "La raison du rejet est obligatoire";
				}
			},
		}).then((result) => {
			if (result.isConfirmed) {
				crudStore
					.makeRequest(
						"PUT",
						{
							reason: result.value,
						},
						`/blacklist-vehicles/${props.id}/${accept ? "validate" : "reject"}}`
					)
					.then(() => {
						fetchDemand();
						Alert.success("Opération effectuée avec succès");
					});
			}
		});
	};

	const fetchDemand = async () => {
		url.value = "/interpol/demands";
		await crudStore.fetchRow(props.id);
	};

	onBeforeMount(() => {
		loading.value = true;
		demand.value = null;
	});

	onMounted(async () => {
		await fetchDemand();
	});
</script>

<style scoped></style>
