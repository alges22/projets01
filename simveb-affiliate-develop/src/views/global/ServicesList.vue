<template>
	<div class="grid grid-cols-12 gap-6 mt-5">
		<template v-if="loading">
			<ServiceCardSkeleton v-for="n in 5" :key="n" />
		</template>
		<template v-else-if="services?.length > 0">
			<template v-if="isForm">
				<div
					v-for="(service, index) in services"
					:key="index"
					class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md"
				>
					<div class="relative group">
						<input
							:id="'service-select-' + service.code"
							v-model="serviceType"
							class="peer rounded-lg w-6 h-6 absolute top-7 left-4"
							name="service-select"
							type="radio"
							:value="service.code"
						/>
						<label
							class="peer-checked:border-success custom-checkbox-label active:bg-success-700"
							:for="'service-select-' + service.code"
						>
							<CallToActionCard
								image-alt="Cliquez pour choisir la demande"
								:subtitle="service.description"
								:title="service.name"
							>
								<div
									v-if="service.image_url"
									class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 rounded-full p-2"
									:style="{ backgroundColor: service.color }"
								>
									<img alt="Cliquez pour choisir la demande" class="" :src="service.image_url" />
									<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
										<i class="fa-light fa-check"></i>
									</span>
								</div>

								<ServiceImg v-else :service="{ code: service.code }">
									<span class="hidden group-has-[input:checked]:flex floating-icon ml-8 -mt-4">
										<i class="fa-light fa-check"></i>
									</span>
								</ServiceImg>
							</CallToActionCard>
						</label>
					</div>
				</div>

				<div class="intro-y col-span-12">
					<div class="text-right mt-4">
						<button
							class="btn btn-outline-primary w-36 mr-4 border-2"
							type="button"
							@click="$router.back()"
						>
							Annuler
						</button>
						<button
							class="btn btn-primary w-36"
							type="button"
							:disabled="!serviceType"
							@click="goTo(services.find((s) => s.code === serviceType))"
						>
							Suivant
						</button>
					</div>
				</div>
			</template>

			<template v-else>
				<TransitionGroup name="list">
					<div
						v-if="can('update-im-demand')"
						key="0"
						class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md"
					>
						<CallToActionCard
							button-text="Demander"
							image-alt="Cliquez pour choisir la demande de modification"
							subtitle="Mettre à jour une demande déjà effectuée"
							title="Modification"
							@click="handleDemandUpdate"
						>
							<ServiceImg :service="{ code: service.code }" />
						</CallToActionCard>
					</div>
					<div
						v-for="(service, index) in displayedServices"
						:key="index + 1"
						class="col-span-12 md:col-span-6 bg-white rounded-md shadow-md"
					>
						<CallToActionCard
							button-text="Demander"
							image-alt="Cliquez pour choisir la demande"
							:subtitle="service.description"
							:title="service.name"
							@click="goTo(service)"
						>
							<div
								v-if="service.image_url"
								class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 rounded-full p-2 flex items-center justify-center"
								:style="{ backgroundColor: service.color }"
							>
								<img alt="Cliquez pour choisir la demande" class="" :src="service.image_url" />
							</div>

							<ServiceImg v-else :service="{ code: service.code }" />
						</CallToActionCard>
					</div>
				</TransitionGroup>

				<div v-if="services.length > 6" class="intro-y col-span-12">
					<div class="text-center mt-4">
						<button
							class="btn btn-outline-primary w-36 mr-4 border-2"
							type="button"
							@click="
								servicesShowCount < services.length
									? (servicesShowCount = services.length)
									: (servicesShowCount = 6)
							"
						>
							<template v-if="servicesShowCount < services.length"> Afficher plus </template>
							<template v-if="servicesShowCount === services.length"> Afficher moins </template>
						</button>
					</div>
				</div>
			</template>
		</template>
		<template v-else>
			<div class="col-span-12">
				<div class="intro-y text-center">
					<p class="text-lg text-gray-500">Aucun service disponible pour le moment</p>
				</div>
			</div>
		</template>
	</div>
</template>

<script setup>
	import { computed, onMounted, ref } from "vue";
	import client from "@/assets/js/axios/client.js";
	import CallToActionCard from "@/components/CallToActionCard.vue";
	import { useRouter } from "vue-router";
	import ServiceCardSkeleton from "@/components/Skeleton/ServiceCardSkeleton.vue";
	import { serviceMap } from "../../../space-config.js";
	import Alert from "@/components/notification/alert.js";
	import Swal from "sweetalert2";
	import { userHasPermissions } from "@/helpers/permissions.js";
	import ServiceImg from "@/views/global/ServiceImg.vue";

	defineProps({
		isForm: Boolean,
	});

	const services = ref([]);
	const loading = ref(true);
	const router = useRouter();
	const serviceType = ref(null);
	const servicesShowCount = ref(6);
	const { can } = userHasPermissions();

	defineEmits(["back"]);

	const goTo = (service) => {
		service
			? router.push({ name: serviceMap[service.code], params: { serviceId: service.id } })
			: Alert.error("Cliquez pour choisir le service");
	};

	const displayedServices = computed(() => {
		return services.value.slice(0, servicesShowCount.value);
	});

	const fetchServices = () => {
		loading.value = true;
		return new Promise((resolve) => {
			client({
				method: "GET",
				url: "/client/services?_paginate=0",
			}).then((response) => {
				services.value = [];
				response.data.map((service) => {
					if (service.children.length > 0) {
						service.children.map((child) => {
							services.value.push(child);
						});
					} else {
						services.value.push(service);
					}
				});
				loading.value = false;
				resolve();
			});
		});
	};

	onMounted(async () => {
		await fetchServices();
	});

	const handleDemandUpdate = () => {
		Swal.fire({
			title: "Mettre à jour une demande !",
			text: "Seuls les demandes en cours peuvent être mises à jour. Des frais supplémentaires peuvent s'appliquer selon la modification.",
			icon: "info",
			showCancelButton: true,
			confirmButtonText: "Continuer",
			cancelButtonText: "Annuler",
		}).then((result) => {
			if (result.isConfirmed) {
				router.push({ name: "update-demand" });
			}
		});
	};
</script>

<style>
	.list-move,
	.list-enter-active,
	.list-leave-active {
		transition: all 0.5s ease;
	}

	.list-enter-from,
	.list-leave-to {
		opacity: 0;
		transform: translateX(30px);
	}

	.list-leave-active {
		position: absolute;
	}
</style>
