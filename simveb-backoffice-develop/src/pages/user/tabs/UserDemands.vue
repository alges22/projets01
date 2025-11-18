<script setup lang="ts">
	defineProps<{
		demands: any;
	}>();
</script>

<template>
	<div class="card-inner pt-1 mt-2" style="font-size: 12px">
		<div v-if="demands.length == 0" class="has-text-centered has-text-weight-bold">Aucune demande</div>
		<div v-for="demand in demands" :key="demand.id" class="mt-4">
			<div class="columns">
				<div class="column is-half has-text-weight-bold" style="background: #d4e4fb; color: #1470eb">
					<span class="">{{ demand.service.name }}</span>
				</div>

				<div class="column is-half has-text-weight-bold" style="background: #cee0fa; color: #1470eb">
					<span class="has-text-weight-semibold">{{ demand.created_at }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column is-half" style="background: #f6f9fe">
					<span class="">Dossier Ref</span>
				</div>

				<div class="column is-half has-text-weight-semibold" style="background: #f6f9fe">
					<span class="has-text-weight-semibold"># {{ demand.reference }}</span>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped lang="scss"></style>
