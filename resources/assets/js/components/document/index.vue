<template>
    <div class="container">
        <div class="grid-content">
            <h4>Work Summary</h4>
            <label for="">From : </label><input type="date" v-model="work_summary_filter.from" @change="get_work_summary()">
            <label for="">To : </label><input type="date" v-model="work_summary_filter.to" @change="get_work_summary()">
            <div class="row text-center mt-2" v-if="Object.keys(work_stat).length !== 0">
                <div class="col-4">
                    <div class="alert alert-secondary">
                        <small>Total Received</small>
                        <h4 v-html="work_stat.received"></h4>
                    </div>
                </div>
                <div class="col-4">
                    <div class="alert alert-secondary">
                        <small>Total Released</small>
                        <h4 v-html="work_stat.released"></h4>
                    </div>
                </div>
                <div class="col-4">
                    <div class="alert alert-secondary">
                        <small>Total Returned</small>
                        <h4 v-html="work_stat.returned"></h4>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <el-row :gutter="20">
            <el-col :span="5">
                <div class="grid-content">
                    <h4>Documents</h4>
                </div>
            </el-col>
            <el-col :span="1" :offset="16">
                <div class="grid-content">
                    <el-button
                        type="primary"
                        icon="el-icon-plus"
                        @click="openAddDocument = true"
                        round
                        size="small"
                    >Add</el-button>
                </div>
            </el-col>
        </el-row>

        <el-tabs v-model="activeName" @tab-click="loadMyDocuments">
            <el-tab-pane label="All" name="first">
                <all-documents :refreshDatatable="refreshDatatable"></all-documents>
            </el-tab-pane>

            <el-tab-pane label="Personal" name="second">
                <my-documents v-if="myDocuments" :refreshDatatable="refreshDatatable"></my-documents>
            </el-tab-pane>

            <el-tab-pane label="Returned" name="third" @tab-click="loadReturnedDocuments()">
                <returned-documents v-if="returnedDocuments" :refreshDatatable="refreshDatatable"></returned-documents>
            </el-tab-pane>
        </el-tabs>

        <el-dialog
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            custom-class="routeModal"
            :visible.sync="openAddDocument"
            width="75%"
        >
            <add-document
                @closeCreate="closeCreateModal"
                :list="create"
                :subDocuments="subDocuments"
                :process="process"
            ></add-document>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="openAddDocument=false">Close</el-button>
            </span>
        </el-dialog>
    </div>
</template>


<script>
import MyDocuments from "./../document/MyDocuments.vue";
import ReturnedDocuments from "./../document/ReturnedDocuments.vue";
import AllDocuments from "./../document/AllDocuments.vue";
import AddDocument from "./../document/create.vue";

export default {
    components: {
        MyDocuments,
        AllDocuments,
        ReturnedDocuments,
        AddDocument
    },
    data() {
        return {
            activeName: "first",
            myDocuments: false,
            returnedDocuments: false,
            openAddDocument: false,
            refreshDatatable: false,
            create: {
                document_title: "",
                document_code: this.generateCode(),
                document_date: "",
                document_type_id: ""
            },
            subDocuments: [],
            process: [],
            work_summary_filter: {
                from: "",
                to: "",
            },
            work_stat: {}
        };
    },

    methods: {
        closeCreateModal: function() {
            this.openAddDocument = false;
            this.refreshDatatable = !this.refreshDatatable;
            this.create.document_title = "";
            this.create.document_code =  this.generateCode(),
            this.create.document_date = "";
            this.create.document_type_id = "";
            this.subDocuments = [];
            this.process = [];
        },
        loadMyDocuments(tab) {
            if (tab.label == "Personal") {
                this.myDocuments = true;
            }

            if (tab.label == "Returned") {
                this.returnedDocuments = true;
            }
        },
        randomString(length, chars) {
            var result = "";
            for (var i = length; i > 0; --i)
                result += chars[Math.round(Math.random() * (chars.length - 1))];
            return result;
        },
        generateCode() {
            return this.randomString(
                8,
                "0123456789abcdefghijklmnopqrstuvwxyz" +
                    Date.now()
            );
        },
        get_work_summary() {
            var self = this
            axios.post('/get_work_summary', this.work_summary_filter ).then( response => {
                self.work_stat = response.data
            })
        }
    }
};
</script>