<script setup lang="ts">
	import dayjs from "dayjs";
	import { userHasPermissions } from "/@src/utils/permission";

	export interface AffiliateInformationViewProps {
		affiliate: {};
		mainLeader: {};
	}

	withDefaults(defineProps<AffiliateInformationViewProps>(), {
		affiliate: undefined,
		mainLeader: undefined,
	});

	const { can } = userHasPermissions();
</script>

<template>
	<div v-if="affiliate" class="columns tab-details-inner">
		<div class="column is-8">
			<VCard class="side-card">
				<div class="card-head">
					<h4>Information sur l'affilié</h4>
				</div>
				<div class="card-inner is-size-6-5">
					<div class="columns">
						<div class="column is-one-quarter">
							<span>Raison Social / Nom de l'entreprise</span>
						</div>
						<div class="column">
							<span>{{ affiliate.social_reason }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-one-quarter">
							<span>Type de profile</span>
						</div>
						<div class="column">
							<span class="has-text-weight-bold">
								<VTag :label="affiliate.profile_type?.name" color="primary" />
							</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-one-quarter">
							<span>Email</span>
						</div>
						<div class="column">
							<span>{{ affiliate.email }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-one-quarter">
							<span>Téléphone</span>
						</div>
						<div class="column">
							<span>{{ affiliate.telephone }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-one-quarter">
							<span>IFU</span>
						</div>
						<div class="column">
							<span>{{ affiliate.ifu }}</span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-one-quarter">
							<span>RCCM</span>
						</div>
						<div class="column">
							<span>{{ affiliate.rccm }}</span>
						</div>
					</div>
					<!--					<div class="columns">
						<div class="column is-one-quarter">
							<span>Dirigeant</span>
						</div>
						<div class="column">
							<span>
								{{ `${mainLeader.identity?.fullName} - ${mainLeader.position?.name}` }}
							</span>
						</div>
					</div>-->
					<div class="columns">
						<div class="column is-one-quarter">
							<span>Date de création</span>
						</div>
						<div class="column">
							<span>
								{{ dayjs(affiliate.created_at).format("DD-MM-YYYY : hh:mm") }}
							</span>
						</div>
					</div>
					<div class="columns is-centered">
						<div class="column is-half">
							<RouterLink
								class="button is-primary"
								v-if="can('show-affiliate-registration-request')"
								:to="`/admin/affiliate-registration-requests/${affiliate.registration_request_id}`"
							>
								Voir la demande d'enregistrement
							</RouterLink>
						</div>
					</div>
				</div>
			</VCard>
		</div>
	</div>
</template>
