<script setup lang="ts">
	const { y } = useWindowScroll();

	const isScrolling = computed(() => {
		return y.value > 30;
	});

	defineProps({
		col: {
			type: Number,
			required: false,
			default: 11,
		},
	});

	defineEmits(["submit"]);
</script>

<template>
	<div class="page-content-inner">
		<div class="form-content-wrapper">
			<div class="columns is-centered">
				<div class="column" :class="'is-' + col">
					<Transition name="translate-page-y" mode="out-in">
						<form class="form-content-box is-form is-footerless" @submit.prevent="$emit('submit')">
							<div class="form-head stuck-header" :class="[isScrolling && 'is-stuck']">
								<div class="form-head-inner">
									<slot name="form-head-inner"></slot>
								</div>
							</div>
							<div class="form-body">
								<slot name="form-body"></slot>
							</div>
						</form>
					</Transition>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="scss">
	@import "/@src/scss/abstracts/all";

	.is-navbar {
		.form-content-wrapper {
			margin-top: 30px;
		}
	}

	.form-content-wrapper {
		padding-bottom: 60px;

		.form-content-box {
			@include vuero-s-card;

			&.is-navigation {
				.media-flex-center {
					padding-bottom: 20px;

					.flex-meta {
						span {
							&:first-child {
								font-size: 1.3rem;
							}
						}
					}
				}

				.form-content-menu {
					.form-content-menu-item {
						display: flex;
						align-items: center;
						padding: 12px 16px;
						border: 1px solid transparent;
						border-radius: 8px;
						margin-bottom: 5px;
						transition: all 0.3s; // transition-all test

						&.router-link-exact-active {
							box-shadow: var(--light-box-shadow);
							border-color: var(--fade-grey-dark-3);

							span,
							i {
								color: var(--primary);
							}

							.end {
								display: block;
							}
						}

						&:not(.router-link-exact-active) {
							&:hover {
								background: var(--fade-grey-light-3);
							}
						}

						i {
							margin-inline-end: 8px;
							font-size: 1.1rem;
							color: var(--light-text);

							&.fas,
							.fal,
							.far {
								font-size: 0.9rem;
							}
						}

						span {
							font-family: var(--font-alt);
							font-size: 0.95rem;
							color: var(--dark-text);
						}

						.end {
							margin-inline-start: auto;
							display: none;
						}
					}
				}
			}

			&.is-form {
				padding: 0;

				&.is-footerless {
					padding-bottom: 20px;
				}

				.form-head,
				.form-foot {
					padding: 12px 20px;

					.form-head-inner,
					.form-foot-inner {
						display: flex;
						align-items: center;
						justify-content: space-between;
					}
				}

				.form-head {
					border-bottom: 1px solid var(--fade-grey-dark-3);
					transition: all 0.3s; // transition-all test

					&.is-stuck {
						background: var(--white);
						padding-inline-end: 80px;
						border-inline-start: 1px solid var(--fade-grey-dark-3);
					}

					.left {
						h3 {
							font-family: var(--font-alt);
							font-size: 1.2rem;
							line-height: 1.3;
						}

						p {
							font-size: 0.95rem;
						}
					}
				}

				.form-foot {
					border-top: 1px solid var(--fade-grey-dark-3);
				}

				.form-body {
					padding: 20px;

					.fieldset {
						padding: 20px 0;
						max-width: 480px;
						margin: 0 auto;

						.fieldset-heading {
							margin-bottom: 20px;

							h4 {
								font-family: var(--font-alt);
								font-weight: 600;
								font-size: 1rem;
							}

							p {
								font-size: 0.9rem;
							}
						}

						.v-avatar {
							position: relative;
							display: block;
							margin: 0 auto;

							.edit-button {
								position: absolute;
								bottom: 0;
								inset-inline-end: 0;
							}
						}

						.setting-list {
							.setting-form {
								text-align: center;

								.filepond-profile-wrap {
									margin: 0 auto 10px !important;
								}
							}

							.setting-item {
								display: flex;
								align-items: center;
								margin-bottom: 24px;

								.icon-wrap {
									position: relative;
									display: flex;
									align-items: center;
									justify-content: center;
									width: 50px;
									min-width: 50px;
									height: 50px;
									border-radius: var(--radius-rounded);
									background: var(--fade-grey-light-2);
									border: 1px solid var(--fade-grey-dark-3);
									color: var(--light-text);

									&.has-img {
										border-color: var(--primary);

										img {
											width: 36px;
											min-width: 36px;
											height: 36px;
										}
									}

									i {
										font-size: 1.4rem;
									}
								}

								img {
									display: block;
									width: 50px;
									min-width: 50px;
									height: 50px;
									border-radius: var(--radius-rounded);
									border: 1px solid transparent;
								}

								.meta {
									margin-inline-start: 10px;

									> span {
										font-family: var(--font);
										display: block;

										&:first-child {
											font-family: var(--font-alt);
											font-weight: 600;
											color: var(--dark-text);
											font-size: 0.9rem;
										}

										&:nth-child(2),
										&:nth-child(3) {
											font-size: 0.85rem;
											color: var(--light-text);

											i {
												position: relative;
												top: -2px;
												font-size: 4px;
												margin: 0 6px;
											}
										}

										&:nth-child(3) {
											color: var(--primary);
										}

										span {
											display: inline-block;
										}
									}
								}

								.end {
									margin-inline-start: auto;
								}
							}
						}
					}
				}
			}
		}
	}

	.is-dark {
		.form-content-wrapper {
			.form-content-box {
				@include vuero-card--dark;

				&.is-navigation {
					.form-content-menu {
						.form-content-menu-item {
							&.router-link-exact-active {
								background: var(--dark-sidebar-light-8);
								border-color: var(--dark-sidebar-light-12);

								i,
								span {
									color: var(--primary);
								}
							}

							&:not(.router-link-exact-active) {
								&:hover {
									background: var(--dark-sidebar-light-10);
								}
							}

							span {
								color: var(--dark-dark-text);
							}
						}
					}
				}

				&.is-form {
					.form-head,
					.form-foot {
						border-color: var(--dark-sidebar-light-12);
					}

					.form-head {
						&.is-stuck {
							background: var(--dark-sidebar);
							border-color: var(--dark-sidebar-light-6);
						}

						.left {
							h3 {
								color: var(--dark-dark-text);
							}
						}
					}

					.form-body {
						.fieldset {
							.fieldset-heading {
								h4 {
									color: var(--dark-dark-text);
								}
							}

							.setting-list {
								.setting-item {
									> img,
									> .icon-wrap,
									> .icon-wrap img {
										border-color: var(--dark-sidebar-light-12);
									}

									> .icon-wrap {
										background: var(--dark-sidebar-light-2);
									}

									.meta {
										> span {
											&:nth-child(3) {
												color: var(--primary);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
</style>
