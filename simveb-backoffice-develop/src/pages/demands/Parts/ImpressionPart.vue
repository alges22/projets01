<template>
	<VCard class="side-card">
		<div class="card-head">
			<h3 class="font-bold text-light">{{ order.type_label }}</h3>
		</div>
		<div class="card-inner is-one-third">
			<div class="columns">
				<div class="column">
					<span>Référence d'impression</span>
				</div>
				<div class="column">
					<KeyField :value="order.reference" />
				</div>
			</div>
			<div class="columns">
				<div class="column">
					<span>Statut</span>
				</div>
				<div class="column is-flex is-justify-content-space-between">
					<VTag :label="order.status_label" :color="statusColor(order?.status)" />
				</div>
			</div>
			<template v-if="order.validated_at">
				<div class="columns">
					<div class="column">
						<span>Validé par :</span>
					</div>
					<div class="column">
						<span>{{ order.validator?.identity.fullName }}</span>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>Validé le :</span>
					</div>
					<div class="column">
						<span>{{ order.validated_at }}</span>
					</div>
				</div>
			</template>
			<template v-if="order.rejected_at">
				<div class="columns">
					<div class="column">
						<span>Rejeté par :</span>
					</div>
					<div class="column">
						<span>{{ order.rejector?.identity.fullName }}</span>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>Rejeté le :</span>
					</div>
					<div class="column">
						<span>{{ order.rejected_at }}</span>
					</div>
				</div>
			</template>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 gap-4 tab-details-inner">
			<div
				v-if="isPlate"
				:class="{
					'col-span-full': order.type !== 'both',
				}"
			>
				<div v-if="order.type === 'both'" class="card-head bg-primary-100 mt-8">
					<h3 class="!text-primary-dark">Plaque</h3>
				</div>

				<div class="card-inner is-one-third">
					<div class="columns">
						<div class="column">
							<span>Statut</span>
						</div>
						<div class="column is-flex is-justify-content-space-between">
							<VTag :label="order.plate_status_label" :color="statusColor(order?.plate_status)" />
						</div>
					</div>
					<div class="columns">
						<div class="column">
							<span>Date d'affectation</span>
						</div>
						<div class="column">
							<span>{{ order.plate_affected_at }}</span>
						</div>
					</div>
					<template v-if="order.plate_printed_at">
						<div class="columns">
							<div class="column">
								<span>Date d'impression</span>
							</div>
							<div class="column">
								<span>{{ order.plate_printed_at }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Imprimé par :</span>
							</div>
							<div class="column">
								<span>{{ order.plate_printer?.identity.fullName }}</span>
							</div>
						</div>
					</template>
					<div v-if="order.received_at" class="columns">
						<div class="column">
							<span>Reçu par le propriétaire le :</span>
						</div>
						<div class="column">
							<span>{{ order.received_at }}</span>
						</div>
					</div>
					<template v-if="order.plate_validated_at">
						<div class="columns">
							<div class="column">
								<span>Validé par :</span>
							</div>
							<div class="column">
								<span>{{ order.plate_validator?.identity.fullName }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Validé le :</span>
							</div>
							<div class="column">
								<span>{{ order.plate_validated_at }}</span>
							</div>
						</div>
					</template>
					<template v-if="order.plate_rejected_at">
						<div class="columns">
							<div class="column">
								<span>Rejeté par :</span>
							</div>
							<div class="column">
								<span>{{ order.plate_rejector?.identity.fullName }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Rejeté le :</span>
							</div>
							<div class="column">
								<span>{{ order.plate_rejected_at }}</span>
							</div>
						</div>
					</template>
					<div class="columns">
						<div class="column">
							<span>Observations :</span>
						</div>
						<div class="column">
							<span>{{ order.plate_observations }}</span>
						</div>
					</div>
				</div>
			</div>

			<div
				v-if="isGrayCard"
				:class="{
					'col-span-full': order.type !== 'both',
				}"
			>
				<div v-if="order.type === 'both'" class="card-head bg-primary-100 mt-8">
					<h3 class="!text-primary-dark">Carte grise</h3>
				</div>
				<div class="card-inner is-one-third">
					<div class="columns">
						<div class="column">
							<span>Statut</span>
						</div>
						<div class="column is-flex is-justify-content-space-between">
							<VTag :label="order.card_status_label" :color="statusColor(order?.card_status)" />
						</div>
					</div>
					<div v-if="order.card_printed_at" class="columns">
						<div class="column">
							<span>Date d'impression</span>
						</div>
						<div class="column">
							<span>{{ order.card_printed_at }}</span>
						</div>
					</div>
					<template v-if="order.card_validator_id">
						<div class="columns">
							<div class="column">
								<span>Validé par :</span>
							</div>
							<div class="column">
								<span>{{ order.card_validator?.identity.fullName }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Validé le :</span>
							</div>
							<div class="column">
								<span>{{ order.validated_at }}</span>
							</div>
						</div>
					</template>
					<template v-if="order.card_rejected_at">
						<div class="columns">
							<div class="column">
								<span>Rejeté par :</span>
							</div>
							<div class="column">
								<span>{{ order.card_rejector?.identity.fullName }}</span>
							</div>
						</div>
						<div class="columns">
							<div class="column">
								<span>Rejeté le :</span>
							</div>
							<div class="column">
								<span>{{ order.rejected_at }}</span>
							</div>
						</div>
					</template>
					<div class="columns">
						<div class="column">
							<span>Observations :</span>
						</div>
						<div class="column">
							<span>{{ order.card_observations }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</VCard>
</template>

<script setup lang="ts">
	import statusColor from "/@src/utils/status-color";

	const props = defineProps<{
		order: any;
	}>();

	const isPlate = computed(() => props.order.type === "both" || props.order.type === "plate");

	const isGrayCard = computed(() => props.order.type === "both" || props.order.type === "gray_card");
</script>

<style lang="scss"></style>
