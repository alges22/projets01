<template>
	<div
		class="w-24 h-24 lg:w-16 lg:h-16 lg:mr-4 rounded-full p-2 flex items-center justify-center"
		:class="{
			'bg-[#1371EB33]': service.code === 'IMMATRICULATION_STANDARD',
			'bg-[#FF474733]':
				service.code === 'IMMATRICULATION_PRESTIGE_LABEL' ||
				service.code === 'IMMATRICULATION_PRESTIGE_NUMBER_LABEL' ||
				service.code === 'IMMATRICULATION_PRESTIGE_NUMBER',
			'bg-[#F7506E33]':
				service.code === 'SALE_DECLARATION' ||
				service.code === 'TINTED_WINDOW_AUTHORIZATION' ||
				service.code === 'GLASS_ENGRAVING',
			'bg-[#00AFB533]': service.code === 'MUTATION',
			'bg-[#3227AE33]': service.code === 'TITLE_RECOVERY' || service.code === 'TITLE_DEPOSIT',
			'bg-[#795D6833]': service.code === 'RE_IMMATRICULATION',
			'bg-[#008CCF33]': service.code === 'VEHICLE_TRANSFORMATION',
		}"
	>
		<template v-if="service.code === 'IMMATRICULATION_STANDARD'">
			<img alt="Cliquez pour choisir la demande" class="" src="@/assets/images/parc/plaque.png" />
		</template>
		<template v-else-if="service.code === 'IMMATRICULATION_PRESTIGE_LABEL'">
			<img alt="Cliquez pour choisir la demande" class="" src="@/assets/images/parc/prestige.png" />
		</template>
		<template v-else-if="service.code === 'IMMATRICULATION_PRESTIGE_NUMBER'">
			<img alt="Cliquez pour choisir la demande" class="" src="@/assets/images/parc/prestige.png" />
		</template>
		<template v-else-if="service.code === 'IMMATRICULATION_PRESTIGE_NUMBER_LABEL'">
			<img alt="Cliquez pour choisir la demande" class="" src="@/assets/images/parc/prestige.png" />
		</template>
		<template v-else-if="service.code === 'SALE_DECLARATION'">
			<img alt="Cliquez pour choisir la demande" class="" src="@/assets/images/parc/car_coin.png" />
		</template>
		<template v-else-if="service.code === 'MUTATION'">
			<img alt="Cliquez pour choisir la demande" class="" src="@/assets/images/parc/arrow_bue_green.png" />
		</template>
		<template v-else-if="service.code === 'TITLE_RECOVERY'">
			<img alt="Cliquez pour choisir la demande" class="" src="@/assets/images/parc/reprise.png" />
		</template>
		<template v-else-if="service.code === 'TITLE_DEPOSIT' || service.code === 'GLASS_ENGRAVING'">
			<img alt="Cliquez pour choisir la demande" class="" src="@/assets/images/parc/reprise.png" />
		</template>
		<template v-else-if="service.code === 'RE_IMMATRICULATION'">
			<img alt="Cliquez pour choisir la demande" class="" src="@/assets/images/parc/reimat.png" />
		</template>
		<template v-else-if="service.code === 'VEHICLE_TRANSFORMATION'">
			<img alt="Cliquez pour choisir la demande" class="" src="@/assets/images/parc/arrow_crossing.png" />
		</template>
		<template v-else-if="service.code === 'TINTED_WINDOW_AUTHORIZATION'">
			<img alt="Cliquez pour choisir la demande" class="" src="@/assets/images/parc/file_pen.png" />
		</template>

		<slot />
	</div>
</template>

<script setup>
	import { defineProps } from "vue";

	defineProps({
		service: {
			type: Object,
			required: true,
		},
	});
</script>
