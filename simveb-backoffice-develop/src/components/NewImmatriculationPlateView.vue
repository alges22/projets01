<script setup lang="ts">
	defineProps({
		immatriculationNumber: {},
		bgColor: {
			type: String,
			required: false,
			default: "#f5f5dc",
		},
		textColor: {
			type: String,
			required: false,
			default: "#000",
		},
		form: {
			type: String,
			required: false,
			default: "square",
		},
		isLabel: {
			type: Boolean,
			required: false,
			default: false,
		},
		countryCode: {
			type: String,
			required: false,
		},
	});
</script>
<template>
	<div
		:style="'background-color: ' + bgColor"
		:class="{
			'plate-square': form === 'square',
			'plate-rec': form === 'rectangle',
		}"
		class="border-4 border-black rounded-lg"
	>
		<template v-if="form === 'square'">
			<template v-if="isLabel">
				<div class="flex flex-col h-full justify-between">
					<div class="flex justify-between items-center h-16">
						<div class="w-8"></div>
						<div
							class="flex flex-col items-center justify-center bg-orange-400 w-8 text-white text-xs p-1 border border-black"
						>
							<span>xx</span>
						</div>
						<div class="flex flex-col items-center justify-center">
							<div class="flex items-center">
								<BeninFlagCardIcon />
							</div>
							<div :style="'color: ' + textColor" class="text-xl font-bold text-black">
								{{ countryCode }}
							</div>
						</div>
					</div>
					<div
						:style="'color: ' + textColor"
						class="flex justify-center space-wide text-5xl font-bold mt-2 mb-2"
					>
						<span class="text-black">{{ immatriculationNumber }}</span>
					</div>
				</div>
			</template>

			<template v-else>
				<div class="flex flex-col h-full justify-between">
					<!-- Partie supérieure -->
					<div class="flex justify-between items-center h-16">
						<!-- Lettres -->
						<div :style="'color: ' + textColor" class="flex items-center text-5xl font-bold">
							<span class="text-black">{{ immatriculationNumber[2] }}</span>
						</div>
						<!-- Espace -->
						<div
							class="flex flex-col items-center justify-center bg-orange-400 w-8 text-white text-xs p-1 border border-black"
						>
							<span>xx</span>
						</div>
						<!-- Drapeau et code du pays -->
						<div class="flex flex-col items-center justify-center">
							<div class="flex items-center">
								<BeninFlagCardIcon />
							</div>
							<!-- Country code -->
							<div :style="'color: ' + textColor" class="text-xl font-bold text-black">
								{{ immatriculationNumber[4] }}
							</div>
						</div>
					</div>
					<!-- Partie inférieure -->
					<div
						:style="'color: ' + textColor"
						class="flex justify-center space-wide text-5xl font-bold mt-2 mb-2"
					>
						<span class="text-black me-2">{{ immatriculationNumber[1] }}</span>
						<span class="text-black">{{ immatriculationNumber[3] }}</span>
					</div>
				</div>
			</template>
		</template>

		<template v-else-if="form === 'rectangle'">
			<template v-if="isLabel">
				<div :style="'color: ' + textColor" class="flex items-center space-x-2 text-5xl font-bold">
					<span>{{ immatriculationNumber }}</span>
				</div>

				<div class="flex-1"></div>

				<div class="flex items-center space-x-2">
					<div
						class="flex flex-col items-center justify-center bg-orange-400 text-white text-xs p-1 border border-black"
					>
						<span>xx</span>
					</div>
					<div class="flex flex-col items-center justify-center">
						<div class="flex items-center">
							<BeninFlagCardIcon />
						</div>
						<div :style="'color: ' + textColor" class="text-lg font-bold">
							{{ countryCode }}
						</div>
					</div>
				</div>
			</template>

			<template v-else>
				<!-- Letters and numbers -->
				<div :style="'color: ' + textColor" class="flex items-center space-x-2 text-5xl font-bold">
					<span class="">{{ immatriculationNumber[1] }}</span>
					<span class="">{{ immatriculationNumber[2] }}</span>
					<span class="">{{ immatriculationNumber[3] }}</span>
				</div>

				<!-- Space between letters/numbers and the right-side graphics -->
				<div class="flex-1"></div>

				<!-- Right-side graphics -->
				<div class="flex items-center space-x-2">
					<!-- Rectangle with text -->
					<div
						class="flex flex-col items-center justify-center bg-orange-400 text-white text-xs p-1 border border-black"
					>
						<span>xx</span>
					</div>
					<!-- Country flag graphic -->
					<div class="flex flex-col items-center justify-center">
						<div class="flex items-center">
							<BeninFlagCardIcon />
						</div>
						<!-- Country code -->
						<div :style="'color: ' + textColor" class="text-lg font-bold">
							{{ immatriculationNumber[4] }}
						</div>
					</div>
				</div>
			</template>
		</template>
	</div>
</template>

<style lang="scss">
	.plate-square {
		@apply p-2  h-36;
		width: 16rem;
	}
	.plate-rec {
		@apply flex items-center justify-center px-2;
		width: 22rem;
	}
	.space-wide {
		letter-spacing: 0.5rem;
		font-size: 2.75rem;
	}
</style>
