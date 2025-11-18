<template>
	<CreateFormWrapper :col="10" @submit="submit">
		<template #form-head-inner>
			<div class="left">
				<h3>{{ update ? "Modification" : "Création" }}</h3>
			</div>
			<div class="right">
				<div class="buttons">
					<VButton
						dark-outlined
						icon="fa-light fa-arrow-left rem-100"
						light
						type="reset"
						@click="returnPreviousPage($router)"
					>
						Retour
					</VButton>
					<VButton :loading="formLoading" color="primary" raised tabindex="0" type="submit">
						Enregistrer
					</VButton>
				</div>
			</div>
		</template>
		<template #form-body>
			<form>
				<div class="columns is-centered pt-2">
					<div class="column is-8">
						<VField horizontal label="Libelle">
							<VControl fullwidth :errors="errors.label || []">
								<VInput v-model="row.label" name="label" placeholder="Libelle" required type="text" />
							</VControl>
						</VField>
						<VField horizontal label="Description">
							<VControl fullwidth :errors="errors.description || []">
								<VInput
									v-model="row.description"
									name="description"
									placeholder="Description"
									required
									type="text"
								/>
							</VControl>
						</VField>
					</div>
				</div>
			</form>
		</template>
	</CreateFormWrapper>
</template>

<script lang="ts" setup>
	import { ref } from "vue";
	import { useNotyf } from "/@src/composable/useNotyf";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import { returnPreviousPage } from "/@src/utils/helpers";

	const props = defineProps({
		centerId: {
			type: String,
			default: null,
		},
	});

	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url, formLoading, row, errors } = storeToRefs(crudStore);
	const update = ref(false);

	onBeforeMount(() => {
		url.value = "/management-center-types";
	});

	const submit = () => {
		if (update.value) {
			crudStore.updateRow(row.value.id, row.value).then(() => {
				notyf.success("Modification effectué avec succès!");
				router.push({ name: "management_center_types" });
			});
		} else {
			crudStore.createRow(row.value).then(() => {
				notyf.success("Enregistrement effectué avec succès!");
				router.push({ name: "management_center_types" });
			});
		}
	};

	onMounted(() => {
		if (props.centerId) {
			update.value = true;
			crudStore.fetchRow(props.centerId).then((res) => {});
		}
		row.value = {};
	});
</script>

<style lang="scss" scoped></style>
