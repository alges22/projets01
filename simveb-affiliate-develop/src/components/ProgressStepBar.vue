<template>
	<ol
		role="list"
		class="scroll-container mt-4 divide-y divide-gray-300 rounded-md border border-gray-300 md:flex md:divide-y-0 overflow-y-scroll"
	>
		<li v-for="(step, stepIdx) in steps" :key="stepIdx" class="relative md:flex md:flex-1">
			<div v-if="step.done" class="group flex w-full items-center">
				<span class="flex items-center px-6 py-4 text-sm font-medium">
					<span
						class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-indigo-600 group-hover:bg-indigo-800"
					>
						<i class="fa-light fa-check fa-2xl text-white" aria-hidden="true" />
					</span>
					<span class="ml-4 text-lg font-bold text-gray-900">{{ step.name }}</span>
				</span>
			</div>
			<div v-else-if="step.current" class="flex items-center px-6 py-4 text-sm font-medium" aria-current="step">
				<span
					class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border-2 border-indigo-600"
				>
					<span class="text-indigo-600">{{ stepIdx + 1 }}</span>
				</span>
				<span class="ml-4 text-sm font-bold text-primary">{{ step.name }}</span>
			</div>
			<div v-else class="group flex items-center">
				<span class="flex items-center px-6 py-4 text-sm font-medium">
					<span
						class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border-2 border-gray-300 group-hover:border-gray-400"
					>
						<span class="text-gray-500 group-hover:text-gray-900">{{ stepIdx + 1 }}</span>
					</span>
					<span class="ml-4 text-lg font-bold text-gray-500 group-hover:text-gray-900">
						{{ step.name }}
					</span>
				</span>
			</div>
			<template v-if="stepIdx !== steps.length - 1">
				<div class="absolute top-0 right-0 hidden h-full w-5 md:block" aria-hidden="true">
					<svg class="h-full w-full text-gray-300" viewBox="0 0 22 80" fill="none" preserveAspectRatio="none">
						<path
							d="M0 -2L20 40L0 82"
							vector-effect="non-scaling-stroke"
							stroke="currentcolor"
							stroke-linejoin="round"
						/>
					</svg>
				</div>
			</template>
		</li>
	</ol>
</template>
<script setup>
	defineProps({
		steps: {
			type: Array,
			required: true,
		},
	});
</script>
