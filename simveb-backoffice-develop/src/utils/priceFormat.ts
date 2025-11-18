/**
 * Format Price in FR-XOF Format
 * @param {number|string} price
 * @returns {string}
 */
export function formatPrice(price: string | Number) {
	return new Intl.NumberFormat("fr-FR", {
		style: "currency",
		currency: "XOF",
	}).format(price);
}
