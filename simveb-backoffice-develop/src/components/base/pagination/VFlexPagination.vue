<script setup lang="ts">
	export interface VFlexPaginationProps {
		itemPerPage: number;
		totalItems: number;
		currentPage?: number;
		maxLinksDisplayed?: number;
		noRouter?: boolean;
		routerQueryKey?: string;
		totalPages: number;
	}

	export interface VFlexPaginationEmits {
		(e: "update:currentPage", currentPage: number): void;
		(e: "paginate", currentPage: number): void;
	}

	const emits = defineEmits<VFlexPaginationEmits>();
	const props = withDefaults(defineProps<VFlexPaginationProps>(), {
		currentPage: 1,
		maxLinksDisplayed: 4,
		useRouter: true,
		routerQueryKey: "page",
	});

	const lastPage = computed(() => Math.ceil(props.totalItems / props.itemPerPage) || 1);
	const totalPageDisplayed = computed(() =>
		lastPage.value > props.maxLinksDisplayed + 2 ? props.maxLinksDisplayed + 2 : lastPage.value,
	);
	const pages = computed(() => {
		const _pages = [];
		let firstButton = props.currentPage - Math.floor(totalPageDisplayed.value / 2);
		let lastButton = firstButton + (totalPageDisplayed.value - Math.ceil(totalPageDisplayed.value % 2));

		if (firstButton < 1) {
			firstButton = 1;
			lastButton = firstButton + (totalPageDisplayed.value - 1);
		}

		if (lastButton > lastPage.value) {
			lastButton = lastPage.value;
			firstButton = lastButton - (totalPageDisplayed.value - 1);
		}

		for (let page = firstButton; page <= lastButton; page += 1) {
			if (page === firstButton || page === lastButton) {
				continue;
			}

			_pages.push(page);
		}

		return _pages;
	});

	const showLastLink = computed(() => lastPage.value > 1);

	const setCurrentPage = (number: number) => {
		emits("paginate", number);
	};

	const previous = () => {
		if (props.currentPage === 1) return;
		emits("paginate", props.currentPage - 1);
	};

	const next = () => {
		if (props.currentPage >= props.totalPages) return;
		emits("paginate", props.currentPage + 1);
	};
</script>

<template>
	<VFlex
		role="navigation"
		class="flex-pagination pagination is-rounded"
		aria-label="pagination"
		justify-content="space-between"
	>
		<ul class="pagination-list">
			<slot name="before-pagination"></slot>
			<li>
				<button
					tabindex="0"
					class="pagination-link"
					aria-label="Aller à la page 1"
					:class="[currentPage === 1 && 'is-current']"
					@click="(e: MouseEvent) => setCurrentPage(1)"
				>
					1
				</button>
			</li>

			<li v-if="showLastLink && (pages.length === 0 || pages[0] > 2)">
				<span class="pagination-ellipsis">…</span>
			</li>

			<li v-for="page in pages" :key="page">
				<button
					tabindex="0"
					class="pagination-link"
					:aria-label="'Aller à la page ' + page"
					:aria-current="currentPage === page ? 'page' : undefined"
					:class="[currentPage === page && 'is-current']"
					@click="(e: MouseEvent) => setCurrentPage(page)"
				>
					{{ page }}
				</button>
			</li>

			<li v-if="showLastLink && pages[pages.length - 1] < lastPage - 1">
				<span class="pagination-ellipsis">…</span>
			</li>

			<li v-if="showLastLink">
				<button
					tabindex="0"
					class="pagination-link"
					:aria-label="'Aller à la page ' + lastPage"
					:class="[currentPage === lastPage && 'is-current']"
					@click="(e: MouseEvent) => setCurrentPage(lastPage)"
				>
					{{ lastPage }}
				</button>
			</li>
			<slot name="after-pagination"></slot>
		</ul>

		<slot name="before-navigation"></slot>
		<button
			:disabled="currentPage === 1"
			tabindex="0"
			class="pagination-previous has-chevron"
			@click="(e: MouseEvent) => previous()"
		>
			<i aria-hidden="true" class="fa-light fa-chevron-left"></i>
		</button>
		<button
			:disabled="currentPage >= totalPages"
			tabindex="0"
			class="pagination-next has-chevron"
			@click="(e: MouseEvent) => next()"
		>
			<i aria-hidden="true" class="fa-light fa-chevron-right"></i>
		</button>
		<slot name="after-navigation"></slot>
	</VFlex>
</template>

<style lang="scss">
	.flex-pagination {
		button {
			cursor: pointer;
		}
	}
</style>
