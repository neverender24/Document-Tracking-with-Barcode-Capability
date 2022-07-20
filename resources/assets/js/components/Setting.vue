<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Display Options</h4>
                    </div>
                    <hr />
                    <div class="panel-body">
                        <div class="form-group mb-4">
                            <label class="col-md-4 control-label"
                                >Alternate Account Name</label
                            >
                            <div class="col-md-6">
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="list.alter_name"
                                />
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label class="col-md-5 control-label"
                                >Preferred account name to print: </label
                            >
                                <div class="col-md-6">
                                    <input type="radio" v-model="list.preferred_name" value="0" v-bind:value="0"> Default
                                    <input type="radio" class="ml-3" v-model="list.preferred_name" value="1" v-bind:value="1"> Alternate
                       
                                </div>
                                 </div>
                        <div class="form-group mb-3">
                            <div class="col-md-6 col-md-offset-4">
                                <button class="btn btn-primary" @click="save()">
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            list: {
                preferred_name: null,
                alter_name: null
            },
            errors: {},
        };
    },
    mounted() {
        axios.post("/edit-settings").then((response) => {
            if (response.data) {
                this.list = response.data;
            }
        });
    },
    methods: {
        save() {
            axios
                .post("/update-settings", this.$data.list)
                .then((response) => {
                    this.$message({
                        type: "success",
                        message: "Password successfully changed",
                    });
                    // this.$router.push({ path: 'documents' })
                    location.reload()
                })
                .catch((error) => {
                    this.errors = error.response;
                    this.$message({
                        type: "error",
                        message: this.errors.data.message,
                    });
                });
        },
    },
};
</script>
