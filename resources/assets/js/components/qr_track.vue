<template>
        <div class="table-responsive">
        <route-index v-if="barcode" :routes="routeData" :title="routeTitle"></route-index>
        </div>
</template>

<script>
import RouteIndex from "./route/index";

export default {
    components: {
        RouteIndex
    },
    directives: {
        focus: {
            inserted: function(el) {
                el.focus();
            }
        }
    },
    data() {
        return {
            barcode: "",
            routeData: {},
            routeTitle: ""
        };
    },
    mounted(){
        this.barcode = this.$attrs.barcode
        this.getRoute()
    },
    methods: {
        getRoute() {
            this.loading = !this.loading;
            axios
                .post("fast-track", { barcode: this.barcode })
                .then(response => {
                    this.routeData = response.data;
                    this.routeTitle = response.data[0].document.document_title;
                })
                .catch(error => (this.errors = error.response.data.errors));
        },
        close() {
            this.barcode = ""
        }
    }
};
</script>