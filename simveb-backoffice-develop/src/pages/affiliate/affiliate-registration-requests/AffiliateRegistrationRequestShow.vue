<script setup lang="ts">
	import { useI18n } from "vue-i18n";
	import { storeToRefs } from "pinia";
	import { onBeforeMount } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";
	import { useAffiliateStore } from "/@src/stores/modules/affiliate";
	import { useNotyf } from "/@src/composable/useNotyf";
	import { userHasPermissions } from "/@src/utils/permission";
	import { returnPreviousPage } from "/@src/utils/helpers";
	import dayjs from "dayjs";
	import statusColor from "/@src/utils/status-color";

	const route = useRoute();
	const notyf = useNotyf();
	const { can } = userHasPermissions();
	const crudStore = useCrudStore();
	const affiliateStore = useAffiliateStore();
	const { url, loading } = storeToRefs(crudStore);
	const { validateLoading, rejectLoading } = storeToRefs(affiliateStore);

	const files = ref([]);
	const openValidateModal = ref(false);
	const openRejectModal = ref(false);
	const reject_reason = ref("");
	const item = ref(null);

	onBeforeMount(() => {
		url.value = "/affiliate-registration-requests";
		loading.value = true;
	});

	const validate = () => {
		openValidateModal.value = false;
		affiliateStore.validateRequest(item.value.id).then((res) => {
			notyf.success(t("message.validate.success"));
			crudStore.fetchRow(item.value.id).then((res) => {
				item.value.status = res.status;
				item.value.validated_by = res.validated_by?.identity?.fullName;
				item.value.validated_at = res.validated_at;
			});
		});
	};
	const reject = () => {
		openRejectModal.value = false;
		affiliateStore.rejectRequest(item.value.id, { reason: reject_reason.value }).then((res) => {
			notyf.success(t("message.reject.success"));
			crudStore.fetchRow(item.value.id).then((res) => {
				item.value.status = res.status;
				item.value.rejected_by = res.rejected_by?.identity?.fullName;
				item.value.rejected_at = res.rejected_at;
				item.value.reject_reason = res.reject_reason;
			});
		});
	};

	onMounted(() => {
		crudStore.fetchRow(route.params.id).then((res) => {
			item.value = res;
		});
	});
