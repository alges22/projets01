<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";

	const code = ref("");
	const description = ref("");
	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url, formLoading, errors } = storeToRefs(crudStore);
	const route = useRoute();

	const itemId = route.params.id;

	onBeforeMount(() => {
		url.value = "/document-types";
	});

	onMounted(() => {
		crudStore.fetchRow(itemId).then((res) => {
			code.value = res.code;
			description.value = res.description;
		});
	});

	const submit = () => {
		crudStore
			.updateRow(itemId, {
				code: code.value,
				description: description.value,
			})
			.then(() => {
				notyf.success("Modification effectuée avec succès!");
				router.push({ name: "document_types" });
			});
	};
</script>

<template>
	<CreateFormWrapper :col="10" @submit="submit">
		<template #form-head-inner>
			<div class="left">
				<h3>Modification</h3>
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
						Enregistrer
					</VButton>
				</div>
			</div>
		</template>
		<template #form-body>
			<div class="columns is-centered pt-2">
				<div class="column is-8">
					<VField label="Code" horizontal>
						<VControl fullwidth :errors="errors.code || []">
							<VInput v-model="code" type="text" placeholder="Code" name="label" required />
						</VControl>
					</VField>
					<VField label="Description" horizontal>
						<VControl fullwidth :errors="errors.description || []">
							<VTextarea
								v-model="description"
								type="number"
								placeholder="Description"
								name="description"
							/>
						</VControl>
					</VField>
				</div>
			</div>
		</template>
	</CreateFormWrapper>
</template>
