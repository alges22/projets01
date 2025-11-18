<template>
	<div class="flex flex-col lg:flex-row items-center p-5">
		<slot></slot>
		<slot name="content">
			<div class="lg:ml-2 lg:mr-auto text-center lg:text-left mt-3 lg:mt-0">
				<h2 v-if="title" class="font-semibold">{{ title }}</h2>
				<div v-if="subtitle" class="text-slate-500 text-xs mt-0.5">{{ subtitle }}</div>
			</div>
			<button v-if="buttonText" class="btn btn-primary btn-sm" @click="$emit('click')">
				{{ buttonText }}
			</button>
		</slot>
	</div>
</template>
<script setup>
	defineProps({
		imageAlt: {
			type: String,
			required: false,
		},
		title: {
			type: String,
			required: false,
		},
		subtitle: {
			type: String,
			required: false,
		},
		buttonText: {
			type: String,
			required: false,
		},
	});

	defineEmits(["click"]);
</script>
