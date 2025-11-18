<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useNotyf } from "/@src/composable/useNotyf";

	const route = useRoute();
	const router = useRouter();
	const notyf = useNotyf();
	const crudStore = useCrudStore();
	const { url, formLoading, errors } = storeToRefs(crudStore);

	const itemId = route.params.id;
	const item = ref({
		name: null,
		description: null,
		parent_id: null,
		is_interpol: 0,
		responsible_id: null,
	});
	const organizations = ref([]);
	const profiles = ref([]);

	onBeforeMount(() => {
		url.value = "/organizations";
	});

	onMounted(() => {
		if (itemId) {
			crudStore.loadEditData(itemId).then((res) => {
				item.value = {
					name: res.organization.name,
					description: res.organization.description,
					parent_id: res.organization.parent_id,
					is_interpol: res.organization.is_interpol,
					responsible_id: res.organization.responsible?.identity.user.id,
				};

				organizations.value = res.organizations;
				profiles.value = res.profiles;
			});
		} else {
			crudStore.loadCreateData().then((res) => {
				organizations.value = res.organizations;
				profiles.value = res.profiles;
			});
		}
	});

	const submit = () => {
		if (itemId) {
			crudStore.updateRow(itemId, item.value).then(() => {
				notyf.success("Organisation modifié avec succès");
				router.push({ name: "organizations" });
			});
		} else {
			item.value.is_interpol = item.value.is_interpol ? 1 : 0;
			crudStore.createRow(item.value).then(() => {
				notyf.success("L'organisation a bien été crée");
				router.push({ name: "organizations" });
			});
		}
	};
</script>

<template>
	<CreateFormWrapper :col="10" @submit="submit">
		<template #form-head-inner>
			<div class="left">
				<h3>Organisation</h3>
				<p>{{ itemId ? "Modification" : "Création" }}</p>
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
					<VField label="Nom" horizontal>
						<VControl fullwidth :errors="errors.name || []">
							<VInput v-model="item.name" name="name" placeholder="Nom" required />
						</VControl>
					</VField>
					<VField label="Description" horizontal>
						<VControl fullwidth :errors="errors.description || []">
							<VTextarea v-model="item.description" name="description" required></VTextarea>
						</VControl>
					</VField>
					<VField label="Organisation parente" horizontal>
						<VControl fullwidth :errors="errors.parent_id || []">
							<v-select
								v-model="item.parent_id"
								:options="organizations"
								label="name"
								:reduce="(item) => item.id"
							></v-select>
						</VControl>
					</VField>
					<VField label="Responsable" horizontal>
						<VControl fullwidth :errors="errors.responsible_id || []">
							<v-select
								v-model="item.responsible_id"
								:options="profiles"
								label="name"
								:reduce="(item) => item.id"
							></v-select>
						</VControl>
					</VField>
				</div>
			</div>
		</template>
	</CreateFormWrapper>
</template>
