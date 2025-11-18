<template>
	<VDropdown icon="ellipsis-vertical" spaced right>
		<template #content>
			<a role="menuitem" href="#" class="dropdown-item is-media">
				<div class="icon">
					<i aria-hidden="true" class="fa-light fa-reload"></i>
				</div>
				<div class="meta">
					<span>Reload</span>
					<span>Refresh search results</span>
				</div>
			</a>

			<a role="menuitem" href="#" class="dropdown-item is-media">
				<div class="icon">
					<i aria-hidden="true" class="fa-light fa-save"></i>
				</div>
				<div class="meta">
					<span>Save</span>
					<span>Save this search</span>
				</div>
			</a>

			<hr class="dropdown-divider" />

			<a role="menuitem" href="#" class="dropdown-item is-media">
				<div class="icon">
					<i aria-hidden="true" class="fa-light fa-cog"></i>
				</div>
				<div class="meta">
					<span>Settings</span>
					<span>configuration options</span>
				</div>
			</a>
		</template>
	</VDropdown>
</template>