</script>
<template>
	<div class="page-content-inner">
		<div class="mb-3">
			<VIconButton icon="arrow-left" circle outlined color="primary" @click="returnPreviousPage($router)" />
		</div>
		<template v-if="!loading">
			<div class="columns">
				<div class="column is-3 is-size-6">
					<div class="is-flex is-justify-content-space-between">
						<div>Statut de la demande</div>
						<div class="has-text-weight-bold">
							<VTag :color="statusColor(item.status)" :label="t('status.' + item.status)" />
						</div>
					</div>
				</div>
			</div>
			<div v-if="item.status == 'pending'" class="is-flex is-justify-content-flex-end">
				<VButton
					v-if="can('validate-affiliate-registration-request')"
					color="primary"
					raised
					:loading="validateLoading"
					@click="openValidateModal = true"
				>
					Valider
				</VButton>
				<VButton
					v-if="can('reject-affiliate-registration-request')"
					class="ml-3"
					color="danger"
					raised
					:loading="rejectLoading"
					@click="openRejectModal = true"
				>
					Rejeter
				</VButton>
			</div>
			<div class="columns">
				<div class="column is-7">
					<VCard>
						<div class="mb-5">
							<span class="is-uppercase has-text-weight-medium has-text-primary is-size-6">Entité</span>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">Raison Social / Nom de l'entreprise</div>
							<div class="column is-8">
								{{ item.social_reason }}
							</div>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">Type de profile</div>
							<div class="column is-8">
								{{ item.profile_type?.name }}
							</div>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">Adresse</div>
							<div class="column is-8">
								{{ item.seat }}
							</div>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">IFU</div>
							<div class="column is-8">
								{{ item.ifu }}
							</div>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">RCCM</div>
							<div class="column is-8">
								{{ item.rccm }}
							</div>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">Téléphone</div>
							<div class="column is-8">
								{{ item.telephone }}
							</div>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">Email</div>
							<div class="column is-8">
								{{ item.email }}
							</div>
						</div>
						<div v-if="item.creator_name" class="columns">
							<div class="column is-4 has-text-weight-medium">Créé par</div>
							<div class="column is-8">
								{{ item.creator?.identity.fullName }}
							</div>
						</div>
						<div v-if="item.creator_name" class="columns">
							<div class="column is-4 has-text-weight-medium">Créé le</div>
							<div class="column is-8">
								{{ dayjs(item.created_at).format("DD-MM-YYYY : H:mm") }}
							</div>
						</div>
						<div v-if="item.status == 'validated'" class="columns">
							<div class="column is-4 has-text-weight-medium">Validé à</div>
							<div class="column is-8">
								{{ item.validated_at }}
							</div>
						</div>
						<div v-if="item.status == 'validated'" class="columns">
							<div class="column is-4 has-text-weight-medium">Validé par</div>
							<div class="column is-8">
								{{ item.validated_by }}
							</div>
						</div>
						<div v-if="item.status == 'rejected'" class="columns">
							<div class="column is-4 has-text-weight-medium">Rejeté par</div>
							<div class="column is-8">
								{{ item.rejected_at }}
							</div>
						</div>
						<div v-if="item.status == 'rejected'" class="columns">
							<div class="column is-4 has-text-weight-medium">Raison du rejet</div>
							<div class="column is-8">
								{{ item.reject_reason }}
							</div>
						</div>
						<div v-if="item.status == 'rejected'" class="columns">
							<div class="column is-4 has-text-weight-medium">Rejeté par</div>
							<div class="column is-8">
								{{ item.rejected_by }}
							</div>
						</div>
					</VCard>
				</div>
				<!--				<div class="column is-5">
					<VCard>
						<div class="mb-5">
							<span class="is-uppercase has-text-weight-medium has-text-primary is-size-6">
								Dirigeant
							</span>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">Nom complet</div>
							<div class="column is-8">
								{{ item.leader.lastname + " " + item.leader.firstname }}
							</div>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">Email</div>
							<div class="column is-8" style="word-break: break-all">
								{{ item.leader.email }}
							</div>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">Téléphone</div>
							<div class="column is-8">
								{{ item.leader.telephone }}
							</div>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">IFU</div>
							<div class="column is-8">
								{{ item.leader.ifu }}
							</div>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">NPI</div>
							<div class="column is-8">
								{{ item.leader.npi }}
							</div>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">Adresse</div>
							<div class="column is-8">
								{{ item.leader.address }}
							</div>
						</div>
						<div class="columns">
							<div class="column is-4 has-text-weight-medium">Position</div>
							<div class="column is-8">
								{{ item.leader.position_name }}
							</div>
						</div>
					</VCard>
				</div>-->
			</div>
			<div class="columns">
				<div class="column is-6">
					<VCard>
						<div class="mb-5">
							<span class="has-text-weight-medium has-text-primary is-uppercase">Documents</span>
						</div>
						<div v-for="(file, index) in files" class="columns" :key="index">
							<div class="column is-10">{{ file.path.name }}</div>
							<div class="column is-2">
								<a :href="file.url" target="_blank">
									<i class="iconify has-text-primary" data-icon="download" aria-hidden="true"></i>
								</a>
							</div>
						</div>
					</VCard>
				</div>
			</div>
		</template>

		<VPlaceholderSection v-else title="Chargement en cours" />

		<VModal
			v-if="can('validate-affiliate-registration-request')"
			:open="openValidateModal"
			actions="center"
			title="Confirmation"
			@close="openValidateModal = false"
		>
			<template #content>
				<VPlaceholderSection
					title="Êtes vous sûr ?"
					subtitle="Êtes vous sûr de vouloir valider cette demande?"
				/>
			</template>
			<template #action>
				<VButton color="primary" raised :loading="validateLoading" @click="validate"> Confirmer </VButton>
			</template>
		</VModal>

		<VModal
			v-if="can('reject-affiliate-registration-request')"
			:open="openRejectModal"
			actions="center"
			title="Êtes vous sûr de vouloir rejeter cette demande?"
			@close="openRejectModal = false"
		>
			<template #content>
				<VField label="Raison" horizontal>
					<VControl fullwidth>
						<VTextarea v-model="reject_reason" name="reject_reason" placeholder="Raison" required />
					</VControl>
				</VField>
			</template>
			<template #action>
				<VButton color="danger" raised :loading="validateLoading" @click="reject"> Confirmer </VButton>
			</template>
		</VModal>
	</div>
</template>
