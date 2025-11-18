<script setup lang="ts">
	import type { CImmatriculationPlateShape } from "/@src/composable/useImmatriculationPlate";

	export interface CImmatriculationPlateEmits {
		(e: "flip"): void;
	}

	export interface CImmatriculationPlateProps {
		shape?: CImmatriculationPlateShape;
		color?: string;
		textColor?: string;
		prefix?: string;
		zone?: string;
		number?: string;
		republic?: string;
		flipped?: boolean;
	}

	const emit = defineEmits<CImmatriculationPlateEmits>();
	const props = withDefaults(defineProps<CImmatriculationPlateProps>(), {
		shape: "rectangular",
		color: "#f5f5dc",
		textColor: "#000",
		prefix: "AC",
		zone: "02",
		number: "124",
		republic: "RB",
	});

	const backFaceStyle = `background-color: ${props.color}`;
</script>

<template>
	<div class=""></div>
</template>
