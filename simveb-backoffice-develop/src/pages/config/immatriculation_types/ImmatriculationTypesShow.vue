<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { onBeforeMount, onUnmounted } from "vue";
	import { useCrudStore } from "/@src/stores/modules/crud";

	const route = useRoute();

	const crudStore = useCrudStore();
	const { url, row } = storeToRefs(crudStore);

	const itemId = route.params.id;

	onBeforeMount(() => {
		url.value = "/immatriculation-types";
	});

	onUnmounted(() => {
		crudStore.reset();
	});

	onMounted(() => {
		crudStore.fetchRow(itemId);
	});
</script>

<template>
	<div class="page-content-inner">
		<VCard class="mt-4">
			<h3 class="title is-5 mb-2">Détails type d'immatriculation</h3>
			<VTabs class="mt-4" selected="details" :tabs="[{ label: 'Détails', value: 'details' }]">
				<template #tab="{ activeValue }">
					<div v-if="activeValue === 'details'">
						<div class="w-full font-semibold text-lg" v-if="row">
							<div class="flex">
								<p class="w-1/2 text-black">Label</p>
								<p class="text-black">{{ row.label }}</p>
							</div>
							<div class="flex">
								<p class="w-1/2 text-black">Code</p>
								<p class="text-black">{{ row.code }}</p>
							</div>
							<div class="flex">
								<p class="w-1/2 text-black">Description</p>
								<p class="text-black">{{ row.description }}</p>
							</div>
							<div class="flex">
								<p class="w-1/2 text-black">Couleursde plaque</p>
								<p class="text-black">
									<span class="mr-3" v-for="color in row.plate_colors" :key="color.id">
										{{ color.label }} |
									</span>
								</p>
							</div>
						</div>
					</div>
				</template>
			</VTabs>
		</VCard>
	</div>
</template>
