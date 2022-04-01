<template>
    <div class="container">
        <div class="loading" v-if="loading">Loading&#8230;</div>
        <div class="alert alert-danger d-none" role="alert">
            <span v-if="errors.length">
                <b>Please correct the following error(s):</b>
                <li v-for="error in errors">{{ error }}</li>
            </span>
        </div>

        <div class="row">
            <div class="col-md-6 order-md-6 mb-6">
                <!-- Generate Sub-Documents -->
                <div class="container document" v-if="list.subDocuments.length > 0">
                    <h6 v-if="list.subDocuments.length > 0">
                        Scan Documents
                        <small
                            style="color: red;"
                        >TIPS: Hit enter/barcode to add new document.</small>
                    </h6>
                    <table class="table">
                        <tbody>
                            <tr v-for="(row, index) in list.subDocuments">
                                <td>
                                    <input
                                        type="text"
                                        class="code form-control form-control-sm border-secondary"
                                        v-on:keyup.enter="addSubDocuments(row,index)"
                                        @keyup="checkError(row,index)"
                                        v-model="row.code"
                                        v-focus
                                    >
                                    <small class="text-danger" v-if="row.errors2">{{ row.errors2 }}</small>
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        v-model="row.title"
                                        disabled
                                    >
                                </td>
                                <td>
                                    <a
                                        v-on:click="removeSubDocument(index);"
                                        style="cursor: pointer"
                                    >
                                        <span class="fa fa-trash fa-lg" style="color:red"></span>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- End Generate Sub-Documents -->
                <!-- Generate First Route -->
                <div class="container route" v-if="list.process.length > 0">
                    <h6 v-if="list.process.length > 0">Route to</h6>
                    <table class="table">
                        <tbody>
                            <tr v-for="(row, index) in list.process">
                                <td>
                                    <!-- <select
                                        class="form-control form-control-sm border-secondary"
                                        v-model="row.office_id"
                                    >
                                        <option value></option>
                                        <option
                                            v-for="(value,key) in offices"
                                            v-bind:item="value"
                                            :value="value.id"
                                        >{{ value.office_prefix }} - {{ value.office_name }}</option>
                                    </select>-->
                                    <model-select
                                        :options="searchOfficeSelect"
                                        v-model="row.office_id"
                                        placeholder="select item"
                                    ></model-select>
                                </td>
                                <td>
                                    <a v-on:click="removeStep(index);" style="cursor: pointer">
                                        <span class="fa fa-trash fa-lg" style="color:red"></span>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- End Generate Sub-Documents -->
            </div>

            <div class="col-md-6 order-md-1">
                <h4>Releasing</h4>

                <div style="margin-top: 20px">
                    <el-radio-group v-model="radio" size="small">
                        <el-radio-button label="Good"></el-radio-button>
                        <el-radio-button label="With Remarks"></el-radio-button>
                        <el-radio-button label="Return"></el-radio-button>
                    </el-radio-group>
                </div>

                <div v-if="radio=='With Remarks'">
                    <el-input
                        type="textarea"
                        :autosize="{ minRows: 5, maxRows: 10}"
                        placeholder="Enter remarks"
                        v-model="withRemarks"
                    ></el-input>
                    <p style="color: red;">
                        <small>TIPS: State any remarks separated by semicolon (;). You can type your return remarks here if not available in choices</small>
                    </p>
                </div>

                <div v-if="radio=='Return'">
                    <el-checkbox-group v-model="checkList" v-if="radio=='Return'">
                        <div class="row">
                            <div class="col-6">
                                <el-checkbox label="Incomplete Supporting Documents "></el-checkbox>
                            </div>
                            <div class="col-6">
                                <el-checkbox label="Incomplete Signatories "></el-checkbox>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <el-checkbox label="Uncertified Photocopies "></el-checkbox>
                            </div>
                            <div class="col-6">
                                <el-checkbox label="Inappropriate Charges "></el-checkbox>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <el-checkbox label="Incorrect mathematical computations "></el-checkbox>
                            </div>
                            <div class="col-6">
                                <el-checkbox label="Document not filled out completely "></el-checkbox>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <el-checkbox label="Invalid claims and transactions "></el-checkbox>
                            </div>
                            <div class="col-6">
                                <el-checkbox label="Insufficient fund "></el-checkbox>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <el-checkbox label="Unauthorized signatories "></el-checkbox>
                            </div>
                            <div class="col-6">
                                <el-checkbox label="Excessive claims "></el-checkbox>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <el-checkbox label="Not filled up completely "></el-checkbox>
                            </div>
                            <div class="col-6">
                                <el-checkbox label="Inconsistent details "></el-checkbox>
                            </div>
                        </div>
                    </el-checkbox-group>

                    <el-input
                        type="textarea"
                        :autosize="{ minRows: 5, maxRows: 10}"
                        placeholder="Other reasons"
                        v-model="returnRemarks"
                    ></el-input>
                    <p style="color: red;">
                        <small>TIPS: Append/State any return remarks not stated above.</small>
                    </p>
                </div>
            </div>
        </div>

        <hr>

        <div class="col-md-12 col-sm-12 col-xs-12 form-group" style="padding:0;">
            <span class="btn btn-primary" @click="validate">Save</span>
            <span
                class="btn btn-success"
                @click="addSubDocuments"
                v-if="list.subDocuments.length == 0"
            >Add Document</span>
            <span class="btn btn-success" @click="addSteps">Add Route</span>
        </div>
    </div>
