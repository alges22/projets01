<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";

	const { can } = userHasPermissions();
</script>

<template>
	<div class="mobile-subsidebar">
		<div class="inner">
			<div class="sidebar-title">
				<h3>Véhicule à 4 roues</h3>
			</div>

			<ul class="submenu" data-simplebar>
				<li v-if="can('browse-im-demand')">
					<RouterLink
						:to="{
							name: 'immatriculation_demands',
							params: {
								wheels: 4,
							},
						}"
					>
						Immatriculation
					</RouterLink>
				</li>
				<VCollapseLinks v-if="can('browse-plate-duplicate') || can('browse-card-duplicate')">
					<template #header>
						Duplicata
						<i aria-hidden="true" class="rtl-hidden fa-light fa-chevron-right" />
						<i aria-hidden="true" class="ltr-hidden fa-light fa-chevron-left" />
					</template>
					<RouterLink
						v-if="can('browse-plate-duplicate')"
						:to="{ name: 'plate_duplicates' }"
						class="parent-link"
					>
						Plaques
					</RouterLink>
					<RouterLink
						v-if="can('browse-card-duplicate')"
						:to="{ name: 'gray_card_duplicates' }"
						class="parent-link"
					>
						Carte grise
					</RouterLink>
				</VCollapseLinks>

				<li v-if="can('browse-reimmatriculation-demand')">
					<RouterLink
						:to="{
							name: 're_immatriculation_demands',
							params: {
								wheels: 4,
							},
						}"
					>
						Ré-Immatriculation
					</RouterLink>
				</li>

				<li v-if="can('browse-impression-demand')">
					<RouterLink :to="{ name: 'print_order' }"> Demandes d'impression</RouterLink>
				</li>
			</ul>
		</div>
	</div>
</template>
