<template>
	<div class="intro-y flex flex-col sm:flex-row items-center mt-4">
		<h2 class="text-lg mr-auto font-bold">Déclaration de réforme</h2>
	</div>

	<div class="intro-y grid grid-cols-11 gap-5 mt-5">
		<div class="col-span-12 lg:col-span-4 2xl:col-span-3">
			<div class="box p-5 rounded-md">
				<div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
					<div class="font-bold text-base truncate">Détails de la réforme</div>
					<router-link
						v-if="can('update-reform-declaration')"
						:to="{ name: 'reform-declaration-create', params: { reformId: reform.id } }"
						class="flex items-center ml-auto text-warning"
					>
						<i class="fa-light fa-pencil w-4 h-4 mr-2" /> Modifier
					</router-link>
				</div>
				<div class="flex items-center">
					<i class="fa-light fa-clipboard w-4 h-4 text-slate-500 mr-2" />
					Référence:
					<button
						v-tooltip
						title="Copier la référence"
						class="underline decoration-dotted ml-1"
						@click="copyToClipboard(reform.reference).then(() => Alert.success('Copié !'))"
					>
						{{ reform.reference }}
					</button>
				</div>
				<div class="flex items-center mt-4">
					<i class="fa-light fa-calendar w-4 h-4 text-slate-500 mr-2" />
					Date de déclaration: 24 March 2022
				</div>
				<div class="flex items-center mt-4">
					<i class="fa-light fa-calendar w-4 h-4 text-slate-500 mr-2" />
					Date de la vente: 24 March 2022
				</div>
				<div class="flex items-center mt-4">
					<i class="fa-light fa-clock w-4 h-4 text-slate-500 mr-2" />
					Status:
					<span class="bg-success/20 text-success rounded px-2 ml-1">Completed</span>
				</div>
				<div class="flex items-center mt-4">
					<i class="fa-light fa-file-archive w-4 h-4 text-slate-500 mr-2" />
					Rapport:
					<a
						v-tooltip
						title="Voir le rapport"
						:href="reform.report"
						target="_blank"
						download
						class="btn btn-outline-warning underline decoration-dotted ml-1"
					>
						<i class="fa-light fa-download w-4 h-4" />
					</a>
				</div>
			</div>
		</div>

		<div class="col-span-12 lg:col-span-7 2xl:col-span-8">
			<div class="box p-5 rounded-md">
				<div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
					<div class="font-medium text-base truncate">Les véhicules réformés</div>
				</div>
				<div class="overflow-auto lg:overflow-visible grid grid-cols-12 gap-6 -mt-3">
					<div
						v-for="(vehicle, index) in reform.reformed_vehicles"
						:key="index"
						class="intro-y col-span-12 md:col-span-6 lg:col-span-4"
					>
						<div class="box">
							<div class="p-5">
								<div
									class="h-40 2xl:h-56 image-fit rounded-md overflow-hidden before:block before:absolute before:w-full before:h-full before:top-0 before:left-0 before:z-10 before:bg-gradient-to-t before:from-black before:to-black/10"
								>
									<img alt="" class="rounded-md object-cover" src="@/assets/images/car/car_9.png" />
									<span
										class="absolute top-0 bg-warning/80 text-white text-xs m-5 px-2 py-1 rounded z-10"
									>
										VIN : {{ vehicle.vehicle.vin }}
									</span>
									<div class="absolute bottom-0 text-white px-5 pb-6 z-10">
										<span class="block font-medium text-base">
											{{ vehicle.vehicle.vehicle_model }}
										</span>
									</div>
								</div>
								<div class="text-slate-600 dark:text-slate-500 mt-5">
									<div class="flex items-center">
										<i class="fa-light fa-coin-blank w-4 h-4 mr-2" /> Prix :
										<span class="font-bold ms-2">{{ formatPrice(vehicle.price) }}</span>
									</div>
									<div class="flex items-center mt-3">
										<i class="fa-light fa-note w-4 h-4 mr-2" />Référence du reçu douanier :
										<button
											v-tooltip
											title="Copier la référence"
											class="font-bold ms-2 underline decoration-dotted"
											@click="
												copyToClipboard(vehicle.custom_receipt_reference).then(() =>
													Alert.success('Copié !')
												)
											"
										>
											{{ vehicle.custom_receipt_reference }}
										</button>
									</div>
									<div class="flex items-center mt-3">
										<i class="fa-light fa-user w-4 h-4 mr-2" />Acheteur :
										{{ vehicle.custom_receipt_reference }}
									</div>
									<div class="flex items-center mt-3">
										<i class="fa-light fa-user w-4 h-4 mr-2" />NPI de l'acheteur :
										<span class="font-bold ms-2">{{ vehicle.buyer_npi }}</span>
									</div>
									<div class="flex items-center mt-3">
										<i class="fa-light fa-file-archive w-4 h-4 text-slate-500 mr-2" />
										Fichier de cession:
										<a
											v-tooltip
											title="Voir"
											target="_blank"
											:href="vehicle.divesting_file"
											download=""
											class="btn btn-outline-warning underline decoration-dotted ml-1"
										>
											<i class="fa-light fa-download w-4 h-4" />
										</a>
									</div>
									<div class="flex items-center mt-3">
										<i class="fa-light fa-file-archive w-4 h-4 text-slate-500 mr-2" />
										Ordre de prise en charge:
										<a
											v-tooltip
											title="Voir"
											target="_blank"
											:href="vehicle.pickup_order"
											download
											class="btn btn-outline-warning underline decoration-dotted ml-1"
										>
											<i class="fa-light fa-download w-4 h-4" />
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
	import { useCrudStore } from "@/stores/crud.js";
	import { storeToRefs } from "pinia";
	import { onMounted } from "vue";
	import { copyToClipboard, formatPrice } from "@/helpers/utils.js";
	import Alert from "@/components/notification/alert.js";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const { can } = userHasPermissions();

	const props = defineProps({
		reformId: {
			type: String,
			required: true,
		},
	});

	const crudStore = useCrudStore();
	const { url, row: reform } = storeToRefs(crudStore);

	onMounted(async () => {
		url.value = "reform-declarations";
		await crudStore.fetchRow(props.reformId);
	});
</script>

<style scoped></style>
