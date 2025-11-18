<template>
	<ul v-if="totalPages >= 1" class="pagination">
		<li class="page-item">
			<button
				class="page-link"
				type="button"
				:class="{
					disabled: currentPage === 1,
				}"
				@click.prevent="previous"
			>
				<i class="fa-light fa-chevron-left w-4 h-4" />
			</button>
		</li>
		<template v-if="currentPage - delta > 1">
			<li
				class="page-item"
				:class="{
					disabled: currentPage === 1,
				}"
			>
				<button
					class="page-link"
					type="button"
					:class="{
						disabled: currentPage === 1,
					}"
					@click.prevent="setCurrentPage(1)"
				>
					1
				</button>
			</li>
			<li class="page-item disabled">
				<button class="page-link" type="button">...</button>
			</li>
		</template>

		<template v-for="index in totalPages">
			<li
				v-if="index >= currentPage - delta && index <= currentPage + delta"
				:key="index"
				class="page-item"
				:class="{
					active: currentPage === index,
					disabled: currentPage === index,
				}"
			>
				<button
					class="page-link"
					type="button"
					:class="{ disabled: currentPage === index }"
					@click.prevent="setCurrentPage(index)"
				>
					{{ index }}
				</button>
			</li>
		</template>

		<template v-if="currentPage + delta < totalPages">
			<li class="page-item disabled">
				<button class="page-link" type="button">...</button>
			</li>
			<li
				class="page-item"
				:class="{
					disabled: currentPage === totalPages,
				}"
			>
				<button
					class="page-link"
					type="button"
					:class="{
						disabled: currentPage === totalPages,
					}"
					@click.prevent="setCurrentPage(totalPages)"
				>
					{{ totalPages }}
				</button>
			</li>
		</template>

		<li class="page-item">
			<button
				class="page-link"
				type="button"
				:class="{
					disabled: currentPage === totalPages,
				}"
				@click.prevent="next"
			>
				<i class="fa-light fa-chevron-right w-4 h-4" />
			</button>
		</li>
	</ul>
</template>

<script setup>
	import { toRefs } from "vue";

	const props = defineProps({
		totalPages: {
			type: Number,
			required: true,
		},
		currentPage: {
			required: true,
			type: Number,
		},
	});

	const delta = 2;
	const { totalPages, currentPage } = toRefs(props);
	const emit = defineEmits(["paginate"]);

	const setCurrentPage = (number) => {
		emit("paginate", number);
	};

	const previous = () => {
		if (currentPage.value === 1) return;
		emit("paginate", currentPage.value - 1);
	};

	const next = () => {
		if (currentPage.value >= totalPages.value) return;
		emit("paginate", currentPage.value + 1);
	};
</script>
