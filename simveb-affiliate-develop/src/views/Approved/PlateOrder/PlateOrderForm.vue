<template>
	<Modal :show="open" is-form size="modal-xl" @hidden="$emit('close')" @submit="submit">
		<ModalHeader class="pt-6 bg-primary rounded-sm">
			<h2 class="font-bold text-2xl mr-auto text-light">Commande de plaque d'immatriculation</h2>
			<button class="absolute right-0 top-0 mt-6 mr-3" type="button" @click="$emit('close')">
				<i class="fa-solid fa-x w-8 h-4 font-bold text-light" />
			</button>
		</ModalHeader>
		<ModalBody>
			<template v-if="loading">
				<ServiceCardSkeleton v-for="n in 5" :key="n" />
			</template>
			<template v-else>
				<div class="container m-4">
					<div v-for="(order, index) in row.plates" :key="index" class="grid grid-cols-12 gap-4 mb-8">
						<SelectInputGroup
							v-model="order.plate_shape_id"
							:name="'plate_shape_id_' + index"
							:options="formData.shapes"
							class="col-span-5"
							label="Forme de la plaque"
							option-text="name"
							option-value="id"
							required
						/>
						<SelectInputGroup
							v-model="order.plate_color_id"
							:errors="errors.plate_color_id || []"
							:name="'plate_color_id_' + index"
							:options="formData.colors"
							class="col-span-4"
							label="Couleur de plaque"
							option-text="label"
							option-value="id"
							placeholder="Sélectionner une couleur"
							required
						>
							<template #custom="{ selected, option }">
								<div :style="{ backgroundColor: option.color_code }" class="color-box mr-5"></div>
								<span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
									{{ option.label }}
								</span>
							</template>
						</SelectInputGroup>
						<div class="col-span-2">
							<TextInputGroup
								v-model="order.nb"
								:name="'nb_' + index"
								class="col-span-2"
								label="Quantité"
								min="1"
								required
								type="number"
							/>
						</div>
						<div class="flex items-end">
							<button class="text-danger" type="button" @click="row.plates.splice(index, 1)">
								<i class="fa-solid fa-trash text-xl"></i>
							</button>
						</div>
					</div>
					<div class="flex justify-end mt-4 mx-4">
						<button class="text-primary" type="button" @click="addPlateOrder">
							<i class="fa-solid fa-plus"></i> Ajouter une ligne
						</button>
					</div>
				</div>
			</template>
		</ModalBody>
		<ModalFooter class="">
			<div class="flex mb-2 justify-end">
				<button class="btn btn-outline-secondary me-2 w-36" type="reset" @click="$emit('close')">
					Annuler
				</button>
				<BasicButton :loading="formLoading" class="btn-primary ms-2 w-36" type="submit">
					Enregistrer
				</BasicButton>
			</div>
		</ModalFooter>
	</Modal>
</template>

<script setup>
	import { Modal, ModalBody, ModalFooter, ModalHeader } from "@/global-components/modal/index.js";
	import { onBeforeUnmount, ref, watch } from "vue";
	import { useCrudStore } from "@/stores/crud.js";
	import { storeToRefs } from "pinia";
	import BasicButton from "@/components/BasicButton.vue";
	import ServiceCardSkeleton from "@/components/Skeleton/ServiceCardSkeleton.vue";
	import TextInputGroup from "@/components/Form/TextInputGroup.vue";
	import SelectInputGroup from "@/components/Form/SelectInputGroup.vue";
	import Alert from "@/components/notification/alert.js";

	const emit = defineEmits(["close", "submit"]);
	const props = defineProps({
		open: {
			type: Boolean,
			required: true,
		},
	});

	const crudStore = useCrudStore();
	const { row, formLoading, url, errors, formData, formDataLoaded, loading } = storeToRefs(crudStore);
	const summary = ref({});

	const addPlateOrder = () => {
		row.value.plates.push({
			plate_shape_id: formData.value.shapes[0].id,
			plate_color_id: formData.value.colors[0].id,
			nb: 1,
		});
	};

	const submit = async () => {
		await crudStore.createRow(row.value).then((res) => {
			summary.value = res;
			Alert.success("Commande de plaque enregistrée avec succès");
			emit("submit");
		});
	};

	onBeforeUnmount(() => {
		emit("close");
	});

	watch(
		() => props.open,
		async (value) => {
			row.value.plates = [];
			if (value) {
				if (!formDataLoaded.value) {
					url.value = "plate-orders";
					await crudStore.loadCreateData();
				}
				addPlateOrder();
			}
		}
	);
</script>

<style scoped>
	.color-box {
		width: 30px;
		height: 30px;
		border-radius: 80%;
	}
</style>
