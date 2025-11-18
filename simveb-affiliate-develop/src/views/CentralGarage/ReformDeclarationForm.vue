<template>
	<div class="intro-y flex items-center mt-4 lg:mt-16">
		<h2 class="text-lg font-semibold mr-auto">
			{{ update ? "Mise à jour de déclaration de réforme" : "Nouvelle déclaration de réforme" }}
		</h2>
	</div>

	<form
		v-if="!update"
		class="flex justify-between items-end dashboard-card !rounded-md mt-4"
		@submit.prevent="fetchInfo"
	>
		<div class="w-full">
			<TextInputGroup
				v-model="row.reference"
				name="reference"
				label="Référence du certificat de vente"
				placeholder=""
				:errors="errors?.reference || []"
				required
				class="w-full"
			/>
		</div>
		<div class="ms-2 w-1/5">
			<BasicButton :loading="formLoading" class="btn-primary w-full h-auto" type="submit"> Vérifier </BasicButton>
		</div>
	</form>

	<div v-if="update" class="intro-y flex items-center mt-8">
		<h2 class="text-lg font-semibold mr-auto">Référence de la réforme : {{ row.reference }}</h2>
	</div>
	<hr class="mt-4" />

	<form
		v-if="row.auction_sale_declaration_id"
		class="grid grid-cols-12 gap-6 mt-8 bg-white p-2 xl:p-4 rounded-md"
		@submit.prevent="handleSubmit"
	>
		<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
			<div class="intro-y flex items-center">
				<h2 class="text-primary text-xl">Rapport</h2>
			</div>
		</div>
		<div class="col-span-12">
			<FileInputGroup
				v-model="row.report"
				name="report"
				required
				:disabled="formLoading"
				accept="application/pdf,image/*"
				:errors="errors.documents || []"
			>
				<p class="mt-1 text-xs leading-4 text-gray-600">Format pris en compte: png, jpg - Taille : 500 Ko</p>
			</FileInputGroup>
		</div>

		<template v-for="(vehicle, index) in row.auction_vehicles" :key="index">
			<hr class="mt-4 col-span-12" />

			<div class="col-span-12 py-3 px-8 bg-[#E8F1FD] rounded-sm">
				<div class="intro-y flex items-center">
					<h2 class="text-primary text-2xl">Véhicule : {{ vehicle.info.vehicle_model }}</h2>
				</div>
			</div>
			<div class="col-span-12 grid grid-cols-12 gap-6">
				<div class="col-span-12">
					<TextInputGroup
						v-model="vehicle.custom_receipt_reference"
						:name="`custom_receipt_reference-${index}`"
						label="Référence du reçu douanier"
						placeholder=""
						:errors="errors.custom_receipt_reference || []"
						required
					/>
				</div>
				<div class="col-span-12 md:col-span-6">
					<FileInputGroup
						v-model="vehicle.divesting_file"
						:name="`divesting_file$-${index}`"
						label="Fichier de cession"
						:errors="errors.divesting_file || []"
						required
						:disabled="formLoading"
						accept="application/pdf,image/*"
					>
						<p class="mt-1 text-xs leading-4 text-gray-600">
							Format pris en compte: png, jpg - Taille : 500 Ko
						</p>
					</FileInputGroup>
				</div>
				<div class="col-span-12 md:col-span-6">
					<FileInputGroup
						v-model="vehicle.pickup_order"
						:name="`pickup_order-${index}`"
						label="Ordre de prise en charge"
						:errors="errors.pickup_order || []"
						required
						:disabled="formLoading"
						accept="application/pdf,image/*"
					>
						<p class="mt-1 text-xs leading-4 text-gray-600">
							Format pris en compte: png, jpg - Taille : 500 Ko
						</p>
					</FileInputGroup>
				</div>
			</div>
		</template>
		<div class="col-span-12 flex align-center justify-end mt-5">
			<button class="btn btn-outline-primary w-36 mr-4 border-2" type="reset" @click="$router.back()">
				Retour
			</button>
			<BasicButton :loading="formLoading" class="btn btn-primary w-36" type="submit">Suivant</BasicButton>
		</div>
	</form>
</template>

<script setup>
	import { useCrudStore } from "@/stores/crud.js";
	import { storeToRefs } from "pinia";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import BasicButton from "@/components/BasicButton.vue";
	import FileInputGroup from "@/components/Form/FileInputGroup.vue";
	import Alert from "@/components/notification/alert.js";
	import { onMounted, ref } from "vue";
	import { useRouter } from "vue-router";

	const router = useRouter();
	const crudStore = useCrudStore();
	const { row, formLoading, errors, url } = storeToRefs(crudStore);
	const update = ref(false);
	const props = defineProps({
		reformId: {
			type: String,
			required: false,
		},
	});

	const fetchInfo = async () => {
		await crudStore
			.makeRequest("GET", {}, `/auction-sale-declarations/show-by-reference/${row.value.reference}`)
			.then((res) => {
				row.value = {
					...row.value,
					auction_sale_declaration_id: res.id,
					auction_vehicles: res.saled_vehicles.map((vehicle) => {
						return {
							id: vehicle.id,
							custom_receipt_reference: null,
							divesting_file: null,
							pickup_order: null,
							info: vehicle.vehicle,
						};
					}),
				};
			});
	};

	const handleSubmit = async () => {
		url.value = "/reform-declarations";
		if (update.value) {
			await crudStore.updateWithFile(row.value.id, row.value).then(() => {
				Alert.success("Déclaration de réforme mise à jour avec succès");
				router.back();
			});
		} else {
			await crudStore.createWithFile(row.value).then(() => {
				Alert.success("Déclaration de réforme enregistrée avec succès");
				router.back();
			});
		}
	};

	onMounted(async () => {
		if (props.reformId) {
			url.value = "reform-declarations";
			update.value = true;
			await crudStore.fetchRow(props.reformId).then((res) => {
				row.value = {
					auction_sale_declaration_id: res.id,
					reference: res.reference,
					auction_vehicles: res.reformed_vehicles.map((vehicle) => {
						return {
							id: vehicle.id,
							custom_receipt_reference: vehicle.custom_receipt_reference,
							divesting_file: null,
							pickup_order: null,
							info: vehicle.vehicle,
						};
					}),
				};
			});
		}
	});
</script>

<style scoped></style>
