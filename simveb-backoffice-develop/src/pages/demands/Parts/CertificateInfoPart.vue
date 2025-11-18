<template>
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 tab-details-inner">
		<VCard class="side-card">
			<div class="card-head">
				<h3 class="font-bold text-light">Certificat</h3>
			</div>
			<div class="card-inner is-one-third">
				<div class="columns">
					<div class="column">
						<span>Numéro de certificat</span>
					</div>
					<div class="column">
						<span>{{ demandInfo.model.reference }}</span>
					</div>
				</div>
				<div class="columns">
					<div class="column">
						<span>Fichier</span>
					</div>
					<div class="column">
						<span>
							<button v-if="demandInfo.model.certificate" type="button" @click="openFile">Voir</button>
							<template v-else>Pas encore disponible</template>
						</span>
					</div>
				</div>
			</div>
		</VCard>
	</div>
</template>

<script lang="ts" setup>
	import client from "/@src/composable/axiosClient";

	const props = defineProps<{
		demandInfo: any;
	}>();

	const openFile = async () => {
		await client({
			method: "GET",
			url: `client/certificates/${props.demandInfo.model.certificate.id}`,
			data: {},
		}).then((response) => {
			const href = response.data;
			const link = document.createElement("a");
			link.href = href;
			link.setAttribute("download", "Certificat.pdf");
			link.style.display = "none";
			link.setAttribute("target", "_blank");
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
