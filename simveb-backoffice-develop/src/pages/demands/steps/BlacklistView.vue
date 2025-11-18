<script setup lang="ts">
	import { useDemandStore } from "/src/stores/modules/demand";
	import { storeToRefs } from "pinia";

	export interface ImmatriculationDemandBlackListViewProps {
		demandId: string;
	}

	const props = withDefaults(defineProps<ImmatriculationDemandBlackListViewProps>(), {
		demandId: undefined,
	});

	const demandStore = useDemandStore();
	const { demand, url } = storeToRefs(demandStore);

	onMounted(() => {
		if (!demand.value.id) {
			demandStore.fetchDemand(props.demandId);
		}
	});

	onBeforeMount(() => {
		url.value = "/immatriculation-demands";
	});
</script>

<template>
	<div class="columns tab-details-inner">
		<div class="column is-8">
			<VCard class="side-card">
				<div class="card-head">
					<h4>Liste noire</h4>
				</div>
				<div class="card-inner is-size-6-5">
					<div class="columns">
						<div class="column is-one-quarter">
							<span class="has-text-weight-semibold">Date du contrôle</span>
						</div>
						<div class="column">
							<span> {{ demand.black_listed_at ?? "Aucun" }} </span>
						</div>
					</div>
					<div class="columns">
						<div class="column is-one-quarter">
							<span class="has-text-weight-semibold">Validé à</span>
						</div>
						<div class="column">
							<span>{{ demand.black_list_verified_at }}</span>
						</div>
					</div>
				</div>
			</VCard>
		</div>
	</div>
</template>
