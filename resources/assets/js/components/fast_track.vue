<template>
        <!-- Modal -->
        <div
            class="modal fade"
            id="exampleModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        > 
            <div class="modal-dialog modal-dialog-centered modal-full" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Fast Track</h4>
                    </div>
                    <div class="modal-body">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Enter barcode...then hit Enter"
                            @input="getRoute()"
                            v-model="barcode"
                            v-focus
                        >
                        <route-index v-if="barcode" :routes="routeData" :title="routeTitle"></route-index>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="close()">Close</button>
                    </div>
                </div>
            </div>
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
    methods: {
        getRoute() {
            this.loading = !this.loading;
            axios
                .post("view-routes", { barcode: this.barcode })
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