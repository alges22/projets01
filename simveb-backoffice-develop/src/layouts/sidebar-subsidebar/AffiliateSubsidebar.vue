<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";

	const { can } = userHasPermissions();
	const emit = defineEmits(["close"]);
</script>

<template>
	<div class="sidebar-panel is-generic">
		<div class="subpanel-header">
			<h3 class="no-mb">Affilié</h3>
			<div
				class="panel-close"
				tabindex="0"
				role="button"
				@keydown.space.prevent="emit('close')"
				@click="emit('close')"
			>
				<i aria-hidden="true" class="fa-light fa-x"></i>
			</div>
		</div>
		<div class="inner" data-simplebar>
			<ul>
				<li v-if="can('browse-affiliate-registration-request')">
					<RouterLink
						:to="{
							name: 'affiliate_registration_requests',
						}"
					>
						Demandes d'enregistrement d'affilié
					</RouterLink>
				</li>
				<li v-if="can('browse-affiliate')">
					<RouterLink
						:to="{
							name: 'affiliates',
						}"
					>
						Liste des affiliés
					</RouterLink>
				</li>
			</ul>
		</div>
	</div>
</template>

<style lang="scss">
	@import "/@src/scss/layout/sidebar-panel";
</style>
