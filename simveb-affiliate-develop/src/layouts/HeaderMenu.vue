<template>
	<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline">
		<BackButton />

		<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">{{ pageTitle }}</li>
			</ol>
		</nav>

		<NotificationBox />

		<AppsBox />

		<UserMenuDropdown />
	</div>
</template>

<script setup>
	import NotificationBox from "@/components/NotificationBox.vue";
	import AppsBox from "@/components/AppsBox.vue";
	import UserMenuDropdown from "@/components/UserMenuDropdown.vue";
	import BackButton from "@/components/BackButton.vue";
</script>
