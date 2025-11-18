<template>
	<li v-if="can('dashboard')">
		<MenuLink
			:s-key="sKey"
			:to="{ name: 'admin_home' }"
			icon="home"
			label="Tableau de bord"
			title="Tableau de bord"
		/>
	</li>

	<!--	<li v-if="can('browse-users')">
		<RouterLink v-if="sKey == 'icon'" to="/users">
			<i aria-hidden="true" class="fa-light fa-users sidebar-svg"></i>
		</RouterLink>
		<RouterLink v-if="sKey == 'text'" to="/users">Tous les utilisateurs</RouterLink>
	</li>-->

	<li v-if="can('browse-im-demand')">
		<MenuLink :s-key="sKey" :to="{ name: 'demands_list' }" icon="file-pen" label="Demandes" title="Demandes" />
	</li>

	<li v-if="can('show-vehicle')">
		<MenuLink :s-key="sKey" :to="{ name: 'vehicles_list' }" icon="car" label="Véhicules" title="Véhicules" />
	</li>

	<!-- <li>
		<MenuLink :s-key="sKey" :to="{ name: 'aggregators' }" icon="money-bill" label="Paiements" title="Paiements" />
	</li> -->

	<li v-if="can('access-config')">
		<MenuLink :s-key="sKey" :to="{ name: 'config' }" icon="cog" label="Configuration" title="Configuration" />
	</li>

	<li v-if="can('browse-order')">
		<MenuLink :s-key="sKey" :to="{ name: 'orders_list' }" icon="bag-shopping" label="Commandes" title="Orders" />
	</li>

	<li v-if="can('browse-activity-log')">
		<MenuLink :s-key="sKey" :to="{ name: 'logs_list' }" icon="notes" label="Journalisation" title="Logs" />
	</li>

	<li v-if="can('browse-accreditation')">
		<MenuLink
			:s-key="sKey"
			:to="{ name: 'accreditations_list' }"
			icon="user-tag"
			label="Accréditations"
			title="Accréditations"
		/>
	</li>

	<li v-if="can('browse-space-registration-request')">
		<MenuLink
			:s-key="sKey"
			:to="{ name: 'spaces_registration_request_list' }"
			icon="notes"
			label="Enregistrement d'espaces"
			title="Enregistrement d'espaces"
		/>
	</li>
	<li v-if="can('browse-pledge')">
		<MenuLink
			:s-key="sKey"
			:to="{ name: 'pledges_issue_request_list' }"
			icon="notes"
			label="Demandes d'émission de gage"
			title="Demandes d'émission de gage"
		/>
	</li>
	<li v-if="can('browse-pledge-lift')">
		<MenuLink
			:s-key="sKey"
			:to="{ name: 'pledges_lift_issue_request_list' }"
			icon="notes"
			label="Demandes de levées de gage"
			title="Demandes de levées de gage"
		/>
	</li>
	<li v-if="can('browse-oppositions')">
		<MenuLink
			:s-key="sKey"
			:to="{ name: 'oppositions' }"
			icon="notes"
			label="Demandes d'opposition"
			title="Demandes d'opposition"
		/>
	</li>

	<li v-if="can('browse-plate-order')">
		<MenuLink
			:s-key="sKey"
			:to="{ name: 'plate_order_list' }"
			icon="rectangle-barcode"
			label="Gestion des plaques"
			title="Gestion des plaques"
		/>
	</li>
</template>

<script setup lang="ts">
	import { userHasPermissions } from "/@src/utils/permission";

	const { can } = userHasPermissions();

	defineProps({
		sKey: {
			type: String,
			required: true,
		},
	});
</script>

<style scoped lang="scss"></style>
