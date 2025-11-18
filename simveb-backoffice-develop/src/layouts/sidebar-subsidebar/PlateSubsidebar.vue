<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";

	const emit = defineEmits(["close"]);
	const { can } = userHasPermissions();
</script>

<template>
	<div class="sidebar-panel is-generic">
		<div class="subpanel-header">
			<h3 class="no-mb">Plaques</h3>
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
				<li v-if="can('browse-plate')">
					<router-link :to="{ name: 'plate_list' }" tabindex="0" role="button">
						Liste des plaques
					</router-link>
				</li>
				<li v-if="can('browse-plate-order')">
					<router-link :to="{ name: 'plate_order_list' }" tabindex="0" role="button"> Commandes </router-link>
				</li>
			</ul>
		</div>
	</div>
</template>

<style lang="scss">
	@import "/@src/scss/layout/sidebar-panel";
</style>
