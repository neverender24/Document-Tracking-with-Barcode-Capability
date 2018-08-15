<template>  
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Register</div>
                    <div class="panel-body">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Old Password</label>
                                <div class="col-md-6">
                                    <input type="password" class="form-control" v-model="list.old_password">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Password</label>
                                <div class="col-md-6">
                                    <input type="password" class="form-control"  v-model="list.new_password">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Confirm Password</label>
                                <div class="col-md-6">
                                    <input type="password" class="form-control"  v-model="list.confirm_password">
                                </div>
                            </div>
                            <div class="form-group">
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

	export default{
		data() {
		  return{
		    list:{
		      old_password:'',
              new_password:'',
              confirm_password: ''
		  	},
		    errors:{},
		  }
		},
		mounted(){
		},
		methods:{
			save(){
			    axios.post('/update-password',this.$data.list).then((response)=> 
			        {
					  this.$snotify.success('Password successfully changed');
			          this.list = ""
                    this.$router.push({ path: 'documents' })
			        })
			      .catch(error=> {
                      this.errors = error.response
                      this.$snotify.error(this.errors.data.message);
                })
			}
		}
	}
</script>
