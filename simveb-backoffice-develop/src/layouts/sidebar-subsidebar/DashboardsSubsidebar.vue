<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";

	const { can } = userHasPermissions();
	const emit = defineEmits(["close"]);
</script>

<template>
	<div class="sidebar-panel is-generic">
		<div class="subpanel-header">
			<h3 class="no-mb">Tableau de bord</h3>
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
				<li v-if="can('dashboard')">
					<RouterLink to="/admin">
						<i aria-hidden="true" class="lnil lnil-home pr-2"></i>
						Tableau de bord
					</RouterLink>
				</li>
			</ul>
		</div>
	</div>
</template>

<style lang="scss">
	@import "/@src/scss/layout/sidebar-panel";
</style>
