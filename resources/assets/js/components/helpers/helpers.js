export const Helpers = {
    print: function (barcode, title, office, name) {

        window.open(
            "/pdf?id=" +
            barcode +
            "&title=" +
            title.replace("&", "%26") +
            "&office=" +
            office +
            "&name=" +
            name
        );
    },

    /**
     * Calculate time the document will stay in specific office
     */
    calculateDate(released, received) {
        var rel = new Date(released);
        var rec = new Date(received);
        var seconds = (rel.getTime() - rec.getTime()) / 1000; //1440516958

        return seconds;
    },

    secondsToHms(timestamp) {
        timestamp = Number(timestamp);
        var h = Math.floor(timestamp / 3600);
        var m = Math.floor((timestamp % 3600) / 60);
        var s = Math.floor((timestamp % 3600) % 60);

        
        var hDisplay = h > 0 ? h + (h == 1 ? ":" : ":") : "";
        var mDisplay = m > 0 ? m + (m == 1 ? ":" : ":") : "";
        var sDisplay = s > 0 ? s + (s == 1 ? "" : "") : "";

        return hDisplay + mDisplay + sDisplay;
    },

    calc(routes) {
        var sum = 0;
        var first = true;
        var firstRecord = 0;
        var self = this;
        _.forEach(routes, function (key, index) {
            if (first) {
                first = false;
                firstRecord = self.calculateDate(
                    key.receive_at,
                    key.release_at
                );
            }

            if (index != 0) {
                sum += self.calculateDate(
                    key.release_at,
                    routes[index - 1].receive_at
                );
            }
        });

        return this.secondsToHms(firstRecord + sum);
    },

    /**
     * Identify color of type
     */
    identifyType(type, prefix) {
        if (type == 14) { //payroll
            return "<span class='badge badge-primary'>" + prefix + "</span>"
        } else if (type == 15) { //memo
            return "<span class='badge badge-warning'>" + prefix + "</span>"
        } else if (type == 32) { //obr
            return "<span class='badge badge-success'>" + prefix + "</span>"
        } else { //others
            return "<span class='badge badge-secondary'>" + prefix + "</span>"
        }
    }




};