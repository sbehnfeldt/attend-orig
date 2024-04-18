import $ from 'jquery';

'use strict';

const Attend = {
    loading: 0,
    $loading: $('#loading'),


    loadAnother: function () {
        this.loading++;
        this.$loading.show();
    },

    doneLoading: function () {
        this.loading--;
        if (0 === this.loading) {
            this.$loading.hide();
        }
    },


    getMonday: function (d) {

        let dw = d.getDay();
        switch (dw) {
            case 1:   // Monday: no op
                break;
            case 2:   // Tuesday:
                d.setDate(d.getDate() - 1);
                break;
            case 3: // Wednesday:
                d.setDate(d.getDate() - 2);
                break;
            case 4: // Thursday:
                d.setDate(d.getDate() - 3);
                break;
            case 5:  // Friday:
                d.setDate(d.getDate() + 3);
                break;
            case 6:
                d.setDate(d.getDate() + 2);
                break;
            case 0:
                d.setDate(d.getDate() + 1);
                break;
        }
        return d;
    }
};

Date.prototype.addDays = function (days) {
    var date = new Date(this.valueOf());
    date.setDate(date.getDate() + days);
    return date;
};

export default Attend;
