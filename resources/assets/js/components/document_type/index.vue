<template>
	<div class="container">
		
		<h6>Document Types <router-link to="/add-document-type" class="btn btn-round btn-primary btn-sm">Add</router-link></h6>
		
		<span class="is-pulled-right" v-if="loading">
			<i class="has-text-info fa fa-circle-o-notch fa-spin fa-2x fa-fw"></i>
		</span>

		<table class="table table-striped">
				<thead>
					<tr>
						<th>Type</th>
						<th>Prefix</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="item,key in temp">
						<td>{{ item.document_type }}</td>
						<td>{{ item.document_type_prefix }}</td>
						<td><router-link to="/edit-document-type"  @click.native="edit(key)" class="btn btn-primary btn-sm right">Edit</router-link></td>
					</tr>
				</tbody>
		</table>
	</div>
</template>

<script>
	export default
	{
	  data(){
	    return{
	      lists:{},
	      loading: false,
	      temp: '',
	    }
	  },
	  mounted(){
	  	axios.post('view-document-types')
			.then((response)=> {
				this.lists = this.temp = response.data})
			.catch((error)=> this.errors = error.response.data.errors);
	  },
	  methods:{
	        getDocumentType(page) {
	          this.loading = !this.loading
	            if (typeof page === 'undefined') {
	              page = 1;
	            }
	            axios.get('view-document-types')
	            .then(response => {
	              this.loading = !this.loading;
	              this.lists = this.temp = response.data
	              return response;
	            })
	        },
					edit(key){
						this.$root.list = this.temp[key];
						console.log(this.$root.list)
					}
	    }
	}
</script>