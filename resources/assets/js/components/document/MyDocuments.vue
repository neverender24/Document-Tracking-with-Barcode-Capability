<template>
    <div class>
        <div class="loading" v-if="loading">Loading&#8230;</div>

        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <div class="fa fa-search"></div>
                </span>
            </div>
            <input
                type="text"
                class="form-control"
                v-model="tableData.search"
                placeholder="Search"
                @change="getMyDocuments()"
                @input="resetSearch()"
            >
            <select v-model="tableData.length" @change="getMyDocuments()">
                <option value="10" selected="selected">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
            <tbody>
                <tr
                    v-for="(item, index) in documents"
                    :key="item.document_code"
                    @click="getRoute(item.document_code)"
                >
                    <td>{{ calc(item.routes, index) }}</td>
                    <td>{{ item.document_code }}</td>
                    <td>{{ item.document_title }}</td>
                    <td style="text-align:center;" v-html="identifyType(item.document_type_id, item.document_type.document_type_prefix)"></td>
                    <td>{{ item.created_at | moment("MMM-DD-YY hh:mmA") }}</td>
                    <td @click="$event.stopPropagation()">
                        <div class="btn-group" role="group">
                            <button
                                @click="print(item.id, item.document_code,item.document_title)"
                                class="btn btn-sm btn-info"
                            >
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16">
                                      <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
                                      <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                                    </svg>
                                </span>
                            </button>
                            <button
                                @click="edit(item.id, item.document_code)"
                                v-if="item.routes.length <= 1 && item.routes[0].receive_at==null"
                                class="btn btn-sm btn-info"
                            >
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                      <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                      <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                    </svg>
                                </span>
                            </button>
                            <button v-else class="btn btn-sm btn-info" disabled>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                      <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                      <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                    </svg>
                                </span>
                            </button>

                            <button
                                @click="deleteDocument(item.document_code)"
                                v-if="item.routes.length <= 1 && item.routes[0].receive_at==null"
                                class="btn btn-sm btn-danger"
                            >
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                      <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z"/>
                                    </svg>
                                </span>
                            </button>
                            <button v-else class="btn btn-sm btn-danger" disabled>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                      <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z"/>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </datatable>
        <div class="py-3 d-flex flex-row align-items-center justify-content-between">
			<pagination
				:pagination="pagination"
				@prev="getMyDocuments(pagination.prevPageUrl)"
				@next="getMyDocuments(pagination.nextPageUrl)" 
			></pagination>
			<p>{{ pagination.from }} to {{pagination.to}} of {{ pagination.total }} entries</p>
		</div>

        <el-dialog title="ROUTES" custom-class="routeModal" :visible.sync="openRoutes" width="85%">
            <route-index :routes="routeData" @deleteRoute="getRoute" :title="routeTitle"></route-index>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="openRoutes = false">Close</el-button>
            </span>
        </el-dialog>

        <el-dialog
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            custom-class="routeModal"
            :visible.sync="openEditDocument"
            width="75%"
        >
            <edit-document :list="editData" :barcode="barcode" @closeEdit="closeEditModal"></edit-document>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="openEditDocument = false">Close</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<style>
.routeModal {
    height: auto;
}
.el-dialog__body {
    padding-top: 0px;
}
</style>

<script>
import RouteIndex from "./../route/index.vue";
import EditDocument from "./../document/edit.vue";
import Datatable from "../helpers/datatable.vue";
import Pagination from "../helpers/pagination.vue";
import { Helpers } from "../helpers/helpers.js";

