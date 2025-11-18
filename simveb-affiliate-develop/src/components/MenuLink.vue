<template>
	<RouterLink :class="active ? 'side-menu--active' : ''" :title="title" :to="to" class="side-menu" role="button">
		<div class="side-menu__icon">
			<i v-if="icon" :class="'text-xl fa-light fa-' + icon"></i>
		</div>
		<div class="side-menu__title">
			{{ label }}
			<slot>
				<div v-if="hasChildren" class="side-menu__sub-icon transform rotate-180 me-3">
					<i class="fa-light fa-chevron-down"></i>
				</div>
			</slot>
		</div>
	</RouterLink>
</template>

<script setup>
	defineProps({
		active: {
			type: Boolean,
			default: false,
			required: false,
		},
		to: {
			type: [Object, String],
			required: true,
		},
		icon: {
			type: String,
			default: "",
			required: false,
		},
		label: {
			type: String,
			default: "",
			required: true,
		},
		title: {
			type: String,
			default: "",
			required: false,
		},
		hasChildren: {
			type: Boolean,
			default: false,
		},
	});
</script>

<style scoped></style>
