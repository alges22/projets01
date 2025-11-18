<template>
	<button
		type="button"
		class="button is-circle is-outlined is-primary button px-4 py-2 rounded-full me-4"
		@click="$router.back()"
	>
		<slot name="before"></slot>
		<i class="fa-light fa-arrow-left" />
		<slot></slot>
	</button>
</template>
<script lang="ts" setup></script>