export default {
    props: {
        refreshDatatable: ""
    },

    components: {
        RouteIndex,
        EditDocument,
        datatable: Datatable,
        pagination: Pagination
    },

    data() {
        let sortOrders = {};

        let columns = [
            { width: "15%", label: "Time", name: "Time" },
            { width: "10%", label: "Barcode", name: "Barcode" },
            { width: "40%", label: "Document Title", name: "document_title" },
            { width: "10%", label: "Type", name: "Type" },
            { width: "20%", label: "Created On", name: "Created On" },
            { width: "5%", label: "Action", name: "Action" }
        ];

        columns.forEach(column => {
            sortOrders[column.name] = -1;
        });

        return {
            columns: columns,
            sortKey: "document_code",
            sortOrders: sortOrders,
            tableData: {
                draw: 0,
                length: 10,
                search: "",
                column: 0,
                dir: "desc"
            },
            pagination: {
                lastPage: "",
                currentPage: "",
                total: "",
                lastPageUrl: "",
                nextPageUrl: "",
                prevPageUrl: "",
                from: "",
                to: ""
            },
            openRoutes: false,
            openEditDocument: false,
            loading: false,
            documents: [],
            myDocuments: [],
            routeData: {},
            routeTitle: "",
            document_id: "",
            barcode: "",
            editData: {}
        };
    },

    created() {
        this.getMyDocuments();
    },

    watch: {
        refreshDatatable: function() {
            this.getMyDocuments();
        }
    },

    methods: {
        configPagination(data) {
            this.pagination.lastPage = data.last_page;
            this.pagination.currentPage = data.current_page;
            this.pagination.total = data.total;
            this.pagination.lastPageUrl = data.last_page_url;
            this.pagination.prevPageUrl = data.prev_page_url;
            this.pagination.nextPageUrl = data.next_page_url;
            this.pagination.from = data.from;
            this.pagination.to = data.to;
        },
        sortBy(key) {
            this.sortKey = key;
            this.sortOrders[key] = this.sortOrders[key] * -1;
            this.tableData.column = this.getIndex(this.columns, "name", key);
            this.tableData.dir = this.sortOrders[key] === 1 ? "asc" : "desc";
            this.getAllDocuments();
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value);
        },

        closeEditModal: function() {
            this.openEditDocument = false;
            this.getMyDocuments();
        },

        edit(id, barcode) {
            this.document_id = id;
            this.barcode = barcode;

            this.loading = !this.loading;
            axios.post("/edit-documents", { id }).then(response => {
                this.loading = !this.loading;
                this.editData = response.data;
                this.openEditDocument = true;
            });
        },

        print: function(id, barcode, title) {
            this.loading = !this.loading;
            Helpers.print(
                barcode,
                title,
                this.$root.user.office_name,
                this.$root.user.user_name
            );
            this.loading = !this.loading;
        },

        deleteDocument(id, barcode) {
            this.document_id = id;
            this.barcode = barcode;
            this.loading = !this.loading;

            this.$confirm(
                "This will permanently delete the record. Continue?",
                "Warning",
                {
                    confirmButtonText: "OK",
                    cancelButtonText: "Cancel",
                    type: "warning"
                }
            )
                .then(() => {
                    axios.post("/delete-document", { id }).then(response => {
                        this.getMyDocuments();
                        this.$message({
                            type: "success",
                            message: "Delete completed"
                        });
                        this.loading = !this.loading;
                    });
                })
                .catch(() => {
                    this.$message({
                        type: "info",
                        message: "Delete canceled"
                    });
                    this.loading = !this.loading;
                });
        },

        getMyDocuments(url = "view-documents") {
            this.loading = !this.loading;
            axios.get(url, { params: this.tableData }).then(response => {
                this.loading = !this.loading;

                let data = response.data;

                if (this.tableData.draw == data.draw) {
                    this.documents = data.data.data;
                    this.configPagination(data.data);
                }
            });
        },

        getRoute(barcode) {
            this.loading = !this.loading;
            axios
                .post("view-routes", { barcode: barcode })
                .then(response => {
                    this.loading = !this.loading;
                    this.routeData = response.data;
                    this.routeTitle = response.data[0].document.document_title;
                    this.openRoutes = true;
                })
                .catch(error => (this.errors = error.response.data.errors));
        },

        calc(routes) {
            return Helpers.calc(routes);
        },

        identifyType(type, prefix) {
            return Helpers.identifyType(type, prefix)
        },
        resetSearch() {
            if (this.tableData.search == '') {
                this.getMyDocuments()
            }
        }
    }
};
</script>