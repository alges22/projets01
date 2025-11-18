<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";

	const { can } = userHasPermissions();
</script>

<template>
	<div class="mobile-subsidebar">
		<div class="inner">
			<div class="sidebar-title">
				<h3>Utilisateurs</h3>
			</div>

			<ul class="submenu" data-simplebar>
				<li v-if="can('browse-users')">
					<RouterLink to="/users">Tous les utilisateurs</RouterLink>
				</li>
				<li v-if="can('browse-affiliate-staff')">
					<RouterLink
						:to="{
							name: 'afifliate_staff',
						}"
					>
						Personnel affilié
					</RouterLink>
				</li>
			</ul>
		</div>
	</div>
</template>
