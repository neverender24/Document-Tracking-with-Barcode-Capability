<template>
    <div class="container">
        <h4>Receiving</h4>
        <div class="loading" v-if="loading">Loading&#8230;</div>

    <div class="row">
        <div class="col-md-4">
            <input
            type="text"
            class="form-control"
            placeholder="Enter barcode no."
            v-model="barcode"
            @change="enterRoute()"
             v-focus
        >
        </div>
        <div class="col-md-6">
            <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                          <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </div>
                </span>
            </div>
            <input
                type="text"
                class="form-control"
                v-model="tableData.search"
                placeholder="Search"
                @change="initReceive()" 
                @input="resetSearch()"
            />
            <select v-model="tableData.length" @change="initReceive()">
                <option value="15" selected="selected">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
        </div>
            <div class="col-md-2">
                <input type="date" class="form-control" v-model="tableData.date_received" @change="initReceive()">
            </div>
    </div>
        
        <hr />

        

        <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
            <tbody>
                <tr v-for="item in documents" :key="item.barcode">
                    <td>{{ item.released_by.name }}</td>
                    <td>{{ item.release_at }}</td>
                    <!-- <td>{{ item.office.office_prefix }}</td>  -->
                    <td>{{ item.receive_at }}</td>
                    <!-- <td>{{ item.received_by.name }}</td> -->
                    <td>{{ item.barcode }}</td>
                    <td>{{ item.document.document_title == null ? '':item.document.document_title.substr(0,50) }}</td>
                    
                    <td v-if="item.document.document_type_id==30">
                        <button v-if="item.document.approved_po!==null" class="btn btn-default btn-sm" @click="approvedPo(item.document.id, 0)" title="Revert tag approved PO">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard-check" viewBox="0 0 16 16" title="test">
                            <path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                            <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                            <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                            </svg>
                        </button>
                        <button v-else class="btn btn-success btn-sm" @click="approvedPo(item.document.id, 1)" title="Tag as approve PO">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard-check" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                            <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                            <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                            </svg>
                        </button>
                    </td>
                    <td v-else></td>

                    <td v-if="item.document.file_tag==1"><button class="btn btn-success btn-sm" @click="fileMe(item.document.id, 0)" >Unfile</button></td>
                    <td v-else><button class="btn btn-success btn-sm" @click="fileMe(item.document.id, 1)">File</button></td>
                </tr>
            </tbody>
        </datatable>
        <pagination
            :pagination="pagination"
            @prev="initReceive(pagination.prevPageUrl)"
            @next="initReceive(pagination.nextPageUrl)"
        ></pagination>
    </div>
</template>


<script>
import Datatable from "../helpers/datatable.vue";
import Pagination from "../helpers/pagination.vue";

export default {
    components: {
        datatable: Datatable,
        pagination: Pagination
    },
    data() {
        let sortOrders = {};

        let columns = [
            { width: "20%", label: "Released by", name: "Released by" },
            // { width: '25%', label: 'Release to', name: 'Release to'},
            { width: "15%", label: "Released on", name: "Released on" },
            { width: "15%", label: "Received on", name: "Received on" },
            // { width: '7%', label: 'Received by', name: 'Received by'},
            { width: "10%", label: "Barcode", name: "Barcode" },
            { width: "30%", label: "Document Title", name: "Document Title" },
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
                length: 15,
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
            documents: [],
            barcode: "",
            loading: false,
            temp: ""
        };
    },
    mounted() {    
        this.initReceive();
    },
    directives: {
        focus: {
            inserted: function(el) {
                el.focus();
            }
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
            this.initReceive();
        },

        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value);
        },

        enterRoute() {
            axios
                .post("/store-receive", { barcode: this.barcode })
                .then(response => {
                    let val = response.data;
                    if (val == 0) {
                        this.$message({
                            type: "error",
                            message: "I can't find the document"
                        });
                        this.barcode = "";
                    } else {
                        this.$message({
                            type: "success",
                            message: "Received successfully"
                        });
                        this.initReceive();
                        this.barcode = "";
                    }
                })
                .catch(error => (this.errors = error.response.data.errors));
        },

        initReceive(url = "view-receive") {
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
        resetSearch() {
            if (this.tableData.search == '') {
                this.initReceive()
            }
        },
        fileMe(id, tag) {
            axios.post("file_document",{ document_id: id, file_tag: tag } ).then(response => {
                this.initReceive()
            });
        },
        approvedPo(id, tag) {
            axios.post("approve_po",{ document_id: id, approved_po: tag } ).then(response => {
                this.initReceive()
            });
        }
    }
};
</script>