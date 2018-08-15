<template>
	<div class="container">
		<h4>All Documents <router-link to="/add-documents" class="btn btn-round btn-primary btn-sm">Add</router-link></h4>
		
		<div class="loading" v-if="loading">Loading&#8230;</div>

		<vue-good-table
		:columns="columns"
		:rows="temp"
		:search-options="{
					enabled: true,
				}"
				:pagination-options="{
					enabled: true,
					perPage: 10, 
				}"
		>
				<template slot="table-row" slot-scope="props">
					<span v-if="props.column.field == 'action'">
						<div class="btn-group" role="group" aria-label="Basic example">
							<router-link to="/view-routes" v-on:click.native="getRoute(props.row.document_code)" class="btn btn-secondary btn-sm">Route </router-link>
						</div>
					</span>
					<span v-if="props.column.field == 'tr'">
							{{ secondsToHms(props.row.routes.map(fields).reduce(sum, 0)) }}
					</span>
					<span v-else>
						{{props.formattedRow[props.column.field]}}
					</span>
				</template>
		</vue-good-table>

		
	</div>

</template>

<script>
	export default
	{
	  data(){
	    return{
				columns: [
					{
						label: 'TR',
						field: 'tr',
					},
					{
						label: 'Code',
						field: 'document_code',
						type: 'number',
					},
					{
						label: 'Title',
						field: 'document_title',
						filterOptions: {
							enabled: true,
						},
					},
					{
						label: 'Type',
						field: 'document_type_prefix',
						filterOptions: {
							enabled: true,
						},
					},
					{
						label: 'Created On',
						field: 'created_at',
						type: 'date',
						dateInputFormat: 'YYYY-MM-DD',
						dateOutputFormat: 'MMM Do YY',
					},
					{
						label: 'Action',
						field: 'action',
					},
				],
	      lists:{},
	      loading: false,
	      temp: [],
	    }
	  },

	  mounted(){
	  	this.getResults();
		this.getUser();
	  },
	  methods:{
	        getResults() {
	          this.loading = !this.loading
	            axios.get('all-documents')
	            .then(response => {
	              this.loading = !this.loading;
	              this.lists = this.temp = response.data
	              return response;
	            })
	        },
					getRoute(barcode){
						this.$root.document.barcode = barcode
					},
					getUser(){
						axios.post('get-user')
							.then((response)=> {
									this.$root.user.office_id = response.data.office_id
									this.$root.user.user_id = response.data.id
								})
							.catch((error)=> this.errors = error.response.data.errors);
					},
					fields(id){
						var rel = new Date(id.updated_at);
						var rec = new Date(id.created_at);
						var seconds = (rel.getTime() - rec.getTime()) / 1000; //1440516958

						return seconds
					},
					sum(item1, item2){
						return item1+item2
					},
					secondsToHms(d) {
							d = Number(d);

							var h = Math.floor(d / 3600);
							var m = Math.floor(d % 3600 / 60);
							var s = Math.floor(d % 3600 % 60);

							return ('0' + h).slice(-2) + ":" + ('0' + m).slice(-2) + ":" + ('0' + s).slice(-2);
					},
	    }
	}
</script>