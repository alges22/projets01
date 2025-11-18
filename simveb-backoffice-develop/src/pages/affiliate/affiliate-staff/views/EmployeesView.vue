<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";
	import dayjs from "dayjs";
	import { useAffiliateStore } from "/@src/stores/modules/affiliate";
	import { storeToRefs } from "pinia";
	import { useNotyf } from "/@src/composable/useNotyf";
	import { useI18n } from "vue-i18n";

	export interface AffiliateEmployeeViewProps {
		employees: [];
		zones: [];
		positions: [];
	}

	withDefaults(defineProps<AffiliateEmployeeViewProps>(), {
		employees: undefined,
		zones: undefined,
		positions: undefined,
	});

	const emit = defineEmits(["onInvitationSent"]);

	const { can } = userHasPermissions();
	const notyf = useNotyf();
	const affiliateStore = useAffiliateStore();
	const { invitationLoading } = storeToRefs(affiliateStore);

	const openInvitationModal = ref(false);

	const invitation = ref({
		type: "member",
		email: null,
		position_id: null,
		firstname: null,
		lastname: null,
		telephone: null,
		zones: [],
	});

	const resetInvitation = () => {
		invitation.value = {
			type: "member",
			email: null,
			position_id: null,
			firstname: null,
			lastname: null,
			telephone: null,
			zones: [],
		};
	};

	const sendInvitation = () => {
		affiliateStore.inviteMember(invitation.value).then(() => {
			openInvitationModal.value = false;
			emit("onInvitationSent");
			resetInvitation();
			notyf.success(t("invitation.creation.success"));
		});
	};
	const defaultImgPath = "/images/avatars/svg/vuero-1.svg";
</script>

<template>
	<div>
		<div v-if="can('invite-affiliate-staff-employee')" class="is-flex is-justify-content-end mb-3">
			<VButton
				:loading="invitationLoading"
				color="primary"
				icon="mail"
				raised
				rounded
				@click="openInvitationModal = true"
			>
				Inviter
			</VButton>
		</div>
		<div class="tab-details-inner">
			<div class="user-grid user-grid-v2">
				<VPlaceholderPage
					:class="[employees.length !== 0 && 'is-hidden']"
					title="Aucun résultat."
					subtitle="Dommage. Il semble que nous n'ayons aucune donnée à ce niveau."
					larger
				>
					<template #image>
						<img class="light-image" src="/@src/assets/illustrations/placeholders/search-4.svg" alt="" />
						<img
							class="dark-image"
							src="/@src/assets/illustrations/placeholders/search-4-dark.svg"
							alt=""
						/>
					</template>
				</VPlaceholderPage>

				<TransitionGroup name="list" tag="div" class="columns is-multiline">
					<div v-for="item in employees" :key="item.id" class="column is-3">
						<div class="grid-item-wrap">
							<div class="grid-item-head">
								<div class="flex-head">
									<div class="meta">
										<span v-if="item.invitation_status === 'pending'" class="dark-inverted">
											Invitation en attente
										</span>
										<span v-if="item.invitation_status === 'confirmed'" class="dark-inverted">
											Invitation confirmée
										</span>
										<span v-if="item.invitation_status === 'blocked'" class="dark-inverted">
											Invitation bloquée
										</span>
										<span class="is-size-7"
											>Créé le {{ dayjs(item.created_at).format("DD-MM-YYYY H:m") }}</span
										>
									</div>
									<div v-if="item.invitation_status === 'confirmed'" class="status-icon is-success">
										<i aria-hidden="true" class="fas fa-check"></i>
									</div>
									<div
										v-if="item.invitation_status === 'pending'"
										class="status-icon has-background-secondary has-text-white"
									>
										<i aria-hidden="true" class="fas fa-exclamation"></i>
									</div>
									<div v-if="item.invitation_status === 'blocked'" class="status-icon is-danger">
										<i aria-hidden="true" class="fas fa-times"></i>
									</div>
								</div>
							</div>
							<div class="grid-item">
								<VAvatar
									:picture="item.identity.picture ? item.identity.picture : defaultImgPath"
									size="big"
								/>
								<h3 class="dark-inverted">
									{{ item.identity.fullName }}
								</h3>
								<p>{{ item.position.name }}</p>
								<!-- zones -->
								<div class="is-size-84" style="word-break: break-all">
									<div class="columns m-0">
										<div class="column is-5 has-text-left">
											<span class="has-text-primary">NPI</span>
										</div>
										<div class="column is-7 has-text-right">
											<span class="has-text-light">{{ item.identity.npi }}</span>
										</div>
									</div>
									<div class="columns m-0">
										<div class="column is-5 has-text-left">
											<span class="has-text-primary">Téléphone</span>
										</div>
										<div class="column is-7 has-text-right">
											<span class="has-text-light">{{ item.identity.telephone }}</span>
										</div>
									</div>
									<div class="columns m-0">
										<div class="column is-5 has-text-left">
											<span class="has-text-primary">Email</span>
										</div>
										<div class="column is-7 has-text-right">
											<span class="has-text-light">{{ item.identity.email }}</span>
										</div>
									</div>
									<div class="columns m-0">
										<div class="column is-5 has-text-left">
											<span class="has-text-primary">IFU</span>
										</div>
										<div class="column is-7 has-text-right">
											<span class="has-text-light">{{ item.identity.ifu }}</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</TransitionGroup>
			</div>
		</div>
		<VModal
			v-if="can('invite-affiliate-staff-employee')"
			:open="openInvitationModal"
			title="Inviter un employé"
			actions="right"
			@close="openInvitationModal = false"
		>
			<template #content>
				<form class="modal-form">
					<div class="columns is-multiline">
						<div class="column is-6">
							<div class="field">
								<label>Nom</label>
								<div class="control">
									<input
										v-model="invitation.lastname"
										type="text"
										class="input"
										placeholder="Nom"
										required
										name="lastname"
									/>
								</div>
							</div>
						</div>
						<div class="column is-6">
							<div class="field">
								<label>Prénoms</label>
								<div class="control">
									<input
										v-model="invitation.firstname"
										type="text"
										class="input"
										placeholder="Prénoms"
										name="firstname"
										required
									/>
								</div>
							</div>
						</div>
						<div class="column is-12">
							<div class="field">
								<label>Email</label>
								<div class="control">
									<input
										v-model="invitation.email"
										type="email"
										class="input"
										placeholder="mail@comp.com"
										name="email"
										required
									/>
								</div>
							</div>
						</div>
						<div class="column is-12">
							<div class="field">
								<label>Téléphone</label>
								<div class="control">
									<input
										v-model="invitation.telephone"
										type="text"
										class="input"
										placeholder="51000000"
										name="telephone"
										required
									/>
								</div>
							</div>
						</div>
						<div class="column is-12">
							<div class="field">
								<label>Poste</label>
								<div class="control">
									<v-select
										v-model="invitation.position_id"
										:options="positions"
										label="name"
										:reduce="(item) => item.id"
										required
										name="position_id"
									>
									</v-select>
								</div>
							</div>
						</div>
						<div class="column is-12">
							<div class="field">
								<label>Zones</label>
								<div class="control">
									<v-select
										v-model="invitation.zones"
										:options="zones"
										label="name"
										:reduce="(item) => item.id"
										required
										multiple
										name="zones[]"
									>
									</v-select>
								</div>
							</div>
						</div>
					</div>
				</form>
			</template>
			<template #action>
				<VButton color="primary" raised :loading="invitationLoading" @click="sendInvitation">Envoyer</VButton>
			</template>
		</VModal>
	</div>
</template>