</template>

<style>
.select-office {
    width: auto !important;
}
</style>

<script>
import { ModelSelect } from "vue-search-select";

export default {
    data() {
        return {
            loading: false,
            checkList: [],
            list: {
                remarks: "",
                subDocuments: [],
                process: []
            },
            errors: {},
            offices: [],
            document_type: {},
            radio: "Good",
            withRemarks: "",
            returnRemarks: "",
            errors2: ""
        };
    },
    components: {
        ModelSelect
    },
    computed: {
        searchOfficeSelect: function() {
            var self = this;
            var select = [];

            _.forEach(self.offices, function(e) {
                select.push({
                    value: e.id,
                    text: e.office_prefix
                });
            });

            return select;
        }
    },
    directives: {
        focus: {
            inserted: function(el) {
                el.focus();
            }
        }
    },

    mounted() {
        this.selectDocumentType();
        this.selectOffice();
    },

    methods: {
        validate() {
            let checkRoute = false;
            let checkDocument = false;

            var filteredProcess = this.list.process.filter(function(pr) {
                if (pr.office_id) {
                    return true;
                }
            });
            if (filteredProcess.length) {
                checkRoute = true;
            }

            var filteredDocument = this.list.subDocuments.filter(function(pr) {
                if (pr.code) {
                    return true;
                }
            });
            if (filteredDocument.length) {
                checkDocument = true;
            }

            if (checkRoute && checkDocument) return this.save();
            this.errors = [];
            this.$message({
                type: "error",
                message: "Fix errors"
            });
            $(".alert").removeClass("d-none");

            if (!checkRoute)
                this.errors.push("There should be at least 1 route.");
            if (!checkDocument)
                this.errors.push("There should be at least 1 Document.");
            return false;
        },

        save() {
            this.loading = !this.loading;

            if (this.radio == "Good") {
                this.list.remarks = "Good";
            } else if (this.radio == "With Remarks") {
                this.list.remarks = "With Remarks: " + this.withRemarks;
            } else {
                this.list.remarks =
                    "Returned: " +
                    _.toString(this.checkList) +
                    " " +
                    this.returnRemarks;
            }

            axios
                .post("/release", this.$data.list)
                .then(response => {
                    let err = [];

                    if (response.data.length != 0) {
                        $(".alert").removeClass("d-none");
                        response.data.forEach(element => {
                            err.push(element);
                        });

                        this.errors = err;
                        this.$message({
                            type: "error",
                            message: "Fix some errors."
                        });
                    } else {
                        this.$message({
                            type: "success",
                            message: "Document successfully marked as released."
                        });

                        location.reload();
                    }
                    this.loading = !this.loading;
                })
                .catch(error => console.log(error));
        },

        addSubDocuments: function(row, index) {
            if (
                $(".code").filter(function() {
                    return this.value == row.code;
                }).length > 1
            ) {
                row.code = "";
            } else {
                this.list.subDocuments.push({
                    code: "",
                    title: ""
                });
            }

            this.checkError(row, index);
        },

        checkError: function(row, index) {
            if (index !== undefined) {
                axios
                    .post("get-subdocument", this.list.subDocuments[index])
                    .then(response => {
                        this.list.subDocuments[index].title =
                            response.data.document_title;

                        var length = response.data.routes.length
                        let error = "";
                        
                        _.findLastKey(response.data.routes, function(n, index) {
                            if (index == length-1 && n.receive_by == null) {
                                error = "Not received yet";
                            }
                        });

                        if (!response.data) {
                            error = "Not found! Check Route.";
                        }

                        this.list.subDocuments[index].errors2 = error;
                    })
                    .catch(error => (this.errors = error.response.data.errors));
            }
        },

        addSteps: function(row, index) {
            this.list.process.push({
                office_id: ""
            });
        },

        removeStep: function(index) {
            this.list.process.splice(index, 1);
        },

        removeSubDocument: function(index) {
            this.list.subDocuments.splice(index, 1);
        },

        selectDocumentType() {
            axios
                .post("view-document-types")
                .then(response => {
                    this.document_type = response.data;
                })
                .catch(error => (this.errors = error.response.data.errors));
        },

        selectOffice() {
            axios
                .post("get-offices")
                .then(response => {
                    this.offices = response.data;
                })
                .catch(error => (this.errors = error.response.data.errors));
        }
    }
};
</script>
