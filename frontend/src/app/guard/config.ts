export class Config {
	static get() {
		if (window.location.hostname.indexOf('localhost') > -1 || window.location.hostname.indexOf('127.') > -1) {
			return lokal;
		} else if (window.location.hostname.indexOf('36.92') > -1) {
			return publics;
		} else if (window.location.hostname.indexOf('simrsrsdpagelaran') > -1) {
			return hostinghttps;
		}else if (window.location.hostname.indexOf('192.168.45.60') > -1) {
			return localIp;
		} else {
			return lokalStatic;
		}
	}
}

var lokal = {
	apiBackend: "http://localhost:8300/",
	apiNotif: "http://127.0.0.1:8300/",
	page: 0,
	rows: 10,
	en: {
		firstDayOfWeek: 0,
		dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
		dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
		dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
		monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
		today: 'Today',
		clear: 'Clear',
		dateFormat: 'mm/dd/yy'
	},
	id: {
		firstDayOfWeek: 1,
		dayNames: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
		dayNamesShort: ["Ming", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
		dayNamesMin: ["Mi", "Se", "Sa", "Ra", "Ka", "Ju", "Su"],
		monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
		today: 'Hari ini',
		clear: 'Kosongkan',
		dateFormat: 'dd/mm/yy'
	}
};


var publics = {
	apiBackend: "http://36.92.14.107:8000/",
	apiNotif: "http://36.92.14.107:8000/",
	page: 0,
	rows: 10,
	en: {
		firstDayOfWeek: 0,
		dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
		dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
		dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
		monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
		today: 'Today',
		clear: 'Clear',
		dateFormat: 'mm/dd/yy'
	},
	id: {
		firstDayOfWeek: 1,
		dayNames: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
		dayNamesShort: ["Ming", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
		dayNamesMin: ["Mi", "Se", "Sa", "Ra", "Ka", "Ju", "Su"],
		monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
		today: 'Hari ini',
		clear: 'Kosongkan',
		dateFormat: 'dd/mm/yy'
	}
};
var hosting = {
	apiBackend: "http://api.pcuresijemari.com/",
	apiNotif: "http://api.pcuresijemari.com/",
	page: 0,
	rows: 10,
	en: {
		firstDayOfWeek: 0,
		dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
		dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
		dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
		monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
		today: 'Today',
		clear: 'Clear',
		dateFormat: 'mm/dd/yy'
	},
	id: {
		firstDayOfWeek: 1,
		dayNames: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
		dayNamesShort: ["Ming", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
		dayNamesMin: ["Mi", "Se", "Sa", "Ra", "Ka", "Ju", "Su"],
		monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
		today: 'Hari ini',
		clear: 'Kosongkan',
		dateFormat: 'dd/mm/yy'
	}
};
var hostinghttps = {
	apiBackend: "https://api.simrsrsdpagelaran.com/",
	apiNotif: "https://api.simrsrsdpagelaran.com/",
	page: 0,
	rows: 10,
	en: {
		firstDayOfWeek: 0,
		dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
		dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
		dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
		monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
		today: 'Today',
		clear: 'Clear',
		dateFormat: 'mm/dd/yy'
	},
	id: {
		firstDayOfWeek: 1,
		dayNames: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
		dayNamesShort: ["Ming", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
		dayNamesMin: ["Mi", "Se", "Sa", "Ra", "Ka", "Ju", "Su"],
		monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
		today: 'Hari ini',
		clear: 'Kosongkan',
		dateFormat: 'dd/mm/yy'
	}
};
var lokalStatic = {
	apiBackend: "http://10.10.10.200:8000/",
	apiNotif: "",
	page: 0,
	rows: 10,
	en: {
		firstDayOfWeek: 0,
		dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
		dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
		dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
		monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
		today: 'Today',
		clear: 'Clear',
		dateFormat: 'mm/dd/yy'
	},
	id: {
		firstDayOfWeek: 1,
		dayNames: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
		dayNamesShort: ["Ming", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
		dayNamesMin: ["Mi", "Se", "Sa", "Ra", "Ka", "Ju", "Su"],
		monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
		today: 'Hari ini',
		clear: 'Kosongkan',
		dateFormat: 'dd/mm/yy'
	}
};

var localIp = {
	apiBackend: "http://192.168.45.60:8300/",
	apiNotif: "",
	page: 0,
	rows: 10,
	en: {
		firstDayOfWeek: 0,
		dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
		dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
		dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
		monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
		today: 'Today',
		clear: 'Clear',
		dateFormat: 'mm/dd/yy'
	},
	id: {
		firstDayOfWeek: 1,
		dayNames: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
		dayNamesShort: ["Ming", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
		dayNamesMin: ["Mi", "Se", "Sa", "Ra", "Ka", "Ju", "Su"],
		monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
		today: 'Hari ini',
		clear: 'Kosongkan',
		dateFormat: 'dd/mm/yy'
	}
};
