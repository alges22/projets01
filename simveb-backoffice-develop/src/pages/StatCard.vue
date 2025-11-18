<template>
	<div class="dashboard-tile">
		<div v-if="title" class="tile-head">
			<h3 class="text-primary">{{ title }}</h3>
			<VDropdown icon="fa-xl fa-solid fa-ellipsis" right color="text-secondary" />
		</div>
		<slot v-else name="head"></slot>
		<div class="tile-body">
			<slot></slot>
		</div>
		<div class="tile-foot">
			<slot name="foot"></slot>
		</div>
	</div>
</template>
<script setup lang="ts">
	defineProps({
		title: { type: String, required: false },
		dropdown: {
			type: Boolean,
			required: false,
			default: true,
		},
	});
</script>
<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.dashboard-tile {
		@include vuero-s-card;
		height: 100%;
		display: flex;
		flex-direction: column;
		margin-right: 12px;
		border-radius: 8px;
		transition: all 0.3s;
		box-shadow: 1px 1px 5px var(--fade-grey-dark-3);

		font-family: var(--font-alt);

		.tile-head {
			display: flex;
			align-items: baseline;
			justify-content: space-between;

			h3 {
				font-family: var(--font-alt);
				color: var(--primary-dark);
				font-weight: 600;
			}
		}

		.tile-body {
			font-size: 2rem;
			padding: 4px 0 8px;
			display: flex;
			justify-content: space-between;
			margin-right: 25%;

			div i {
				color: var(--primary);
			}

			div span {
				@apply font-semibold text-primary-dark;
			}
		}

		.tile-foot {
			margin-top: auto;

			span {
				&:first-child {
					font-weight: 500;

					svg {
						height: 16px;
						width: 16px;
						margin-inline-end: 15px;
						stroke-width: 3px;
					}
				}

				&:nth-child(2) {
					color: var(--light-text);
					font-size: 0.9rem;
				}
			}

			.text-h-primary {
				font-size: 0.9rem;
				color: var(--primary);
				font-weight: 600;
			}
		}
	}
</style>
