<template>
	<div class="page-content-inner">
		<div class="columns">
			<div v-if="service" class="font-medium text-lg">
				{{ service?.name }} <i class="fa fa-angle-right me-4"></i>
			</div>

			<span v-if="step" class="text-lg font-medium">{{ step?.label }}</span>
		</div>
		<div class="form-content-wrapper">
			<CreateFormWrapper @submit="handleSubmit">
				<template #form-head-inner>
					<div class="left">
						<h3>{{ update ? "Modification" : "Création" }}</h3>
					</div>
					<div class="right">
						<div class="buttons">
							<VButton
								icon="fa-light fa-arrow-left rem-100"
								light
								dark-outlined
								type="reset"
								@click="returnPreviousPage($router)"
							>
								Retour
							</VButton>
							<VButton color="primary" raised :loading="formLoading" tabindex="0" type="submit">
								{{ !update ? "Enregistrer" : "Modifier" }}
							</VButton>
						</div>
					</div>
				</template>
				<template #form-body>
					<div class="columns is-centered pt-2">
						<div class="columns is-multiline">
							<!-- <div class="column is-12">
								<VField>
									<VLabel required>Nom</VLabel>
									<VControl fullwidth :errors="[]">
										<VInput v-model="action.name" name="name" required />
									</VControl>
								</VField>
							</div> -->
							<div v-if="createData?.service_permissions != undefined" class="column is-12">
								<VField>
									<VLabel required>Actions</VLabel>
									<div v-if="createData?.service_permissions.length <= 0">
										<p class="text-yellow-500 font-semibold text-lg">
											Aucune action n'est disponible pour cette étape.
										</p>
									</div>
									<div v-else class="grid grid-cols-2 gap-3 p-4">
										<VControl
											v-for="(permission, index) in createData?.service_permissions"
											:key="index"
											:errors="errors.permission_service_id || []"
										>
											<div class="radio-pills">
												<div class="radio-pill">
													<input
														v-model="action.permission_service_id"
														type="radio"
														:checked="
															index === 0 ||
															permission.pivot.id === action.permission_service_id
														"
														name="permissions"
														:value="permission.pivot.id"
													/>
													<div class="radio-pill-inner p-2 !w-full">
														<h2 class="has-text-weight-bold">
															{{ permission.label }}
														</h2>
													</div>
												</div>
											</div>
										</VControl>
									</div>
								</VField>
							</div>
							<div class="column is-6">
								<VField>
									<VLabel required>Durée</VLabel>
									<VControl fullwidth :errors="errors.duration || []">
										<VInput
											v-model="action.duration"
											name="duration"
											type="number"
											placeholder="En minutes"
											required
										/>
									</VControl>
								</VField>
							</div>

							<div v-if="createData.service_steps != undefined" class="column is-6">
								<VField>
									<VLabel required>Ordre d'exécution</VLabel>
									<!-- <VControl fullwidth :errors="errors.code || []">
										<VInput v-model="service.code" name="code" required />
									</VControl> -->
									<VControl fullwidth :errors="errors.position || []">
										<VInput
											v-model="action.position"
											name="position"
											type="number"
											min="1"
											required
										/>
									</VControl>
								</VField>
							</div>

							<div class="column is-12">
								<VField>
									<VLabel required>Mode de déclenchement</VLabel>
									<VControl fullwidth :errors="errors.process_type || []">
										<div class="radio-pills">
											<div
												v-for="process_type in [
													{ label: 'Automatique', value: 'automatic' },
													{ label: 'Manuelle', value: 'manual' },
												]"
												:key="process_type"
												class="radio-pill"
											>
												<input
													v-model="action.process_type"
													type="radio"
													name="process_type"
													:value="process_type.value"
												/>
												<div class="radio-pill-inner">
													<h2 class="has-text-weight-bold">
														{{ process_type.label }}
													</h2>
												</div>
											</div>
										</div>
									</VControl>
								</VField>
							</div>
						</div>
					</div>
				</template>
			</CreateFormWrapper>
		</div>
	</div>
</template>

<script setup>
	import { storeToRefs } from "pinia";
	import { onMounted } from "vue";
	import { useActionStore } from "/@src/stores/modules/action";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import client from "/@src/composable/axiosClient";
	import { useNotyf } from "/@src/composable/useNotyf";
	import { useCrudStore } from "/@src/stores/modules/crud";

	const router = useRouter();

	const actionStore = useActionStore();
	const crudStore = useCrudStore();
	const { service, step, action, createData, nextPosition } = storeToRefs(actionStore);
	const { rows, url } = storeToRefs(crudStore);
	const modalChangePositionIsOpen = ref(false);
	const loading = ref(false);
	const notif = useNotyf();
	const props = defineProps(["serviceId", "stepId", "actionId"]);
	const errors = ref({});
	const update = ref(false);

	const handleSubmit = () => {
		loading.value = true;
		if (update.value == false) {
			action.value.service_step_id = step?.value.pivot.id;
			client
				.post("/actions", { ...action.value })
				.then((response) => {
					notif.success("Action créer avec succès");
					action.value = {};
					// modalChangePositionIsOpen.value = true;
					// console.log(response.data);
					router.push({ name: "actions" });
				})
				.catch((error) => {
					errors.value = error.response.data.errors || error.response.data;
				})
				.finally(() => (loading.value = false));
		} else {
			action.value._method = "PUT";
			client
				.post("/actions/" + props.actionId, { ...action.value })
				.then((response) => {
					notif.success("Action Modifié avec succès");
					action.value = {};
					// modalChangePositionIsOpen.value = true;
					// console.log(response.data);
					router.push({ name: "actions" });
				})
				.catch((error) => {
					errors.value = error.response.data.errors || error.response.data;
				})
				.finally(() => (loading.value = false));
		}
	};
	// const getItems = async (metadata) => {
	// 	await crudStore.fetchRows(metadata);
	// };
	// if (nextPosition.value == 0 || nextPosition.value == undefined) {
	// 	url.value = "/actions";
	// 	getItems({ service_step_id: props.stepId }).then(() => (nextPosition.value = rows.value.length + 1));
	// }
	onMounted(async () => {
		if (props.actionId != undefined && "" != props.actionId) {
			update.value = true;
			client.get("/actions/" + props.actionId).then((res) => (action.value = res.data));
		} else {
			update.value = false;
			action.value = {};
		}
		const url = `/action/create-by-services/${props.serviceId}`;
		if (service?.name == undefined) {
			await client.get("/services/" + props.serviceId).then((res) => {
				service.value = res.data;
				step.value = service.value.steps.find((step) => step.id == props.stepId);
			});
		}
		await client.get(url).then((response) => {
			createData.value = response.data;
		});
	});
</script>

<style lang="scss" scoped></style>
