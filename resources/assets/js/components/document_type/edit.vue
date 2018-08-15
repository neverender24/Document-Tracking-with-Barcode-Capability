<template>
	<div class="container">
		<div class="col-md-8">
			<h3>Edit Document Type</h3>

			<div class="form-group">
				<input type="text" class="form-control border-secondary" v-model="list.document_type" placeholder="Document Type">
			</div>

			<div class="form-group">
				<input type="text" class="form-control border-secondary" v-model="list.document_type_prefix" placeholder="Prefix">
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
		    list:{},
		    errors:{}
		  }
		},
		mounted(){
			this.list = this.$root.list;
		},
		methods:{
			save()
      {
        axios.patch(`/document-types/${this.list.id}`,this.$data.list).then((response)=> 
					{
						this.$router.push({ path: 'document-types' })
						this.$snotify.success('Document type updated.');
					})
          .catch((error)=> this.errors = error.response.data.errors)
      }
		}
	}
</script>
