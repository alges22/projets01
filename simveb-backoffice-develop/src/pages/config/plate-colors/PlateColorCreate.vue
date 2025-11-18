<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";
	import { ColorPicker } from "vue-accessible-color-picker";

	const route = useRoute();
	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url, formLoading, errors } = storeToRefs(crudStore);

	const itemId = route.params.id;
	const item = ref({
		label: null,
		color_code: "#fff",
		text_color: "#000",
		cost: null,
	});

	const previewStyle = ref(`background-color: ${item.value.color_code}; color: ${item.value.text_color};`);

	onBeforeMount(() => {
		url.value = "/plate-colors";
	});

	onMounted(() => {
		if (itemId) {
			crudStore.fetchRow(itemId).then((res) => {
				item.value = {
					label: res.label,
					color_code: res.color_code ? res.color_code : "#fff",
					text_color: res.text_color ? res.text_color : "#000",
					cost: res.cost,
				};
			});
		}
	});

	const updatePreviewStyle = () => {
		previewStyle.value = `background-color: ${item.value.color_code}; color: ${item.value.text_color};`;
	};

	const updatePlateColor = (e) => {
		item.value.color_code = e.cssColor;
		updatePreviewStyle();
	};

	const updateTextColor = (e) => {
		item.value.text_color = e.cssColor;
		updatePreviewStyle();
	};

	const submit = () => {
		if (itemId) {
			crudStore.updateRow(itemId, item.value).then(() => {
				notyf.success("Modification effectuée avec succès!");
				router.push({ name: "plate_colors" });
			});
		} else {
			crudStore.createRow(item.value).then(() => {
				notyf.success("Enregistrement effectué avec succès!");
				router.push({ name: "plate_colors" });
			});
		}
	};
</script>
<template>
	<CreateFormWrapper :col="12" @submit="submit">
		<template #form-head-inner>
			<div class="left">
				<h3>{{ itemId ? "Modification" : "Création" }}</h3>
			</div>
			<div class="right">
				<div class="buttons">
					<VButton
						icon="fa-light fa-arrow-left rem-100"
						light
						dark-outlined
						@click="returnPreviousPage($router)"
					>
						Retour
					</VButton>
					<VButton color="primary" raised :loading="formLoading" tabindex="0" type="submit">
						Enregistrer
					</VButton>
				</div>
			</div>
		</template>
		<template #form-body>
			<div class="columns is-centered pt-2 is-multiline">
				<div class="column is-10">
					<div class="columns is-vcentered">
						<div class="column is-2">
							<span>Libellé</span>
						</div>
						<div class="column is-10">
							<VField horizontal>
								<VControl fullwidth :errors="errors.label || []">
									<VInput v-model="item.label" name="label" placeholder="Libellé" required />
								</VControl>
							</VField>
						</div>
					</div>
					<div class="columns is-vcentered">
						<div class="column is-2">
							<span>Coût</span>
						</div>
						<div class="column is-10">
							<VField horizontal>
								<VControl fullwidth :errors="errors.cost || []">
									<VInput
										v-model="item.cost"
										name="cost"
										placeholder="2000"
										type="number"
										min="1"
										required
									/>
								</VControl>
							</VField>
						</div>
					</div>
				</div>
				<div class="column is-10 mt-5">
					<div class="columns mt-5">
						<div class="column is-8">
							<div class="columns">
								<div class="column is-6">
									<div>Couleur de plaque</div>
									<VField horizontal>
										<VControl fullwidth :errors="errors.color_code || []">
											<ColorPicker
												default-format="hex"
												alpha-channel="show"
												:visible-formats="['hex', 'rgb', 'hsv']"
												:color="item.color_code"
												@color-change="updatePlateColor"
											/>
										</VControl>
									</VField>
								</div>
								<div class="column is-6">
									<div>Couleur du texte</div>
									<VField horizontal>
										<VControl fullwidth :errors="errors.text_color || []">
											<ColorPicker
												default-format="hex"
												alpha-channel="show"
												:visible-formats="['hex', 'rgb', 'hsv']"
												:color="item.text_color"
												@color-change="updateTextColor"
											/>
										</VControl>
									</VField>
								</div>
							</div>
						</div>
						<div class="column is-4">
							<div class="has-text-centered">Prévisualisation de la plaque</div>
							<VCard
								class="mt-2 is-flex is-flex-direction-column is-justify-content-center"
								style="height: 150px"
								:style="previewStyle"
							>
								<span class="has-text-centered is-size-4 is-uppercase has-text-weight-bold"
									>Numéro d'immatriculation</span
								>
							</VCard>
						</div>
					</div>
				</div>
			</div>
		</template>
	</CreateFormWrapper>
</template>
