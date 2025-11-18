<template>
	<div class="mobile-subsidebar">
		<div class="inner">
			<div class="sidebar-title">
				<h3>Véhicules</h3>
			</div>

			<ul class="submenu" data-simplebar>
				<VCollapseLinks>
					<template #header>
						Véhicules
						<i aria-hidden="true" class="fa-light fa-chevron-right" />
					</template>
					<RouterLink to="/vehicle" class="is-submenu">
						<i aria-hidden="true" class="lnil lnil-car"></i>
						<span>Véhicules</span>
					</RouterLink>
				</VCollapseLinks>
				<VCollapseLinks>
					<template #header>
						Configuration
						<i aria-hidden="true" class="fa-light fa-chevron-right" />
					</template>
					<RouterLink to="/admin/vehicle-categories" class="is-submenu">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Catégories de véhicule
					</RouterLink>
					<RouterLink to="/admin/vehicle-types" class="is-submenu">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Types de véhicule
					</RouterLink>
					<RouterLink to="/admin/vehicle-brands" class="is-submenu">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Marques de véhicule
					</RouterLink>
					<RouterLink to="/admin/vehicle-powers" class="is-submenu">
						<i class="fas fa-horse-head pr-2" aria-hidden="true"></i>
						Puissance de véhicule
					</RouterLink>
					<hr class="navbar-divider" />

					<RouterLink to="/glass-types" class="is-submenu">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Types de vitre
					</RouterLink>
					<RouterLink to="/energy-sources" class="is-submenu">
						<i class="lnil lnil-bolt-alt pr-2" aria-hidden="true"></i>
						Sources d'énergie
					</RouterLink>
				</VCollapseLinks>
				<VCollapseLinks>
					<template #header>
						Caractéristiques
						<i aria-hidden="true" class="fa-light fa-chevron-right" />
					</template>

					<RouterLink to="/admin/vehicle-caracteristic-categories" class="is-submenu">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Catégories de caractéristique
					</RouterLink>
					<RouterLink to="/admin/vehicle-caracteristics" class="is-submenu">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Caractéristiques de véhicule
					</RouterLink>
					<RouterLink to="/admin/vehicle-caracteristic-prices" class="is-submenu">
						<!-- <i class="lnil lnil-car pr-2" aria-hidden="true"></i> -->
						Prix des caractéristiques
					</RouterLink>
				</VCollapseLinks>
			</ul>
		</div>
	</div>
</template>
