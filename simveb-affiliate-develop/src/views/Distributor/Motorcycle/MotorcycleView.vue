<template>
	<div class="intro-y flex flex-col sm:flex-row items-center mt-4 justify-between">
		<h2 class="text-lg font-semibold">Détails du véhicule</h2>
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<button v-if="can('update-motorcycle')" class="btn btn-outline-warning mr-2" @click="handleUpdate">
				<i class="fa-light fa-pencil me-2"></i> Modifier
			</button>
			<button v-if="can('delete-motorcycle')" class="btn btn-outline-danger mr-2" @click="handleDelete(data?.id)">
				<i class="fa-light fa-trash me-2"></i> Supprimer
			</button>
		</div>
	</div>

	<LoaderSpinner v-if="loading" class="mt-4" type="block" />

	<div v-else class="grid grid-cols-12 gap-6 mt-4 bg-white p-2 xl:p-4 rounded-md">
		<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
			<div class="intro-y flex items-center">
				<h2 class="text-primary text-2xl">Véhicule</h2>
			</div>
		</div>
		<div class="col-span-12 grid grid-cols-12 gap-6">
			<div class="col-span-12 lg:col-span-6 mx-2 lg:mx-9 intro-y">
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Date de l'enregistrement</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ data.created_at }}</p>
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
				<!-- <div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Déclaration</p>
					<a :href="data.file.path.path" download class="font-bold text-primary w-1/2 text-lg leading-5">
						<i class="fa-light fa-download me-2"></i> Télécharger
					</a>
				</div> -->
				<div class="flex items-center pb-2 mt-2 border-b">
					<p class="w-1/2 text-lg leading-5">Référence de douane</p>
					<p class="font-bold text-dark w-1/2 text-lg leading-5">{{ data.customs_reference }}</p>
				</div>
			</div>
		</div>

		<InstitutionInfoCard :institution-info="data.institution" title="Institution" />

		<OwnerInfoCard :owner-info="data.author.identity" title="Auteur" />
	</div>

	<MotorcycleRegistrationForm
		v-if="can('update-motorcycle')"
		:open="updateModal"
		update
		@close="updateModal = false"
	/>
</template>

<script setup>
	import { useMotorcycleStore } from "@/stores/motorcycle.js";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onMounted, onUnmounted, ref } from "vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import OwnerInfoCard from "@/components/OwnerInfoCard.vue";
	import InstitutionInfoCard from "@/components/InstitutionInfoCard.vue";
	import { useDeleteConfirmation } from "@/composables/useDeleteConfirmation.js";
	import Alert from "@/components/notification/alert.js";
	import { useRouter } from "vue-router";
	import MotorcycleRegistrationForm from "./MotorcycleRegistrationForm.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const props = defineProps({
		id: {
			type: String,
			required: true,
		},
	});

	const router = useRouter();
	const motorcycleStore = useMotorcycleStore();
	const { vehicle: data, loading } = storeToRefs(useMotorcycleStore());
	const updateModal = ref(false);
	const { can } = userHasPermissions();

	const { handleDelete } = useDeleteConfirmation(async (id) => {
		await motorcycleStore.deleteVehicle(id).then(() => {
			Alert.success("Véhicule supprimé avec succès.");
			router.back();
		});
	}, "Êtes-vous sur de vouloir supprimer cet véhicule ?");

	const handleUpdate = () => {
		updateModal.value = true;
	};

	const fetchVehicle = async () => {
		await motorcycleStore.fetchVehicle(props.id);
	};

	onBeforeMount(() => {
		loading.value = true;
		updateModal.value = false;
	});

	onMounted(async () => {
		await fetchVehicle();
	});

	onUnmounted(() => {
		loading.value = false;
	});
</script>

<style scoped></style>
