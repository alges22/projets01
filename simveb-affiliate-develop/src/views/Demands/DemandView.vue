<template>
	<div class="intro-y flex items-center mt-4"></div>

	<LoaderSpinner v-if="loading" type="block" />

	<ImmatriculationDemandShow v-else :demand-info="demand" />

	<div class="intro-y col-span-12">
		<div class="flex align-center justify-end mt-4">
			<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.back()">
				Retour
			</button>
		</div>
	</div>
</template>

<script setup>
	import ImmatriculationDemandShow from "@/views/global/ClientImmatriculationDemandShow.vue";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onMounted } from "vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import { useDemandStore } from "@/stores/demand.js";

	const demandStore = useDemandStore();
	const { demand, loading } = storeToRefs(demandStore);

	const props = defineProps({
		demandId: {
			type: String,
			required: true,
		},
	});

	onBeforeMount(() => {
		loading.value = true;
		demand.value = null;
	});

	const fetchDemand = async () => {
		await demandStore.fetchDemand(props.demandId);
	};

	onMounted(async () => {
		await fetchDemand();
	});
</script>

<style scoped></style>
