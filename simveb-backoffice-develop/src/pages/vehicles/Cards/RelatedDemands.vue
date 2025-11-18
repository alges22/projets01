<template>
	<VCard class="side-card overflow-y-scroll">
		<div class="card-head">
			<h3>Dossiers liés</h3>
		</div>
		<div class="card-inner is-one-third pt-1">
			<template v-for="(demand, index) in demands" :key="index">
				<div class="flex my-2 flex-nowrap">
					<div class="has-text-weight-semibold whitespace-nowrap">
						Dossier Ref #
						<KeyField
							:value="demand.reference"
							:to="{ name: 'demands_show', params: { demandId: demand.id } }"
						/>
					</div>
					<div v-if="demand.is_delayed" class="flex items-center space-x-2 bg-red-100 px-4 rounded-md">
						<div class="font-semibold text-red-500 ms-2">Retard</div>
						<div class="font-semibold bg-red-200 text-red-600 px-2 rounded-md">
							{{ formatHours(Number.parseInt(demand.delayed_hours)) }}
						</div>
					</div>
				</div>
			</template>
		</div>
	</VCard>
</template>

<script setup lang="ts">
	import { formatHours } from "/@src/utils/helpers";

	defineProps<{
		demands: any;
	}>();
</script>

<style lang="scss"></style>
