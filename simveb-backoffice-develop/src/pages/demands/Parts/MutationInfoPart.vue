<template>
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 tab-details-inner">
		<UserInfoCard title="Ancien propriétaire" :user="demandInfo.model.sale_declaration.vehicle_owner.identity" />

		<UserInfoCard title="Nouveau propriétaire" :user="demandInfo.model.new_owner" />

		<VCard class="side-card">
			<div class="card-head">
				<h3 class="font-bold text-light">Vente</h3>
			</div>
			<div class="card-inner is-one-third">
				<div class="columns">
					<div class="column">
						<span>Référence de vente</span>
					</div>
					<div class="column">
						<span>{{ demandInfo.model.sale_declaration_reference }}</span>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>Date de vente</span>
					</div>
					<div class="column">
						<span>{{ dayjs(demandInfo.model.sale_declaration.created_at).format("DD-MM-YYYY") }}</span>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>Fichier</span>
					</div>
					<div class="column">
						<span>
							<button
								v-if="demandInfo.model.sale_declaration.certificate"
								type="button"
								@click="openFile"
							>
								Voir
							</button>
							<template v-else>Pas encore disponible</template>
						</span>
					</div>
				</div>
			</div>
		</VCard>
	</div>
</template>

<script lang="ts" setup>
	import UserInfoCard from "/@src/pages/demands/Cards/UserInfoCard.vue";
	import client from "/@src/composable/axiosClient";
	import { useDate } from "vue3-dayjs-plugin/useDate";

	const props = defineProps<{
		demandInfo: any;
	}>();

	const dayjs = useDate();

	const openFile = async () => {
		await client({
			method: "GET",
			url: `client/certificates/${props.demandInfo.model.sale_declaration.certificate.id}`,
			responseType: "blob",
			data: {},
		}).then((response) => {
			const href = URL.createObjectURL(response.data);
			const link = document.createElement("a");
			link.href = href;
			link.setAttribute("download", "Certificat de vente.pdf");
			document.body.appendChild(link);
			link.click();

			document.body.removeChild(link);
			URL.revokeObjectURL(href);
		});
	};
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.tab-details-inner {
		flex-wrap: wrap !important;
	}

	.color-box {
		width: 35px;
		height: 35px;
		border-radius: 15%;
		margin-left: 10px;
	}

	@media only screen and (width <=767px) {
	}

	@media only screen and (width >=768px) and (width <=1024px) and (orientation: portrait) {
	}
</style>
