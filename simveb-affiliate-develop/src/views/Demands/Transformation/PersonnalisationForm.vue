<template>
	<div class="grid grid-cols-12 gap-6 mt-5">
		<div class="intro-y col-span-12">
			<div class="grid grid-cols-12 gap-6 mt-5">
				<h4 class="text-lg font-semibold col-span-12">Cliquez sur un type de transformation pour l'ajouter</h4>
				<button
					v-for="(tType, tIndex) in transformationTypes"
					:key="tIndex"
					class="cursor-pointer col-span-12 lg:col-span-4 md:col-span-6 relative mr-2 p-0 bg-white rounded-lg"
					@click="selectType(tType)"
				>
					<span class="w-full flex flex-row justify-between items-center rounded-lg px-4 hover:bg-blue-100">
						<span class="flex flex-col lg:flex-row items-center p-5 pl-0">
							<img
								v-if="tType.label === 'Esthétique'"
								:alt="tType.description"
								class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 rounded-xl object-cover"
								src="@/assets/images/parc/design_pattern.png"
							/>
							<img
								v-else-if="tType.label === 'Performance'"
								:alt="tType.description"
								class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 rounded-xl object-cover"
								src="@/assets/images/parc/screw_car.png"
							/>
							<img
								v-else-if="tType.label === 'Éclairage'"
								:alt="tType.description"
								class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 rounded-xl object-cover"
								src="@/assets/images/parc/car_lights.png"
							/>
							<img
								v-else-if="tType.label === 'Personnalisation thématique'"
								:alt="tType.description"
								class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 rounded-xl object-cover"
								src="@/assets/images/parc/truc_opened.png"
							/>
							<span class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
								<h2 class="font-semibold">{{ tType.label }}</h2>
								<p class="text-gray-500">({{ tType.description }})</p>
							</span>
						</span>
					</span>
				</button>
			</div>

			<h4 class="text-lg font-semibold col-span-12 mt-8">Transformation ajoutées</h4>

			<div class="intro-y col-span-12 mt-5">
				<table class="table table-report mt-5">
					<thead>
						<tr>
							<th class="whitespace nowrap">Type de transformation</th>
							<th class="whitespace nowrap">Description</th>
							<th class="whitespace nowrap w-50"></th>
						</tr>
					</thead>
					<tbody>
						<template v-for="(transformation, index) in transformations" :key="index">
							<template v-if="transformation.characteristics.length > 0">
								<tr>
									<td colspan="3" class="text-center !bg-primary-200 w-100">
										<span class="text-lg">
											{{ transformationTypes.find((t) => t.id === transformation.type).label }}
										</span>
									</td>
								</tr>
								<tr v-for="(characteristic, pIndex) in transformation.characteristics" :key="pIndex">
									<td>
										{{
											transformationTypes
												.find((t) => t.id === characteristic.type)
												.category_characteristics.find(
													(c) => c.code === characteristic.categoryCode
												).label
										}}
									</td>
									<td>
										{{
											transformationTypes
												.find((t) => t.id === characteristic.type)
												.category_characteristics.find(
													(c) => c.code === characteristic.categoryCode
												)
												.vehicle_characteristics.find(
													(v) => v.id === characteristic.characteristicId
												).value
										}}
									</td>
									<td>
										<button
											class="btn btn-danger w-12 border-2 align-right"
											type="button"
											@click="removeTransformation(characteristic)"
										>
											<i class="fa-solid fa-minus" />
										</button>
									</td>
								</tr>
							</template>
						</template>
						<template v-if="transformations.length === 0">
							<tr>
								<td colspan="3" class="text-center">
									Aucune transformation ajoutée. Cliquez sur l'un des types pour en ajouter
								</td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>
			<div class="text-right mt-5">
				<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$emit('prev')">
					Annuler
				</button>
				<button
					class="btn btn-primary w-36"
					type="button"
					:disabled="transformations.length === 0"
					@click="goNext"
				>
					Suivant
				</button>
			</div>
		</div>
	</div>

	<Modal
		:show="transformationModal"
		is-form
		backdrop="static"
		size="modal-lg"
		@hidden="transformationModal = false"
		@submit="addTransformation"
	>
		<ModalHeader class="pt-6 bg-primary rounded-sm">
			<h2 class="font-bold text-2xl mr-auto text-light">
				Ajouter une transformation - {{ transformationType?.typeLabel }}
			</h2>
			<button type="button" class="absolute right-0 top-0 mt-6 mr-3" @click="transformationModal = false">
				<i class="fa-solid fa-x w-8 h-4 font-bold text-light" />
			</button>
		</ModalHeader>
		<ModalBody>
			<div class="container m-4">
				<div v-if="transformationType" class="grid grid-rows-2 gap-4 mb-8">
					<SelectInputGroup
						v-model="transformationType.categoryCode"
						:options="
							transformationTypes.find((t) => t.id === transformationType.type).category_characteristics
						"
						name="category_characteristic"
						label="Personnalisation"
						placeholder="Sélectionner une personnalisation"
						required
						option-value="code"
						option-text="label"
						@update:model-value="transformationType.characteristicId = null"
					/>
					<SelectInputGroup
						v-model="transformationType.characteristicId"
						:disabled="!transformationType.categoryCode"
						:options="
							transformationType.categoryCode
								? transformationTypes
										.find((t) => t.id === transformationType.type)
										.category_characteristics.find(
											(c) => c.code === transformationType.categoryCode
										).vehicle_characteristics
								: []
						"
						name="vehicle_characteristic"
						label="Caractéristique"
						placeholder="Sélectionner une caractéristique"
						required
						option-value="id"
						option-text="value"
					/>
				</div>
			</div>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2 justify-end">
				<button class="btn btn-outline-secondary me-2 w-36" type="reset" @click="transformationModal = false">
					Annuler
				</button>
				<BasicButton class="btn-primary ms-2 w-36" type="submit" :loading="formLoading">
					Enregistrer
				</BasicButton>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { useDemandStore } from "@/stores/demand.js";
	import { storeToRefs } from "pinia";
	import { onMounted, onUnmounted, ref } from "vue";
	import BasicButton from "@/components/BasicButton.vue";
	import { Modal, ModalBody, ModalFooter, ModalHeader } from "@/global-components/modal/index.js";
	import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";
	import Alert from "@/components/notification/alert.js";

	const demandStore = useDemandStore();
	const { demand, loading, formData, formLoading } = storeToRefs(demandStore);
	const transformationModal = ref(false);

	const transformationTypes = ref([]);
	const transformations = ref([]);
	const transformationType = ref(null);

	const selectType = (transformation) => {
		transformationType.value = {
			type: transformation.id,
			typeLabel: transformation.label,
			categoryCode: null,
			characteristicId: null,
		};
		transformationModal.value = true;
	};

	const addTransformation = () => {
		let tType = transformations.value.find((t) => t.type === transformationType.value.type);
		if (tType) {
			if (tType.characteristics.find((c) => c.categoryCode === transformationType.value.categoryCode)) {
				Alert.error("Cette personnalisation a déjà été ajoutée");
				return;
			}
			tType.characteristics.push({ ...transformationType.value });
		} else {
			transformations.value.push({
				type: transformationType.value.type,
				characteristics: [{ ...transformationType.value }],
			});
		}

		transformationModal.value = false;
		transformationType.value = null;
	};

	const removeTransformation = (characteristic) => {
		let tType = transformations.value.find((t) => t.type === characteristic.type);
		tType.characteristics = tType.characteristics.filter((c) => c.categoryCode !== characteristic.categoryCode);
		if (tType.characteristics.length === 0) {
			transformations.value = transformations.value.filter((t) => t.type !== characteristic.type);
		}
	};

	const goNext = () => {
		if (transformations.value.length === 0) {
			Alert.error("Veuillez ajouter au moins une transformation");
			return;
		}

		demand.value.value_id = [];
		transformations.value.forEach((t) => {
			t.characteristics.forEach((c) => {
				demand.value.value_id.push(c.characteristicId);
			});
		});

		emit("next");
	};

	const emit = defineEmits(["next", "prev"]);

	onMounted(async () => {
		if (!formData.value) {
			await demandStore.loadForm("9c9d1e88-cbb6-4e67-9b81-ad7c36ee6925");
		}

		transformationTypes.value = formData.value.transformationTypes;
	});

	onUnmounted(() => {
		transformationModal.value = false;
	});
</script>
