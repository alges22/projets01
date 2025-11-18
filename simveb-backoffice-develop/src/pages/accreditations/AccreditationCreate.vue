<template>
	<div>
		<CreateFormWrapper :col="12" @submit="submit">
			<template #form-head-inner>
				<div class="block m-auto">
					<nav class="-mb-px flex space-x-8 m-auto" aria-label="Tabs">
						<button
							v-for="(step, id, index) in stepper.steps.value"
							:key="index"
							:class="{
								'text-primary-600 border-primary-500': stepper.isCurrent(id),
								'text-dark hover:text-primary-700 hover:border-primary-500': stepper.isAfter(id),
								'border-transparent text-gray-500 ': stepper.isBefore(id),
								'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg': true,
							}"
							:disabled="stepper.isCurrent(id) || stepper.isBefore(id)"
							@click="stepper.goTo(id)"
						>
							{{ step.title }}
						</button>
					</nav>
				</div>
			</template>
			<template #form-body>
				<SearchProfile v-if="stepper.isCurrent('search')" @next="stepper.goTo('roles')" />
				<RoleListe
					v-else-if="stepper.isCurrent('roles')"
					@next="stepper.goTo('permissions')"
					@prev="stepper.goTo('permissions')"
				/>
				<PermissionList
					v-else-if="stepper.isCurrent('permissions')"
					@prev="stepper.goTo('roles')"
					@next="submit"
				/>
			</template>
		</CreateFormWrapper>
	</div>
</template>

<script setup>
	import { storeToRefs } from "pinia";
	import { useNotyf } from "/@src/composable/useNotyf";
	import SearchProfile from "/@src/pages/accreditations/steps/SearchProfile.vue";
	import RoleListe from "/@src/pages/accreditations/steps/RoleListe.vue";
	import PermissionList from "/@src/pages/accreditations/steps/PermissionList.vue";
	import { useCrudStore } from "/@src/stores/modules/crud";

	const router = useRouter();
	const crudStore = useCrudStore();
	const { formData, url, row } = storeToRefs(crudStore);

	const stepper = useStepper({
		search: {
			title: "Type de profil",
		},
		roles: {
			title: "Choix des rôles",
		},
		permissions: {
			title: "Liste des permissions",
		},
	});

	const notyf = useNotyf();

	const submit = () => {
		url.value = "/accreditations";
		crudStore.createRow(row.value).then(() => {
			notyf.success("Demande d'accréditation créer avec success");
			router.back();
		});
	};

	onMounted(() => {
		formData.value = {};
	});
</script>

<style lang="scss" scoped></style>
