<script setup lang="ts">
	import { storeToRefs } from "pinia";
	import { useSidebar } from "/@src/stores/sidebar";

	export type SidebarTheme = "default" | "color" | "color-curved" | "curved" | "float" | "labels" | "labels-hover";

	const { panelOpen } = storeToRefs(useSidebar());

	defineEmits(["close"]);

	const props = withDefaults(
		defineProps<{
			theme?: SidebarTheme;
			isOpen?: boolean;
		}>(),
		{
			theme: "default",
		},
	);

	const themeClasses = computed(() => {
		switch (props.theme) {
			case "color":
				return "is-colored";
			case "labels":
				return "has-labels";
			case "labels-hover":
				return "has-labels has-hover-labels";
			case "float":
				return !props.isOpen ? "is-float" : "is-float is-bordered";
			case "curved":
				return !props.isOpen ? "is-curved" : "";
			case "color-curved":
				return !props.isOpen ? "is-colored is-curved" : "is-colored";
			default:
				return "";
		}
	});
</script>

<template>
	<div class="main-sidebar is-colored" :class="[themeClasses, panelOpen ? 'is-panel-open' : 'is-panel-close']">
		<div class="sidebar-brand">
			<div
				class="vuero-hamburger nav-trigger push-resize has-text-white"
				tabindex="0"
				role="button"
				@keydown.space.prevent="panelOpen = !panelOpen"
				@click="panelOpen = !panelOpen"
			>
				<span class="menu-toggle has-chevron">
					<span class="icon-box-toggle">
						<span class="rotate">
							<i aria-hidden="true" class="icon-line-top"></i>
							<i aria-hidden="true" class="icon-line-center"></i>
							<i aria-hidden="true" class="icon-line-bottom"></i>
						</span>
					</span>
				</span>
			</div>
		</div>
		<div class="sidebar-inner">
			<div class="naver"></div>

			<ul class="icon-menu has-slimscroll">
				<slot key="icon" name="links"></slot>
			</ul>

			<!-- User account -->
			<ul class="bottom-menu">
				<slot name="bottom-links"></slot>
			</ul>
		</div>
	</div>
	<Transition name="slide-x">
		<div v-if="panelOpen" class="sidebar-panel is-generic">
			<div class="subpanel-header">
				<div class="sidebar-brand">
					<RouterLink to="/">
						<img alt="logo" src="/images/logos/logo/logo_anatt.png" />
					</RouterLink>
				</div>
				<div
					class="header-panel-close is-dark-primary"
					tabindex="0"
					role="button"
					@keydown.space.prevent="$emit('close')"
					@click="panelOpen = false"
				>
					<i aria-hidden="true" class="fa-solid fa-xmark fa-2xl"></i>
				</div>
			</div>
			<div class="inner" data-simplebar>
				<ul>
					<slot key="text" name="links"></slot>
				</ul>
			</div>
		</div>
	</Transition>
</template>

