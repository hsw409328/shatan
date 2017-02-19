/**
 * 
 */
function getDays() {
	var date = new Date();
	var y = date.getFullYear();
	var m = date.getMonth() + 1;
	if (m == 2) {
		return y % 4 == 0 ? 29 : 28;
	} else if (m == 1 || m == 3 || m == 5 || m == 7 || m == 8 || m == 10
			|| m == 12) {
		return 31;
	} else {
		return 30;
	}
}