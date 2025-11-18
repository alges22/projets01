<template>
	<div class="intro-x dropdown mr-auto sm:mr-6">
		<div
			class="dropdown-toggle notification cursor-pointer"
			role="button"
			aria-expanded="false"
			data-tw-toggle="dropdown"
		>
			<i class="text-2xl fa-light fa-bell nav-icon"></i>
			<div
				v-if="false"
				class="w-4 h-4 flex items-center justify-center absolute top-0 right-0 text-xs text-white rounded-full bg-danger font-medium -mt-1 -mr-1"
			>
				5
			</div>
		</div>
		<div class="notification-content pt-2 dropdown-menu">
			<div class="notification-content__box dropdown-content">
				<div class="notification-content__title">Notifications</div>
				<TextDivider width="w-1/6" class="mb-3" />
				<div class="cursor-pointer relative flex items-center">
					<div class="ml-2 overflow-hidden">
						<div class="w-full truncate text-slate-500 mt-0.5">Aucune notification</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
	import TextDivider from "@/components/TextDivider.vue";
</script>

<style scoped></style>
