<template>
	<div class="content">
		<div class="top-bar -mx-4 px-4 md:mx-0 md:px-0 md:align-baseline mt-4">
			<BackButton />
			<nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">Vente</li>
				</ol>
			</nav>
		</div>

		<router-view />
	</div>
</template>

<script setup>
	import BackButton from "@/components/BackButton.vue";
</script>

<style scoped></style>