<style lang="scss">
	@import "/@src/scss/layout/sidebar-panel";

	.main-sidebar {
		position: fixed;
		top: 0;
		inset-inline-start: 0;
		margin-inline-start: 0;
		height: 100vh;
		width: 50px;
		background-color: var(--body-color);
		box-shadow: none;
		z-index: 35;
		transition:
			border-radius 0.3s ease-in,
			background-color 0.3s ease-in,
			top 0.3s ease-in,
			margin-inline-start 0.3s ease-in,
			height 0.3s ease-in;

		&.is-bordered {
			border-inline-end: 1px solid var(--fade-grey) !important;
		}

		&.is-open {
			box-shadow: 2px 0 2px 0 rgb(0 0 0 / 2%);
		}

		&.is-curved {
			&:not(.is-bordered) {
				border-start-end-radius: 20px;
				border-end-end-radius: 20px;
				border-inline-end: 1px solid var(--fade-grey-dark-4) !important;

				.sidebar-brand {
					border-start-end-radius: 20px;
				}
			}
		}

		&.is-colored {
			border-color: var(--landing-yyy-light-2);
			background: var(--primary);

			.sidebar-inner {
				.naver {
					background: var(--white);
				}

				.icon-menu,
				.bottom-menu {
					li {
						a {
							.sidebar-svg {
								color: var(--white);
							}
						}
					}

					.profile-dropdown {
						.status-indicator {
							border-color: var(--primary);
						}
					}
				}
			}
		}

		&.has-labels {
			&.has-hover-labels {
				.sidebar-inner {
					.icon-menu,
					.bottom-menu {
						li {
							&:hover {
								a {
									&::after {
										opacity: 1 !important;
									}
								}
							}

							a {
								&.is-active,
								&.router-link-exact-active {
									&::after {
										opacity: 1 !important;
									}
								}

								&::after {
									opacity: 0;
								}
							}
						}
					}
				}
			}

			.sidebar-inner {
				.icon-menu,
				.bottom-menu {
					overflow-x: hidden;

					li {
						a {
							&.router-link-exact-active {
								&::after {
									color: var(--primary);
								}
								i {
									color: var(--white);
								}
							}

							&::after {
								content: attr(data-content);
								position: absolute;
								bottom: -8px;
								inset-inline-start: -29px;
								inset-inline-end: 0;
								margin: 0 auto;
								font-family: var(--font);
								font-size: 0.5rem;
								font-weight: 500;
								color: var(--light-text);
								text-transform: uppercase;
								text-align: center;
								//width: 80px;
								transition: opacity 0.3s;
							}

							svg {
								position: relative;
								top: -4px;
							}
						}
					}
				}
			}
		}

		&.is-float {
			border-radius: 1000px;
			overflow: hidden;
			width: 74px;
			margin-inline-start: 6px;
			top: 6px;
			height: calc(100vh - 12px);
			border: none !important;

			&:not(.is-bordered) {
				box-shadow: var(--light-box-shadow);
			}

			&.is-bordered {
				width: 100%;
				margin-inline-start: 0;
				top: 0;
				height: 100vh;
				border-radius: 0;

				.sidebar-brand {
					width: 100%;
				}

				.sidebar-inner {
					.icon-menu,
					.bottom-menu {
						li {
							width: 100%;
						}
					}
				}
			}

			.sidebar-brand {
				width: 74px;
			}

			.sidebar-inner {
				.icon-menu,
				.bottom-menu {
					li {
						width: 74px;
					}
				}

				.bottom-menu {
					padding-bottom: 4px;
				}
			}
		}

		.sidebar-brand {
			width: 100%;
			height: 50px;
			display: flex;
			align-items: center;
			justify-content: center;

			img {
				margin-top: 5px;
				// width: 36px;
				// height: auto;
			}
		}

		.sidebar-inner {
			height: calc(100% - 50px);
			width: 100%;
			position: relative;

			.naver {
				position: absolute;
				top: -150px;
				inset-inline-start: 0;
				height: 60px;
				width: 4px;
				border-radius: 100px;
				background: var(--primary);
				transition: all 0.3s; // transition-all test

				&.is-search-results {
					margin-top: 240px;
				}

				&.from-bottom {
					top: unset !important;
					bottom: -64px;
					margin-top: 0 !important;
				}
			}

			.icon-menu {
				overflow-y: auto;
				overflow-x: hidden;
				//max-height: 400px;
				padding-top: 4rem;
			}

			.icon-menu,
			.bottom-menu {
				li {
					position: relative;
					width: 100%;
					height: 60px;
					display: flex;
					align-items: center;
					justify-content: center;

					&:has(a.router-link-exact-active) {
						background: var(--white);
					}

					.is-messages-counter {
						position: absolute;
						top: 6px;
						inset-inline-end: 16px;
						display: block;
						line-height: 17px;
						background: var(--danger);
						color: var(--white);
						font-weight: 500;
						font-size: 0.6rem;
						border-radius: 100px;
						border: 1px solid var(--white);
						transform: scale(0.8);
					}

					a {
						display: block;
						position: relative;
						transform: rotate(calc(var(--transform-direction) * 0));
						opacity: 1;
						transition: all 0.3s; // transition-all test

						&.is-selected,
						&.router-link-exact-active,
						&.router-link-active {
							.sidebar-svg {
								color: var(--primary) !important;
							}
						}

						.sidebar-svg {
							//height: 20px;
							//width: 20px;
							color: var(--title-grey);
							stroke-width: 1.6px;
							transition: all 0.3s; // transition-all test
						}

						&.is-opened {
							transform: rotate(calc(var(--transform-direction) * 360deg));
							opacity: 0;
						}

						&.is-inactive {
							transform: rotate(calc(var(--transform-direction) * -360deg));
							opacity: 0;
						}
					}
				}
			}

			.bottom-menu {
				position: absolute;
				bottom: 0;
				padding: 0;

				li {
					display: flex;
					justify-content: center;
					align-items: center;
					cursor: pointer;

					&.is-rotate {
						a:hover {
							animation: rotating 1s linear infinite;
						}
					}

					a {
						display: flex;
						justify-content: center;
						align-items: center;
					}

					.dropdown {
						position: relative;
						display: block;
						height: 48px;
						width: 48px !important;

						> img {
							height: 48px;
							width: 48px;
							border-radius: var(--radius-rounded);
							position: relative;
							z-index: 1;
						}

						.status-indicator {
							display: block;
							position: absolute;
							top: 0;
							inset-inline-end: 0;
							width: 14px;
							height: 14px;
							border-radius: var(--radius-rounded);
							border: 2px solid var(--white);
							background: var(--success);
							z-index: 2;
						}
					}
				}

				.profile-dropdown {
					.dropdown-menu {
						bottom: 0;
						inset-inline-start: 145%;

						.dropdown-content {
							padding-top: 0;
							overflow: hidden;

							.dropdown-head {
								display: flex;
								align-items: center;
								padding: 20px 16px;
								margin-bottom: 12px;
								background: #fafafa;

								.meta {
									margin-inline-start: 12px;
									font-family: var(--font);

									span {
										display: block;

										&:first-child {
											font-size: 1.1rem;
											font-weight: 500;
											color: var(--dark-text);
											line-height: 1.2;
										}

										&:nth-child(2) {
											text-transform: uppercase;
											color: var(--light-text);
											font-size: 0.7rem;
										}
									}
								}
							}

							.logout-button {
								svg {
									color: var(--smoke-white) !important;
								}
							}
						}

						.dropdown-item {
							display: flex;
							justify-content: flex-start;
							align-items: center;
							width: 100% !important;

							&:hover,
							&:focus:not(.is-button) {
								background: var(--fade-grey-light-3);
							}
						}

						.flex-item {
							display: flex;
							justify-content: space-between;
							align-items: center;
							margin: 0 15px;

							.toggle-title {
								font-size: 0.8rem;
								color: var(--muted-grey);

								&:hover,
								&:focus {
									background: transparent !important;
								}
							}

							&:hover {
								background: transparent !important;
							}
						}
					}
				}
			}
		}
	}

	.is-dark {
		.main-sidebar:not(.is-colored) {
			background: var(--dark-sidebar);

			&.is-bordered {
				border-inline-end: 1px solid var(--dark-sidebar) !important;
			}

			&.is-curved {
				&:not(.is-bordered) {
					border-color: var(--dark-sidebar-light-16) !important;
				}
			}

			.naver {
				background: var(--primary);
			}

			.icon-menu,
			.bottom-menu {
				li {
					a {
						&.is-selected,
						&.router-link-active {
							svg {
								color: var(--primary) !important;
							}

							.sidebar-icon .path {
								fill: var(--primary) !important;
							}
						}

						&:hover {
							svg {
								color: var(--primary) !important;
							}

							.sidebar-icon .path {
								fill: var(--primary) !important;
							}
						}

						.sidebar-icon.is-active .path {
							fill: var(--primary) !important;
						}
					}

					svg {
						color: var(--primary-grey-light-3);
					}

					.status-indicator {
						border-color: var(--dark-sidebar) !important;
					}
				}
			}

			.profile-dropdown {
				.dropdown-content {
					background: var(--dark-sidebar);

					.dropdown-head {
						background: var(--dark-sidebar-light-2) !important;

						&:hover,
						&:focus {
							background: var(--dark-sidebar-light-2) !important;
						}

						.meta {
							&:hover {
								background: var(--dark-sidebar-light-2) !important;
							}

							span {
								&:first-child {
									color: var(--dark-dark-text) !important;
								}
							}
						}
					}

					.dropdown-item {
						color: var(--white);

						&:hover,
						&:focus:not(.is-button) {
							background: var(--dark-sidebar-light-5);
						}

						&.is-button {
							&:hover,
							&:focus {
								background: var(--dark-sidebar-light-2) !important;
							}
						}
					}

					.dropdown-divider {
						background: var(--dark-sidebar-light-5);
					}
				}
			}

			.sidebar-inner {
				.icon-menu,
				.bottom-menu {
					li {
						a {
							&.is-active {
								i {
									color: var(--primary) !important;
								}
							}
						}
					}
				}
			}
		}

		.main-sidebar {
			&.is-colored {
				// background: var(--primary-dark-2);
				// border-color: var(--primary-dark-2) !important;

				.sidebar-inner {
					.naver {
						opacity: 0.8;
					}

					.icon-menu,
					.bottom-menu {
						li {
							a {
								&:hover,
								&.active {
									.sidebar-svg {
										color: var(--smoke-white);
										stroke: var(--smoke-white);
										opacity: 1;
									}
								}

								.sidebar-svg {
									color: var(--smoke-white-light-2);
									opacity: 0.5;
								}
							}
						}
					}
				}

				.profile-dropdown {
					.status-indicator {
						border-color: var(--primary-dark-2) !important;
					}

					.dropdown-menu {
						.dropdown-content {
							.dropdown-head {
								background: var(--dark-sidebar-light-2) !important;

								&:hover,
								&:focus {
									background: var(--dark-sidebar-light-2) !important;
								}

								.meta {
									&:hover {
										background: var(--dark-sidebar-light-2) !important;
									}

									span {
										&:first-child {
											color: var(--dark-dark-text) !important;
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
