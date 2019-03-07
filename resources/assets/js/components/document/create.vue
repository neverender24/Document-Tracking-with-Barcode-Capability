<template>
    <div>
        <div class="alert alert-danger d-none" role="alert">
            <span v-if="errors.length">
                <b>Please correct the following error(s):</b>
                <li v-for="error in errors">{{ error }}</li>
            </span>
        </div>

        <div class="loading" v-if="loading">Loading&#8230;</div>

        <div class="row">
            <div class="col-md-6 order-md-6 mb-6">
                <!-- Generate Sub-Documents -->
                <div class="container document" v-if="subDocuments.length > 0">
                    <h6>
                        <strong>
                            <em>Sub Documents (Scan Barcode)</em>
                        </strong>
                    </h6>
                    <table class="table">
                        <tbody>
                            <tr v-for="(row, index) in subDocuments">
                                <td>
                                    <input
                                        type="text"
                                        class="code form-control form-control-sm border-secondary"
                                        v-on:keyup.enter="addSubDocuments(row,index)"
                                        v-model="row.code"
                                        v-focus
                                    >
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        v-model="row.title"
                                        readonly
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
                <div class="container route" v-if="process.length > 0">
                    <h6>
                        <strong>
                            <em>Route to (Select Office)</em>
                        </strong>
                    </h6>
                    <table class="table">
                        <tbody>
                            <tr v-for="(row, index) in process">
                                <td>
                                    <select
                                        class="form-control form-control-sm border-secondary"
                                        v-model="row.office_id"
                                    >
                                        <option value></option>
                                        <option
                                            v-for="(value,key) in offices"
                                            v-bind:item="value"
                                            :value="value.id"
                                        >{{ value.office_prefix }} - {{ value.office_name }}</option>
                                    </select>
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
                <h4>Add Document</h4>
                <div class="form-group">
                    <select v-model="list.document_type_id" class="form-control border-secondary">
                        <option value="0">Select Document Type</option>
                        <option
                            v-for="(value,key) in document_type"
                            :value="value.id"
                        >{{ value.document_type }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <input type="text" class="form-control" v-model="list.document_code" readonly>
                </div>
                <div class="form-group">
                    <textarea
                        type="text"
                        class="form-control border-secondary"
                        placeholder="Title keywords"
                        v-model="list.document_title"
                        rows="5"
						@change="removeChars(list.document_title)"
                    ></textarea>
                </div>

                <div class="form-group">
                    <input
                        type="date"
                        class="form-control border-secondary"
                        v-model="list.document_date"
                    >
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                    <span class="btn btn-primary" @click="validate">Save</span>
                    <span to="/print" class="btn btn-danger" @click="print">Print</span>
                    <span class="btn btn-success" @click="addSubDocuments" id="subDoc">Sub-Documents</span>
                    <span class="btn btn-success" @click="addSteps" id="sub">Add Route</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        list: {},
        subDocuments: "",
        process: ""
    },

    data() {
        return {
            errors: {},
            offices: {},
            document_type: {},
            loading: false
        };
    },

    mounted() {
        this.selectDocumentType();
        this.selectOffice();
    },

    directives: {
        focus: {
            inserted: function(el) {
                el.focus();
            }
        }
    },

    methods: {
        validate() {
            let checkRoute = false;

            var filteredProcess = this.process.filter(function(pr) {
                if (pr.office_id) {
                    return true;
                }
            });
            if (filteredProcess.length) {
                checkRoute = true;
            }

            if (
                this.list.document_title &&
                this.list.document_type_id &&
                checkRoute
            )
                return this.save();
            this.errors = [];
            this.$message({
                type: "error",
                message: "Fix errors"
            });
            $(".alert").removeClass("d-none");
            if (!this.list.document_title)
                this.errors.push("Keywords required.");
            if (!this.list.document_type_id)
                this.errors.push("Document Type required.");
            if (!checkRoute)
                this.errors.push("There should be at least 1 route.");
            return false;
        },

        save() {
            this.loading = !this.loading;
            axios
                .post("/documents", [
                    this.list,
                    this.subDocuments,
                    this.process
                ])
                .then(response => {
                    this.$message({
                        type: "success",
                        message: "Added successfully"
                    });

                    this.loading = !this.loading;
                    //this.print();
                    this.$emit("closeCreate");
                })
                .catch(error => {});
        },

        addSubDocuments: function(row, index) {
            if (
                $(".code").filter(function() {
                    return this.value == row.code;
                }).length > 1
            ) {
                row.code = "";
            } else {
                this.subDocuments.push({
                    code: "",
                    title: ""
                });
            }

            //getting sub-documents via document_code
            if (index !== undefined) {
                axios
                    .post("get-subdocument", this.subDocuments[index])
                    .then(response => {
                        this.subDocuments[index].title =
                            response.data.document_title;
                    })
                    .catch(error => (this.errors = error.response.data.errors));
            }
        },

        addSteps: function(row, index) {
            this.process.push({
                office_id: ""
            });
        },

        removeStep: function(index) {
            this.process.splice(index, 1);
        },

        removeSubDocument: function(index) {
            this.subDocuments.splice(index, 1);
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
        },

        print: function() {
            this.loading = !this.loading;
            window.open(
                "/pdf?id=" +
                    this.list.document_code +
                    "&title=" +
                    this.list.document_title +
                    "&office=" +
                    this.$root.user.office_name +
                    "&name=" +
                    this.$root.user.user_name
            );
            this.loading = !this.loading;
        },

		removeChars(text) {
			var regexp = new RegExp('#([^\\s]*)','g');
			this.list.document_title = text.replace(regexp, 'No.');
		}
    }
};
</script>
