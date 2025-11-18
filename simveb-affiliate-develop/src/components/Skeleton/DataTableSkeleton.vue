<template>
	<div
		v-for="n in 10"
		:key="n"
		class="intro-y col-span-12 md:col-span-6 bg-white rounded-md shadow-md pointer-events-none my-4"
	>
		<div class="animate-pulse lg:mr-auto text-center lg:text-left mt-3 lg:mt-0 w-full h-12 bg-gray-300"></div>
	</div>
</template>

<script setup></script>

<style scoped></style>
