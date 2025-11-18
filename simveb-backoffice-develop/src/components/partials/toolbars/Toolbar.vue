<script setup lang="ts">
	import { useDarkmode } from "/@src/stores/darkmode";

	const darkmode = useDarkmode();
</script>

<template>
	<div class="toolbar">
		<div class="toolbar-link">
			<label
				tabindex="0"
				class="dark-mode"
				role="button"
				@keydown.space.prevent="(e) => (e.target as HTMLLabelElement).click()"
			>
				<input
					data-cy="dark-mode-toggle"
					type="checkbox"
					:checked="!darkmode.isDark"
					@change="darkmode.onChange"
				/>
				<span></span>
			</label>
		</div>

		<!-- <a
      tabindex="0"
      role="button"
      class="toolbar-link right-panel-trigger"
      @keydown.space.prevent="panels.setActive('languages')"
      @click="panels.setActive('languages')"
    >
      <img :src="localFlagSrc" alt="" />
    </a> -->

		<slot></slot>
	</div>
</template>
