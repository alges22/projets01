<template>
	<div v-if="can('invite-space-staff')" class="intro-y flex flex-col sm:flex-staff items-center mt-4">
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<button class="btn btn-outline-primary shadow-md mr-2" @click="modalIsOpen = true">
				Ajouter un membre
			</button>
		</div>
	</div>

	<div class="grid grid-cols-12 gap-6 !rounded-none mt-4">
		<DataTable
			:headers="headers"
			:items="staffs"
			:loading="loading"
			:meta="meta"
			:create-button="false"
			empty-text="Aucun membre trouvé"
			header-class="uppercase text-start"
			search
		>
			<template #search>
				<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
					<button class="border-0 mr-2">
						<i class="w-4 h-4 fa-light fa-ellipsis text-2xl"></i>
					</button>
				</div>
			</template>
			<template #member="{ item }">
				<div class="flex items-center">
					<div class="w-24 h-24 lg:w-12 lg:h-12 image-fit lg:mr-1">
						<!--						<img alt="Profile" class="rounded-full" src="@/assets/images/preview-14.jpg" />-->
						<AvatarComponent
							size="lg"
							:lastname="item.identity.lastname"
							:firstname="item.identity.firstname"
						/>
					</div>
					<div class="ml-3">
						<span class="font-medium dark:text-gray-900">{{ item.identity.fullName }}</span>
						<div class="text-slate text-xs mt-0.5">{{ item.identity.npi }}</div>
					</div>
				</div>
			</template>
			<template #role="{ item }">
				<div class="font-bold text-gray-600">
					<template v-for="(role, index) in item.roles">
						{{ role.label }}{{ index < item.roles.length - 1 ? ", " : "" }}
					</template>
				</div>
			</template>
			<template #actions="{ item: user }">
				<button
					v-if="can('update-space-staff') || can('deactivate-space-staff')"
					v-tooltip
					class="flex items-center me-3 btn rounded-full"
					:class="user.suspended ? 'btn-outline-success' : 'btn-outline-danger'"
					:title="user.suspended ? 'Ré-affecter' : 'Suspendre'"
					@click="toggleStatus(user)"
				>
					<i class="fa-light w-4 h-4" :class="user.suspended ? 'fa-user-check' : 'fa-user-xmark'" />
				</button>
				<button
					v-if="can('affect-police-officer')"
					v-tooltip
					class="flex items-center me-3 btn rounded-full btn-outline-success"
					title="Affecter à une frontière"
					@click="affectUser(user)"
				>
					<i class="fa-light w-4 h-4 fa-arrow-alt-circle-right" />
				</button>
			</template>
		</DataTable>
	</div>

	<InvitationForm
		v-if="can('invite-space-staff')"
		:open="modalIsOpen"
		@close="modalIsOpen = false"
		@submit="modalIsOpen = false"
	/>

	<AffectationForm
		v-if="can('affect-police-officer')"
		:open="affectationModal"
		:npi="selectedNpi"
		@close="affectationModal = false"
		@submit="successModalIsOpen = true"
	/>

	<SuccessModalComponent :open="successModalIsOpen" @close="successModalIsOpen = false" />
</template>

<script setup>
	import DataTable from "@/components/DataTable.vue";
	import { useStaffStore } from "@/stores/staff.js";
	import { storeToRefs } from "pinia";
	import { ref, watch } from "vue";
	import SuccessModalComponent from "@/components/SuccessModalComponent.vue";
	import AvatarComponent from "@/components/AvatarComponent.vue";
	import Alert from "@/components/notification/alert.js";
	import AffectationForm from "@/views/Staff/AffectationForm.vue";
	import InvitationForm from "@/views/Staff/InvitationForm.vue";
	import { userHasPermissions } from "@/helpers/permissions.js";
	import { useDeleteConfirmation } from "@/composables/useDeleteConfirmation.js";

	const staffStore = useStaffStore();
	const { staffs, loading, meta } = storeToRefs(staffStore);
	const { can } = userHasPermissions();
	const options = ref({});
	const successModalIsOpen = ref(false);
	const modalIsOpen = ref(false);
	const affectationModal = ref(false);
	const selectedNpi = ref(null);

	const headers = [
		{ key: "member", title: "Membre", sortable: false },
		{ key: "role", title: "Rôle", sortable: false },
		{ key: "last_login", title: "Dernière connexion", sortable: false },
	];

	const fetchStaffs = async (metadata) => {
		await staffStore.fetchStaffs(metadata);
	};

	const { handleDelete: toggleStatus } = useDeleteConfirmation(
		async (item) => {
			await staffStore.suspendStaff(item.id).then(() => {
				Alert.success(item.suspended ? "Ré-affectation réussie" : "Suspendu avec succès");
				item.suspended = !item.suspended;
			});
		},
		"Êtes vous sûr ?",
		null,
		"Oui, je suis sûr"
	);

	const affectUser = (item) => {
		selectedNpi.value = item.identity.npi;
		affectationModal.value = true;
	};

	watch(
		options,
		(newOptions) => {
			fetchStaffs(newOptions);
		},
		{ deep: true, immediate: true }
	);
</script>
