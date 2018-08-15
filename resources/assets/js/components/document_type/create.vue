<template>
	<div class="container">
		<div class="col-md-8">
			<h3>Add Document Type</h3>

			<div class="form-group">
				<input type="text" class="form-control border-secondary" name="document_type" v-model="list.document_type" placeholder="Document Type">
			</div>

			<div class="form-group">
				<input type="text" class="form-control border-secondary" name="document_type_prefix" v-model="list.document_type_prefix" placeholder="Prefix">
			</div>

			<div class="col-md-12 col-sm-12 col-xs-12 form-group">
			  <span class="btn btn-primary" @click="save">Save</span>
			  <router-link to="/document-types" class="btn btn-danger">Cancel</router-link>
			</div>
		</div>


	</div>
</template>
<script>

	export default{
		data() {
		  return{
		    list:{
		      document_type: '',
					document_type_prefix: '',
		  	},
		    errors:{},
				offices:{}
		  }
		},
		mounted(){
		},
		methods:{
			save(){
			    axios.post('/document-types',this.$data.list).then((response)=> 
			        {
								this.$snotify.success('Document type added');
			          this.$parent.list.push(response.data);
			          this.$parent.list.sort(function(a,b){
			            if(a.created_at > b.created_at){
			              return -1;
			            }else if(a.created_at < b.created_at){
			              return 1;
			            }
			          })
			          this.list = ""
			        })
			      .catch((error)=> this.errors = error.response.data.errors)
			    this.$router.push({ path: 'document-types' })
			}
		}
	}
</script>
