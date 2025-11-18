<script setup lang="ts">
	defineProps<{
		logs: any;
	}>();
</script>

<template>
	<div class="card-inner pt-1 mt-2" style="font-size: 12px">
		<div v-if="logs.length == 0" class="has-text-centered has-text-weight-bold">Aucune donnée</div>
		<div v-for="log in logs" :key="logs.id" class="mt-4">
			<div class="columns">
				<div class="column is-half has-text-weight-bold" style="background: #d4e4fb; color: #1470eb">
					<span class="">{{ log.log_action }}</span>
				</div>

				<div class="column is-half has-text-weight-bold" style="background: #cee0fa; color: #1470eb">
					<span class="has-text-weight-semibold">{{ log.created_at }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column is-half" style="background: #f6f9fe">
					<span class="">IP</span>
				</div>

				<div class="column is-half has-text-weight-semibold" style="background: #f6f9fe">
					<span class="has-text-weight-semibold"> {{ log.ip_address }}</span>
				</div>
			</div>
			<div class="columns">
				<div class="column is-half" style="background: #f6f9fe">
					<span class="">Description</span>
				</div>

				<div class="column is-half has-text-weight-semibold" style="background: #f6f9fe">
					<span class="has-text-weight-semibold"> {{ log.description }}</span>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped lang="scss"></style>
