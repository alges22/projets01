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
