<script setup lang="ts">
	import CountryFlag from "vue-country-flag-next";

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
	<div class="is-flex is-justify-content-center">
		<div
			:style="'background-color: ' + bgColor"
			:class="{
				'immatriculation-plate-square': form === 'square',
				'immatriculation-plate-rec': form === 'rectangle',
			}"
		>
			<template v-if="form === 'square'">
				<template v-if="isLabel">
					<div class="plate-first-row">
						<div>
							<div class="plate-republic">
								<div class="is-flex is-justify-content-center">
									<country-flag country="bj" size="normal" />
								</div>
								<div class="is-flex is-justify-content-center" :style="'color: ' + textColor">
									<span>{{ countryCode }}</span>
								</div>
							</div>
						</div>
					</div>
					<div class="plate-numbers">
						{{ immatriculationNumber }}
					</div>
				</template>

				<template v-else>
					<div class="plate-first-row">
						<div>
							<div class="plate-republic">
								<div class="is-flex is-justify-content-center">
									<country-flag country="bj" size="normal" />
								</div>
								<div class="is-flex is-justify-content-center" :style="'color: ' + textColor">
									<span>{{ immatriculationNumber[4] }}</span>
								</div>
							</div>
						</div>
						<div class="plate-prefix" :style="'color: ' + textColor">
							{{ immatriculationNumber[0] }}
						</div>
						&nbsp;
						<div class="plate-alpha-letters" :style="'color: ' + textColor">
							{{ immatriculationNumber[2] }}
						</div>
					</div>
					<div class="plate-numbers">
						<div class="plate-zone" :style="'color: ' + textColor">
							{{ immatriculationNumber[1] }}
						</div>
						<div class="plate-number" :style="'color: ' + textColor">
							{{ immatriculationNumber[3] }}
						</div>
					</div>
				</template>
			</template>

			<template v-else-if="form === 'rectangle'">
				<template v-if="isLabel">
					<div class="plate-prefix" :style="'color: ' + textColor">
						<span>{{ immatriculationNumber }}</span>
					</div>
					<div class="plate-republic">
						<div class="is-flex is-justify-content-center">
							<country-flag country="bj" size="normal" />
						</div>
						<div class="is-flex is-justify-content-center" :style="'color: ' + textColor">
							<!-- fifth position -->
							<span>{{ countryCode }}</span>
						</div>
					</div>
				</template>
				<template v-else>
					<div class="plate-alapha-letters" :style="'color: ' + textColor">
						<span>{{ immatriculationNumber[1] }}</span>
					</div>
					<div class="plate-numbers" :style="'color: ' + textColor">
						<!-- third position -->
						<span class="plate-zone">{{ immatriculationNumber[2] }}</span>
						<!-- fourth position -->
						<span class="plate-number">{{ immatriculationNumber[3] }}</span>
					</div>
					<div class="plate-republic">
						<div class="is-flex is-justify-content-center">
							<country-flag country="bj" size="normal" />
						</div>
						<div class="is-flex is-justify-content-center" :style="'color: ' + textColor">
							<!-- fifth position -->
							<span>{{ immatriculationNumber[4] }}</span>
						</div>
					</div>
				</template>
			</template>
		</div>
	</div>
</template>
