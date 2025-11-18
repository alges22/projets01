import { unref } from "vue";

export function usePriceFormat() {
	/**
	 * Format Price in FR-XOF Format
	 * @param {number|string} price
	 * @returns {string}
	 */
	const formatPrice = (price: string | Number) => {
		return new Intl.NumberFormat("fr-FR", {
			style: "currency",
			currency: "XOF",
		}).format(unref(price));
	};

	return {
		formatPrice,
	};
}
