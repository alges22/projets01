<template>
	<div class="list-widget">
		<div class="inner-list-wrapper is-active">
			<div class="inner-list">
				<div>
					<VBlock v-for="(file, index) in files" :key="index" center lighter class="inner-list-item">
						<template #icon>
							<img class="image-icon" src="/@src/assets/icons/doc.svg" alt="" />
						</template>
						<template #action>
							<VIconButton
								class="mr-2"
								color="primary"
								light
								raised
								circle
								icon="eye"
								target="_blank"
								:href="file.url"
							/>
							<VIconButton
								color="success"
								light
								circle
								icon="download"
								:href="`${API_URL}/download/${file.id}`"
							/>
						</template>

						<a target="_blank" :href="file.url">{{ file.name }}</a>
						<span>{{ file.type }}</span>
					</VBlock>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
	defineProps<{
		files?: any[];
	}>();

	const API_URL = import.meta.env.VITE_API_URL;
</script>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.list-widget {
		flex: 1;
		display: inline-block;
		width: 100%;
		padding: 15px;
		background-color: var(--white);
		transition: all 0.3s; // transition-all test

		&:not(:last-child) {
			margin-bottom: 1.5rem;
		}

		&.is-straight {
			@include vuero-s-card;
		}

		.inner-list {
			padding: 10px 0;

			.inner-list-item {
				+ .inner-list-item {
					margin-top: 24px;
				}
			}
		}
	}

	.is-dark {
		.list-widget {
			@include vuero-card--dark;
		}
	}

	.image-icon {
		height: 40px;
		width: 40px;
	}
</style>
