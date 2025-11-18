<template>
	<div class="d-inline-block text-nowrap">
		<template v-for="(action, index) in actions.slice(0, 2)" :key="index">
			<template v-if="action.type === 'button'">
				<button class="btn btn-sm btn-icon" @click="action.onClick(item)">
					<i :class="'fa-light fa-' + action.icon"></i>
				</button>
			</template>
			<template v-else>
				<router-link :to="action.to(item)" class="dropdown-item">
					{{ action.title }}
				</router-link>
			</template>
		</template>
		<template v-if="actions.length > 2">
			<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
				<i class="fa-light fa-dots-vertical-rounded"></i>
			</button>
			<div class="dropdown-menu dropdown-menu-end m-0">
				<template v-for="(action, index) in actions.slice(2)" :key="index">
					<template v-if="action.type === 'button'">
						<a href="#" class="dropdown-item" @click.prevent="action.onClick(item)">{{ action.title }}</a>
					</template>
					<template v-else>
						<router-link :to="action.to(item)" class="dropdown-item">{{ action.title }}</router-link>
					</template>
				</template>
			</div>
		</template>
	</div>
</template>

<script setup>
	defineProps({
		item: {
			required: true,
		},
		actions: {
			type: Array,
			required: true,
		},
	});
</script>

<style scoped></style>
