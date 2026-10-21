jQuery(function ($) {

    /*Customization of the elementor flatpickr for jalali calendar*/
    if (typeof flatpickr !== "undefined") {
        var Persian = {
            firstDayOfWeek: 6,
            weekdays: {
                shorthand: ['یک', 'دو', 'سه', 'چهار', 'پنج', 'جمعه', 'شنبه'],
                longhand: [
                    "یک‌شنبه",
                    "دوشنبه",
                    "سه‌شنبه",
                    "چهارشنبه",
                    "پنچ‌شنبه",
                    "جمعه",
                    "شنبه",
                ],
            },
            months: {
                shorthand: [
                    "فروردین",
                    "اردیبهشت",
                    "خرداد",
                    "تیر",
                    "مرداد",
                    "شهریور",
                    "مهر",
                    "آبان",
                    "آذر",
                    "دی",
                    "بهمن",
                    "اسفند",
                ],
                longhand: [
                    "فروردین",
                    "اردیبهشت",
                    "خرداد",
                    "تیر",
                    "مرداد",
                    "شهریور",
                    "مهر",
                    "آبان",
                    "آذر",
                    "دی",
                    "بهمن",
                    "اسفند",
                ],
            },
            ordinal: function () {
                return "";
            },
            amPM: ["صبح", "بعدازظهر"]
        };

        flatpickr.l10ns.fa = Persian;
        flatpickr.localize(flatpickr.l10ns.fa);
    }

});
