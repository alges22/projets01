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
		npi: null,
		position_id: null,
		center_id: null,
		roles: [],
		organizations: [],
	});

	const positions = ref([]);
	const organizations = ref([]);
	const roles = ref([]);
	const centers = ref([]);

	const dataLoaded = ref(false);

	onBeforeMount(() => {
		url.value = "/staff";
	});

	onMounted(() => {
		if (itemId) {
			crudStore.loadEditData(itemId).then((res) => {
				positions.value = res.positions;
				organizations.value = res.organizations;
				centers.value = res.centers;
				roles.value = res.roles;

				item.value = {
					position_id: res.staff.position_id,
					center_id: res.center_id,
				};

				if (res.staff.services) {
					res.staff.services.forEach((element) => {
						staffServices.value.push(element.id);
					});
				}

				if (res.staff.identity.user.roles) {
					res.staff.identity.user.roles.forEach((element) => {
						roles.value.push(element.id);
					});
				}

				dataLoaded.value = true;
			});
		} else {
			crudStore.loadCreateData().then((res) => {
				positions.value = res.positions;
				organizations.value = res.organizations;
				roles.value = res.roles;
				centers.value = res.centers;

				dataLoaded.value = true;
			});
		}
	});

	const submit = () => {
		if (itemId) {
			crudStore.updateRow(itemId, item.value).then(() => {
				notyf.success("Modification effectuée avec succès!");
				router.push({ name: "staff" });
			});
		} else {
			crudStore.createRow(item.value).then(() => {
				notyf.success("Enregistrement effectué avec succès!");
				router.push({ name: "staff" });
			});
		}
	};
</script>

<template>
	<CreateFormWrapper v-if="dataLoaded" :col="10" @submit="submit">
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
					<VField required label="NPI" horizontal>
						<VControl fullwidth :errors="errors.npi || []">
							<VInput v-model="item.npi" name="npi" placeholder="NPI" required />
						</VControl>
					</VField>
					<VField label="Poste" horizontal required>
						<VControl fullwidth :errors="errors.position_id || []">
							<v-select
								v-model="item.position_id"
								:options="positions"
								label="name"
								:reduce="(item) => item.id"
								required
							></v-select>
						</VControl>
					</VField>
					<VField label="Services" required horizontal>
						<VControl fullwidth :errors="errors.organizations || []">
							<v-select
								v-model="item.organizations"
								multiple
								:options="organizations"
								label="name"
								:reduce="(item) => item.id"
							></v-select>
						</VControl>
					</VField>
					<VField label="Roles" required horizontal>
						<VControl fullwidth :errors="errors.roles || []">
							<v-select
								v-model="item.roles"
								multiple
								:options="roles"
								label="name"
								:reduce="(item) => item.name"
							></v-select>
						</VControl>
					</VField>
					<VField label="Centres de gestions" required horizontal>
						<VControl fullwidth :errors="errors.center_id || []">
							<v-select
								v-model="item.center_id"
								:options="centers"
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
