<script setup lang="ts">
	const emit = defineEmits(["close"]);
</script>

<template>
	<div class="sidebar-panel is-generic">
		<div class="subpanel-header">
			<h3 class="no-mb">Véhicules</h3>
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
					<RouterLink to="/vehicles">
						<i class="lnil lnil-car pr-2" aria-hidden="true"></i>
						Véhicules
					</RouterLink>
				</li>
				<li class="divider with-label">
					<span class="divider-label">Configuration</span>
				</li>
				<li>
					<RouterLink to="/admin/vehicle-categories">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Catégories de véhicule
					</RouterLink>
				</li>
				<li>
					<RouterLink to="/admin/vehicle-types">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Types de véhicule
					</RouterLink>
				</li>
				<li>
					<RouterLink to="/admin/vehicle-brands">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Marques de véhicule
					</RouterLink>
				</li>
				<li>
					<RouterLink to="/admin/vehicle-powers">
						<i class="fas fa-horse-head pr-2" aria-hidden="true"></i>
						Puissance de véhicule
					</RouterLink>
				</li>
				<li class="divider"></li>
				<li>
					<RouterLink to="/glass-types">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Types de vitre
					</RouterLink>
				</li>
				<li>
					<RouterLink to="/energy-sources">
						<i class="lnil lnil-bolt-alt pr-2" aria-hidden="true"></i>
						Sources d'énergie
					</RouterLink>
				</li>
				<li class="divider"></li>
				<li>
					<RouterLink to="/admin/vehicle-caracteristic-categories">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Catégories de caractéristique
					</RouterLink>
				</li>
				<li>
					<RouterLink to="/admin/vehicle-caracteristics">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Caractéristiques de véhicule
					</RouterLink>
				</li>
				<li>
					<RouterLink to="/admin/vehicle-caracteristic-prices">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Prix des caractéristiques
					</RouterLink>
				</li>
			</ul>
		</div>
	</div>
</template>

<style lang="scss">
	@import "/@src/scss/layout/sidebar-panel";
</style>
