<script setup lang="ts">
	const emit = defineEmits(["close"]);
</script>

<template>
	<div class="sidebar-panel is-generic">
		<div class="subpanel-header">
			<h3 class="no-mb">Véhicule à 3 roues</h3>
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
				<li>
					<RouterLink
						:to="{
							name: 'immatriculation_demands',
							params: {
								wheels: 3,
							},
						}"
					>
						Immatriculation
					</RouterLink>
				</li>
				<li class="divider"></li>
				<li>
					<RouterLink
						:to="{
							name: 're_immatriculation_demands',
							params: {
								wheels: 3,
							},
						}"
					>
						Ré-Immatriculation
					</RouterLink>
				</li>
			</ul>
		</div>
	</div>
</template>

<style lang="scss">
	@import "/@src/scss/layout/sidebar-panel";
</style>
