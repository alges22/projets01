<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";

	const { can } = userHasPermissions();
</script>
<template>
	<div class="mobile-subsidebar">
		<div class="inner">
			<div class="sidebar-title">
				<h3>Plaques</h3>
			</div>

			<ul class="submenu" data-simplebar>
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
