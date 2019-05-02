<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Add Notes</h4>
                    </div>
                    <hr>
                    <div class="panel-body">
                        <div class="form-group">
                            <div id="app">
                                <ckeditor
                                    :editor="editor"
                                    v-model="editorData"
                                    :config="editorConfig"
                                ></ckeditor>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button class="btn btn-primary" @click="save()">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
  .ck-editor__editable {
    min-height: 500px;
   }
</style>
<script>

import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

export default {
    data() {
        return {
            list: {
                old_password: "",
                new_password: "",
                confirm_password: ""
            },
            errors: {},
            editor: ClassicEditor,
            editorData: "",
            editorConfig: {
                // The configuration of the editor.
            }
        };
    },
    mounted() {
        axios
                .post("/versions")
                .then(response => {
                    this.editorData = response.data.updates
                })
                .catch(error => {
                    this.errors = error.response;
                    this.$message({
                        type: "error",
                        message: this.errors.data.message
                    });
                });
    },
    methods: {
        save() {
            axios
                .post("/updates", { updates: this.editorData })
                .then(response => {
                    this.$message({
                        type: "success",
                        message: "Password successfully changed"
                    });
                })
                .catch(error => {
                    this.errors = error.response;
                    this.$message({
                        type: "error",
                        message: this.errors.data.message
                    });
                });
        }
    }
};
</script>
