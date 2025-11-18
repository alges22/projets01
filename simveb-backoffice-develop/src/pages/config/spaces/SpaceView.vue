<template>
	<div class="page-content-inner">
		<div class="bg-white rounded-lg mb-4 p-4 flex items-center justify-between space-x-4">
			<div class="text-primary-dark text-2xl font-bold">
				Espace
				<span v-if="space" class="uppercase">{{ space.type_label }} - {{ space.institution?.name }}</span>
			</div>
			<div>
				<VButton
					v-if="!space?.has_suspension && space.status === 'active' && can('store-space-suspension-request')"
					class="me-2"
					color="warning"
					size="medium"
					raised
					@click="requestSuspension"
				>
					<i class="fa fa-pause" aria-hidden="true"></i>
					Suspendre
				</VButton>
				<VButton
					v-if="
						!space?.has_lifting &&
						space.status === 'suspended' &&
						can('store-space-suspension-lifting-request')
					"
					class="me-2"
					color="primary"
					size="medium"
					raised
					@click="requestLifting"
				>
					Levée de suspension
				</VButton>
			</div>
		</div>

		<AlertComponent
			v-if="space?.has_suspension"
			class="border-l-4 mb-4 rounded-md"
			icon="exclamation-triangle"
			color="yellow"
		>
			<p class="text-lg text-yellow-700">
				Une demande de suspension est en attente pour cet espace.
				{{ " " }}
				<button
					v-if="can('show-space-suspension-request')"
					class="font-medium text-yellow-700 underline hover:text-yellow-600"
					@click="openSuspensionModal(space?.current_suspension.id)"
				>
					Veuillez l'examiner.
				</button>
			</p>
		</AlertComponent>

		<AlertComponent
			v-if="space?.status === 'suspended' && !space?.has_lifting"
			class="border-l-4 mb-4 rounded-md"
			icon="exclamation-circle"
			color="danger"
		>
			<p class="text-lg text-danger-700">
				Cet espace est suspendu.
				{{ " " }}
				<button
					v-if="can('store-space-suspension-lifting-request')"
					class="font-medium text-danger-700 underline hover:text-danger-600"
					@click="requestLifting"
				>
					Souhaitez-vous faire une demande de levée de suspension.
				</button>
			</p>
		</AlertComponent>

		<AlertComponent
			v-if="space?.has_lifting"
			class="border-l-4 mb-4 rounded-md"
			icon="exclamation-triangle"
			color="yellow"
		>
			<p class="text-lg text-yellow-700">
				Une demande de levée de suspension est en attente pour cet espace.
				{{ " " }}
				<button
					v-if="can('show-space-suspension-lifting-request')"
					class="font-medium text-yellow-700 underline hover:text-yellow-600"
					@click="openLiftingModal(space?.current_lifting.id)"
				>
					Veuillez l'examiner.
				</button>
			</p>
		</AlertComponent>

		<VLoader size="large" :active="loading">
			<div class="grid grid-cols-2 xl:grid-cols-3 gap-4 tab-details-inner">
				<VCard class="side-card">
					<div class="card-head">
						<h3 class="font-bold text-light">Détails</h3>
					</div>
					<div class="card-inner is-one-third">
						<div class="columns">
							<div class="column">
								<span>Libellé</span>
							</div>
							<div class="column">
								<span>{{ space.type_label }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Statut</span>
							</div>
							<div class="column is-flex is-justify-content-space-between">
								<VTag :label="space.status_label" :color="statusColor(space?.status)" />
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Institution</span>
							</div>
							<div class="column">
								<span>{{ space.institution?.name }}</span>
							</div>
						</div>
						<div v-if="space.printer" class="columns">
							<div class="column">
								<span>Profile associé :</span>
							</div>
							<div class="column">
								<span>{{ space.profile_type.name }}</span>
							</div>
						</div>
					</div>
					<div class="card-head bg-primary-100 mt-8">
						<h3 class="!text-primary-dark">Pièces jointes</h3>
					</div>
					<div class="card-inner is-one-third">
						<FileList :files="space.files" />
					</div>
				</VCard>

				<VCard class="side-card">
					<div class="card-head">
						<h3 class="font-bold text-light">Profiles / Utilisateurs</h3>
					</div>
					<div class="card-inner is-one-third">
						<div v-for="(profile, index) in space.profiles" :key="index" class="columns">
							<div class="column">
								<a href="#">{{ profile.user.identity.fullName }}</a>
							</div>
							<div class="column">
								<KeyField to="#" :value="profile.user.identity.npi" />
							</div>
						</div>
					</div>
				</VCard>

				<VCard class="side-card">
					<div class="card-head">
						<h3 class="font-bold text-light">Thème</h3>
					</div>
					<div class="card-inner is-one-third">
						<div v-if="space.template === 'focus'">
							<img
								class="w-full h-full object-cover rounded-md"
								src="@/assets/templates/focus.webp"
								alt="vehicle"
							/>
							<span class="font-bold flex items-center justify-center mt-2 text-primary-dark text-xl">
								FOCUS
							</span>
						</div>
						<div v-else-if="space.template === 'vivid'">
							<img
								class="w-full h-full object-cover rounded-md"
								src="@/assets/templates/vivid.webp"
								alt="vehicle"
							/>
							<span class="font-bold flex items-center justify-center mt-2 text-primary-dark text-xl">
								VIVID
							</span>
						</div>
						<div v-else-if="space.template === 'default'">
							<img
								class="w-full h-full object-cover rounded-md"
								src="@/assets/templates/serenity.webp"
								alt="vehicle"
							/>
							<span class="font-bold flex items-center justify-center mt-2 text-primary-dark text-xl">
								SERENITY
							</span>
						</div>

						<VButton
							v-if="can('update-space')"
							class="mt-2"
							color="success"
							size="medium"
							raised
							@click="templateModal = true"
						>
							Changer de thème
						</VButton>
					</div>
				</VCard>

				<VCard class="side-card">
					<div class="card-head">
						<h3 class="font-bold text-light">Historique de suspensions</h3>
					</div>
					<div
						v-for="(request, index) in space.suspension_requests"
						:key="index"
						class="bg-white rounded shadow w-full max-w-sm my-4"
					>
						<template v-if="request.status == 'validated'">
							<div class="bg-green-100 p-2 rounded-t flex justify-between">
								<span class="text-green-600 font-medium">{{ request.author.identity.fullName }}</span>
								<span class="text-green-600">{{ request.created_at }}</span>
							</div>
						</template>
						<template v-else-if="request.status == 'pending'">
							<div class="bg-yellow-100 p-2 rounded-t flex justify-between">
								<span class="text-yellow-600 font-medium">{{ request.author.identity.fullName }}</span>
								<span class="text-yellow-600">{{ request.created_at }}</span>
							</div>
						</template>
						<template v-else-if="request.status == 'rejected'">
							<div class="bg-danger-100 p-2 rounded-t flex justify-between">
								<span class="text-danger-600 font-medium">{{ request.author.identity.fullName }}</span>
								<span class="text-danger-600">{{ request.created_at }}</span>
							</div>
						</template>
						<div class="bg-white p-4 rounded-b">
							<span class="text-gray-500">
								Ref
								<button
									v-if="can('show-space-suspension-request')"
									class="font-bold text-primary hover:underline"
									@click="openSuspensionModal(request.id)"
								>
									# {{ request.reference }}
								</button>
								<span v-else class="font-bold text-primary"># {{ request.reference }} </span>
							</span>
						</div>
					</div>
				</VCard>

				<VCard class="side-card">
					<div class="card-head">
						<h3 class="font-bold text-light">Levée de suspensions</h3>
					</div>
					<div
						v-for="(request, index) in space.suspension_lifting_requests"
						:key="index"
						class="bg-white rounded shadow w-full max-w-sm my-4"
					>
						<template v-if="request.status == 'validated'">
							<div class="bg-green-100 p-2 rounded-t flex justify-between">
								<span class="text-green-600 font-medium">{{ request.author.identity.fullName }}</span>
								<span class="text-green-600">{{ request.created_at }}</span>
							</div>
						</template>
						<template v-else-if="request.status == 'pending'">
							<div class="bg-yellow-100 p-2 rounded-t flex justify-between">
								<span class="text-yellow-600 font-medium">{{ request.author.identity.fullName }}</span>
								<span class="text-yellow-600">{{ request.created_at }}</span>
							</div>
						</template>
						<template v-else-if="request.status == 'rejected'">
							<div class="bg-danger-100 p-2 rounded-t flex justify-between">
								<span class="text-danger-600 font-medium">{{ request.author.identity.fullName }}</span>
								<span class="text-danger-600">{{ request.created_at }}</span>
							</div>
						</template>
						<div class="bg-white p-4 rounded-b">
							<span class="text-gray-500">
								Ref
								<button
									v-if="can('show-space-suspension-lifting-request')"
									class="font-bold text-primary hover:underline"
									@click="openLiftingModal(request.id)"
								>
									# {{ request.reference }}
								</button>
								<span v-else class="font-bold text-primary"># {{ request.reference }}</span>
							</span>
						</div>
					</div>
				</VCard>
			</div>
		</VLoader>

		<TemplateUpdateForm
			v-if="can('update-space') && space"
			:open="templateModal"
			:space="space"
			@close="templateModal = false"
			@submit="templateModal = false"
		/>

		<SuspensionModal
			v-if="can('show-space-suspension-request') && space"
			:suspension-id="currentSuspensionId"
			:open="suspensionModal"
			@close="closeSuspensionModal"
			@submit="
				() => {
					suspensionModal = false;
					fetchSpace();
				}
			"
		/>

		<SuspensionLiftingModal
			v-if="can('show-space-suspension-lifting-request') && space"
			:lifting-id="currentLiftingId"
			:open="liftingModal"
			@close="closeLiftingModal"
			@submit="
				() => {
					liftingModal = false;
					fetchSpace();
				}
			"
		/>
	</div>
</template>

<script setup lang="ts">
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { storeToRefs } from "pinia";
	import statusColor from "/@src/utils/status-color";
	import FileList from "/@src/pages/demands/Cards/FileList.vue";
	import TemplateUpdateForm from "/@src/pages/config/spaces/TemplateUpdateForm.vue";
	import Swal from "sweetalert2";
	import SuspensionModal from "/@src/pages/config/spaces/SuspensionModal.vue";
	import { userHasPermissions } from "/@src/utils/permission";
	import AlertComponent from "/@src/components/AlertComponent.vue";
	import SuspensionLiftingModal from "/@src/pages/config/spaces/SuspensionLiftingModal.vue";

	const props = defineProps<{
		spaceId: string;
	}>();
	const templateModal = ref(false);
	const suspensionModal = ref(false);
	const liftingModal = ref(false);
	const currentSuspensionId = ref(null);
	const currentLiftingId = ref(null);

	const crudStore = useCrudStore();
	const { row: space, url, loading } = storeToRefs(crudStore);
	const { can } = userHasPermissions();

	const requestSuspension = async () => {
		await Swal.fire({
			title: "Êtes-vous sûr de vouloir suspendre cet espace ?",
			text: "La suspension ne sera prise en compte qu'après validation",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Oui, Je confirme",
			cancelButtonText: "Annuler",
			input: "textarea",
			inputPlaceholder: "Raison de la suspension",
			inputValidator: (value) => {
				if (!value) {
					return "Vous devez fournir une raison pour la suspension";
				}
			},
		}).then(async (result) => {
			if (result.isConfirmed) {
				url.value = "space-suspension-requests";
				await crudStore.createRow({ space_id: space.value.id, reason: result.value }).then((res) => {
					if (res) {
						Swal.fire("Enregistré", "La requête de suspension a bien été prise en compte", "success");
						fetchSpace();
					}
				});
			}
		});
	};

	const requestLifting = async () => {
		await Swal.fire({
			title: "Demander la levée de suspension de cet espace !",
			text: "L'espace ne sera actif qu'après validation",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Oui, Je confirme",
			cancelButtonText: "Annuler",
			input: "textarea",
			inputPlaceholder: "Merci de fournir une raison pour la levée de suspension",
		}).then(async (result) => {
			if (result.isConfirmed) {
				url.value = "space-suspension-lifting-requests";
				await crudStore.createRow({ space_id: space.value.id, reason: result.value }).then((res) => {
					if (res) {
						Swal.fire(
							"Enregistré",
							"La demande de levée de suspension a bien été prise en compte",
							"success"
						);
						fetchSpace();
					}
				});
			}
		});
	};

	const fetchSpace = async () => {
		url.value = "spaces";
		await crudStore.fetchRow(props.spaceId).then();
	};

	const openSuspensionModal = (suspensionId: string) => {
		currentSuspensionId.value = suspensionId;
		suspensionModal.value = true;
	};

	const closeSuspensionModal = () => {
		suspensionModal.value = false;
		currentSuspensionId.value = null;
	};

	const openLiftingModal = (liftingId: string) => {
		currentLiftingId.value = liftingId;
		liftingModal.value = true;
	};

	const closeLiftingModal = () => {
		liftingModal.value = false;
		currentLiftingId.value = null;
	};

	// HOOK

	onBeforeMount(() => {
		loading.value = true;
		url.value = "spaces";
	});

	onMounted(async () => {
		await fetchSpace();
	});

	onUnmounted(() => {
		space.value = null;
	});
</script>
