<template>
	<div v-if="demand && can('update-im-demand')" class="intro-y flex items-center mt-4">
		<div class="w-full sm:w-auto flex mt-4 sm:mt-0 ml-auto">
			<router-link class="btn btn-outline-warning shadow-md mr-4" :to="goTo(demand)">
				<i class="fa-light fa-pencil w-4 h-4 me-2"></i>
				Modifier ma demande
			</router-link>
		</div>
	</div>

	<LoaderSpinner v-if="loading" type="block" />

	<ClientImmatriculationDemandShow v-else :demand-info="demand" />

	<div class="intro-y col-span-12">
		<div class="flex align-center justify-end mt-4">
			<button class="btn btn-outline-primary w-36 mr-4 border-2" type="button" @click="$router.back()">
				Retour
			</button>
		</div>
	</div>
</template>

<script setup>
	import { useCrudStore } from "@/stores/crud.js";
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onMounted } from "vue";
	import LoaderSpinner from "@/components/LoaderSpinner.vue";
	import ClientImmatriculationDemandShow from "@/views/global/ClientImmatriculationDemandShow.vue";
	import { updateServiceMap } from "../../../space-config.js";
	import { userHasPermissions } from "@/helpers/permissions.js";

	const props = defineProps({
		id: {
			type: String,
			required: true,
		},
	});

	const crudStore = useCrudStore();
	const { loading, row: demand, url } = storeToRefs(crudStore);
	const { can } = userHasPermissions();

	const fetchDemand = async () => {
		url.value = "/client/demands";
		await crudStore.fetchRow(props.id);
	};

	const goTo = (demand) => {
		return {
			name: updateServiceMap[demand.service_code],
			params: { serviceId: demand.service_id, demandId: demand.id },
		};
	};

	onBeforeMount(() => {
		loading.value = true;
		demand.value = null;
	});

	onMounted(async () => {
		await fetchDemand();
	});
</script>

<style scoped></style>
