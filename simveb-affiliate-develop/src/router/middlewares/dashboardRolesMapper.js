import { useAuthStore } from "@/stores/auth.js";
import AffiliateSidebar from "@/layouts/AffiliateSidebar.vue";
import PoliceSidebar from "@/layouts/PoliceSidebar.vue";
import InterpolSidebar from "@/layouts/InterpolSidebar.vue";
import AuctioneerSidebar from "@/layouts/AuctioneerSidebar.vue";
import GarageSidebar from "@/layouts/GarageSidebar.vue";
import Cookies from "js-cookie";
import ApprovedSidebar from "@/layouts/ApprovedSidebar.vue";
import BankSidebar from "@/layouts/BankSidebar.vue";
import GMASidebar from "@/layouts/GMASidebar.vue";
import GMDSidebar from "@/layouts/GMDSidebar.vue";
import CourtSidebar from "@/layouts/CourtSidebar.vue";
import DistributorSidebar from "@/layouts/DistributorSidebar.vue";

/**
 *
 * @returns {Promise<*>}
 */
export const redirectMappedLayout = async () => {
	const store = useAuthStore();
	const online_profile = store.online_profile;

	switch (online_profile.type.code) {
		case "police":
			return PoliceSidebar;
		case "interpol":
			return InterpolSidebar;
		case "auctioneer":
			return AuctioneerSidebar;
		case "central_garage":
			return GarageSidebar;
		case "approved":
			return ApprovedSidebar;
		case "gma":
			return GMASidebar;
		case "gmd":
			return GMDSidebar;
		case "bank":
			return BankSidebar;
		case "court":
			return CourtSidebar;
		case "distributor":
			return DistributorSidebar;
		default:
			// Log him out or redirect him to the login page
			return AffiliateSidebar;
	}
};

/**
 *
 * @returns {object}
 */
export const redirectMappedDashboard = () => {
	const profile = Cookies.get("profile");

	switch (profile) {
		case "police":
			return { name: "police-dashboard" };
		case "interpol":
			return { name: "interpol-dashboard" };
		case "auctioneer":
			return { name: "auctioneer-dashboard" };
		case "central_garage":
			return { name: "central-garage-dashboard" };
		case "approved":
			return { name: "approved-dashboard" };
		case "gma":
			return { name: "gma-dashboard" };
		case "gmd":
			return { name: "gmd-dashboard" };
		case "bank":
			return { name: "court-pledges" };
		case "court":
			return { name: "court-dashboard" };
		case "distributor":
			return { name: "distributor-dashboard" };
		default:
			// Log him out or redirect him to the login page
			return { name: "affiliate-dashboard" };
	}
};
