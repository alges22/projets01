<template>
	<div v-if="searchEnabled" class="centered-search">
		<div class="field">
			<div class="control has-icon">
				<input
					type="search"
					class="input search-input"
					:placeholder="searchPlaceholder ?? 'Rechercher des éléments...'"
					@input="onSearch($event.target.value)"
				/>
				<div class="form-icon">
					<i aria-hidden="true" class="fa-light fa-search" />
				</div>
			</div>
		</div>
	</div>
</template>
<script setup lang="ts">
	import { useSearchStore } from "/@src/stores/search";
	import { debounce } from "/@src/utils/helpers";
	import { storeToRefs } from "pinia";

	const { query: search, enabled: searchEnabled, placeholder: searchPlaceholder } = storeToRefs(useSearchStore());

	const onSearch = debounce((value) => {
		search.value = value;
	}, 500);
</script>
