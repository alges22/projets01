<template>
	<div class="intro-y flex flex-col sm:flex-row items-center mt-4 justify-between">
		<h2 class="text-lg font-semibold">Détails de l'enregistrement</h2>
		<div v-if="data" class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<button
				v-if="can('update-gma-vehicle') && !data.validated_at"
				class="btn btn-outline-warning mr-2"
				@click="handleUpdate"
			>
				<i class="fa-light fa-pencil me-2"></i> Modifier
			</button>
			<button
				v-if="can('delete-gma-vehicle')"
				class="btn btn-outline-danger mr-2"
				@click="handleDelete(data?.id)"
			>
				<i class="fa-light fa-trash me-2"></i> Supprimer
			</button>
			<template v-if="hasOnePermissions(['validate-gma-vehicle', 'reject-gma-vehicle']) && !data?.validated_at">
				<button class="btn btn-success mr-2" @click="validateVehicle">
					<i class="fa-light fa-check me-2"></i> Valider
				</button>
				<button class="btn btn-danger mr-2" @click="rejectVehicle">
					<i class="fa-light fa-times me-2"></i> Rejeter
				</button>
			</template>
		</div>
	</div>

	<LoaderSpinner v-if="loading" class="mt-4" type="block" />

	<div v-else class="grid grid-cols-12 gap-6 mt-4 bg-white p-2 xl:p-4 rounded-md">
		<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
			<div class="intro-y flex items-center">
				<h2 class="text-primary text-2xl">Enregistrement</h2>
			</div>
		</div>
		<div class="col-span-12 grid grid-cols-12 gap-6">
			<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Date de l'enregistrement</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ data.created_at }}</p>
				</div>
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Statut</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">
						<StatusComponent :status="data.status" :status-text="data.status_label" />
					</p>
				</div>
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Validé par :</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">
						{{ data.validated_by ? data.validator?.identity.fullName : "Non validé" }}
					</p>
				</div>
			</div>
			<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">VIN du véhicle</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ data.vin }}</p>
				</div>
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Déclaration</p>
					<a :href="data.file.path.path" download class="font-bold text-primary w-1/2 text-lg leading-5">
						<i class="fa-light fa-download me-2"></i> Télécharger
					</a>
				</div>
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Référence de douane</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ data.customs_reference }}</p>
				</div>
			</div>
		</div>

		<InstitutionInfoCard :institution-info="data.institution" title="Institution" />

		<OwnerInfoCard :owner-info="data.author.identity" title="Auteur" />

		<VehicleInfoCard :loading="loading" :vehicle-info="data.vehicle" />
	</div>

	<VehicleRegistrationForm
		v-if="can('update-gma-vehicle')"
		url="gma-vehicles"
		:open="updateModal"
		update
		@close="updateModal = false"
		@submit="
			() => {
				updateModal = false;
				fetchVehicle();
			}
		"
	/>
</template>

<script setup>
	import { useVehicleStore } from "@/stores/vehicle.js";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onMounted, onUnmounted, ref } from "vue";
	import VehicleInfoCard from "@/components/VehicleInfoCard.vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import StatusComponent from "@/components/StatusComponent.vue";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";
	import InstitutionInfoCard from "@/components/InstitutionInfoCard.vue";
	import { useDeleteConfirmation } from "@/composables/useDeleteConfirmation.js";
	import Alert from "@/components/notification/alert.js";
	import { useRouter } from "vue-router";
	import Swal from "sweetalert2";
	import VehicleRegistrationForm from "@/views/GMA/VehicleRegistrationForm.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const { can, hasOnePermissions } = userHasPermissions();
	const props = defineProps({
		id: {
			type: String,
			required: true,
		},
	});

	const router = useRouter();
	const vehicleStore = useVehicleStore();
	const { vehicle: data, loading, url } = storeToRefs(useVehicleStore());
	const updateModal = ref(false);

	const { handleDelete } = useDeleteConfirmation(async () => {
		await vehicleStore.deleteVehicle(data.value.id).then(() => {
			Alert.success("Enregistrement supprimé.");
			router.back();
		});
	}, "Êtes-vous sur de vouloir supprimer cet enregistrement ?");

	const validateVehicle = async () => {
		Swal.fire({
			title: "Êtes-vous sûr?",
			text: "Vous ne pourrez pas revenir en arrière!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Oui, valider!",
		}).then(async (result) => {
			if (result.isConfirmed) {
				url.value = "validate-gma-vehicles";
				await vehicleStore.validateVehicle({ gma_vehicles: [data.value.id] }).then(() => {
					Alert.success("Véhicule validé avec succès.");
					fetchVehicle();
				});
			}
		});
	};

	const rejectVehicle = async () => {
		Swal.fire({
			title: "Souhaitez-vous rejeter ce véhicule?",
			text: "Vous ne pourrez pas revenir en arrière!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Oui, Rejeter!",
			input: "textarea",
			inputPlaceholder: "Raison du rejet",
			inputValidator: (value) => {
				if (!value) {
					return "La raison du rejet est requise!";
				}
			},
		}).then(async (result) => {
			if (result.isConfirmed) {
				url.value = "reject-gma-vehicles";
				await vehicleStore
					.validateVehicle({
						gma_vehicles: [
							{
								id: data.value.id,
								reason: result.value,
							},
						],
					})
					.then(() => {
						Alert.success("Véhicule rejeté avec succès.");
						fetchVehicle();
					});
			}
		});
	};

	const handleUpdate = () => {
		updateModal.value = true;
	};

	const fetchVehicle = async () => {
		url.value = "gma-vehicles";
		await vehicleStore.fetchVehicle(props.id);
	};

	onBeforeMount(() => {
		loading.value = true;
		updateModal.value = false;
	});

	onMounted(async () => {
		await fetchVehicle();
	});

	onUnmounted(() => {
		url.value = "gov-vehicles";
		loading.value = false;
	});
</script>

<style scoped></style>
