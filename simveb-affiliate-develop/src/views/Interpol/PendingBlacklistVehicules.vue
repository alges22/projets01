<template>
	<div class="intro-y mt-4 flex items-center col-span-12">
		<button class="btn btn-outline-secondary mr-4 border rounded-full" type="button" @click="$router.back()">
			<i class="fa-light fa-arrow-left w-4 h-4"></i>
		</button>
		<h2 class="mr-auto text-lg font-bold">Véhicules en attente d'ajout à la liste noire</h2>
	</div>

	<div class="grid grid-cols-12 gap-6 mt-5 dashboard-card">
		<DataTable
			:headers="headers"
			:items="vehicles"
			:loading="loading"
			:meta="meta"
			empty-text="Aucun véhicule n'est en attente de validation pour le moment."
			header-class="uppercase text-start"
			search
			@update-datatable="(newOptions) => (options = newOptions)"
		>
			<template #vin="{ item }">
				<KeyField :value="item.vin" :to="{ name: 'vehicle-view', params: { vin: item.vin } }" />
			</template>
			<template #statut="{ item }">
				<StatusComponent :status="item.status" :status-text="item.status_label" />
			</template>
			<template #actions="{ item }">
				<template v-if="item.status === 'submitted' && !item.validated_at && !item.rejected_at">
					<button
						v-if="can('validate-blacklist-vehicle')"
						v-tooltip
						class="flex items-center mr-3 btn btn-outline-success shadow-md"
						title="Valider l'ajout"
						@click="handleValidation(item, true)"
					>
						<i class="fa-light fa-check w-4 h-4" />
					</button>
					<button
						v-if="can('reject-blacklist-vehicle')"
						v-tooltip
						class="flex items-center mr-3 btn btn-outline-danger shadow-md"
						title="Rejeter la demande"
						@click="handleValidation(item, false)"
					>
						<i class="fa-light fa-x w-4 h-4" />
					</button>
				</template>
				<router-link
					v-if="can('show-vehicle')"
					v-tooltip
					:to="{ name: 'vehicle-view', params: { vin: item.vin } }"
					class="flex items-center mr-3 btn btn-outline-primary shadow-md"
					title="Voir le véhicule"
				>
					<i class="fa-light fa-eye w-4 h-4" />
				</router-link>
			</template>
		</DataTable>
	</div>
</template>

<script setup>
	import DataTable from "@/components/DataTable.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import { onMounted, ref } from "vue";
	import { storeToRefs } from "pinia";
	import { useCrudStore } from "@/stores/crud.js";
	import KeyField from "@/components/KeyField.vue";
	import Swal from "sweetalert2";
	import Alert from "@/components/notification/alert.js";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const { rows: vehicles, url, loading, meta } = storeToRefs(crudStore);
	const options = ref({
		status: "submitted",
	});

	const headers = [
		{ key: "vin", title: "Véhicule", sortable: false },
		{ key: "statut", title: "Status", sortable: false },
		{ key: "author_id", title: "Ajouté par" },
		{ key: "created_at", title: "Date et heure", sortable: false },
	];

	const fetchBlacklistedVehicles = async (metadata) => {
		url.value = "/blacklist-vehicles";
		await crudStore.fetchRows(metadata);
	};

	const handleValidation = (item, accept = true) => {
		Swal.fire({
			title: "Êtes-vous sûr?",
			text: accept
				? "Valider cette action va ajouter ce véhicule à la liste noire. Les véhicules en liste noire ne pourront plus être enregistrés."
				: "Ne pas ajouter ce véhicule à la liste noire?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: accept ? "Oui, Je confirme" : "Oui, Rejeter",
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
						`/blacklist-vehicles/${item.id}/${accept ? "validate" : "reject"}`
					)
					.then(() => {
						fetchBlacklistedVehicles(options.value);
						Alert.success("Opération effectuée avec succès");
					});
			}
		});
	};

	onMounted(async () => {
		await fetchBlacklistedVehicles(options.value);
	});
</script>

<style scoped></style>
