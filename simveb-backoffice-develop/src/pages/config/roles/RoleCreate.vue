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
		label: null,
		editable: true,
		permissions: [],
	});

	const itemPermissions = ref([]);
	const modules = ref([]);

	onBeforeMount(() => {
		url.value = "/roles";
	});

	onMounted(() => {
		if (itemId) {
			crudStore.fetchRow(itemId).then((res) => {
				item.value = {
					name: res.name,
					label: res.label,
					editable: res.editable,
				};

				if (res.permissions) {
					res.permissions.forEach((element) => {
						itemPermissions.value.push(element.name);
					});
				}
			});
		}
		crudStore.loadCreateData().then((res) => {
			modules.value = res.modules;
		});
	});

	const submit = () => {
		item.value.permissions = itemPermissions.value;
		if (itemId) {
			crudStore.updateRow(itemId, item.value).then(() => {
				notyf.success("Modification effectuée avec succès!");
				router.push({ name: "roles" });
			});
		} else {
			crudStore.createRow(item.value).then(() => {
				notyf.success("Enregistrement effectué avec succès!");
				router.push({ name: "roles" });
			});
		}
	};
</script>

<template>
	<CreateFormWrapper :col="12" @submit="submit">
		<template #form-head-inner>
			<div class="left">
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
				<div class="column is-10">
					<VField label="Libellé" horizontal>
						<VControl fullwidth :errors="errors.label || []">
							<VInput v-model="item.label" name="label" placeholder="Libellé" required />
						</VControl>
					</VField>
					<VField label="Nom" horizontal>
						<VControl fullwidth :errors="errors.name || []">
							<VInput v-model="item.name" name="name" placeholder="Nom" required />
						</VControl>
					</VField>
					<VField label="Editable" horizontal>
						<VControl fullwidth subcontrol :errors="errors.editable || []">
							<VSwitchSegment
								v-model="item.editable"
								name="editable"
								label-true="Oui"
								label-false="Non"
								color="primary"
							/>
						</VControl>
					</VField>
				</div>
			</div>

			<div class="columns is-multiline is-centered">
				<div class="column is-10">
					<span class="has-text-weight-medium">Permissions</span>
				</div>
				<div class="column is-10">
					<VCollapse :items="modules" with-chevron>
						<template #collapse-item-summary="{ item }">{{ item.name }}</template>
						<template #collapse-item-content="{ item }">
							<div v-for="permission in item.permissions" :key="permission.id" class="columns">
								<div class="column is-6">
									<div class="is-flex is-flex-wrap-wrap is-align-content-center">
										<VAnimatedCheckbox
											v-model="itemPermissions"
											:value="permission.name"
											color="primary"
										/>
										<span class="m-auto">{{ permission.label }}</span>
									</div>
								</div>
							</div>
						</template>
					</VCollapse>
				</div>
			</div>
		</template>
	</CreateFormWrapper>
</template>
