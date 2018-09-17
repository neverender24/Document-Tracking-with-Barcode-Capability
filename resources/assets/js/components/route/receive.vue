<template>
	<div class="container">
		<h4>Receiving</h4>
		<div class="loading" v-if="loading">Loading&#8230;</div>

		<el-input placeholder="Enter barcode no." v-model="barcode" @change="enterRoute()" autofocus="true">
			<template slot="prepend">||||</template>
		</el-input>
		<hr>
		<vue-good-table
		:columns="columns"
		:rows="temp"
		styleClass="vgt-table condensed"
		:search-options="{enabled: true,}"
		:pagination-options="{ enabled: true, perPage: 10,}"
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
						label: 'Released by',
						field: 'released_by',
						filterOptions: {
							enabled: true,
						},
					},
					{
						label: 'Release to',
						field: 'office_prefix',
					},
					{
						label: 'Received at',
						field: 'receive_at',
						type: 'date',
						dateInputFormat: 'YYYY-MM-DD',
						dateOutputFormat: 'MMM Do YY',
					},
					{
						label: 'Received by',
						field: 'received_by',
						filterOptions: {
							enabled: true,
						},
					},
					{
						label: 'Released at',
						field: 'release_at',
						filterOptions: {
							enabled: true,
						},
					},
					{
						label: 'Barcode',
						field: 'barcode',
						type: 'number',
					},
					{
						label: 'Document Title',
						field: 'document_title',
					},
				],
	      lists:{},
				barcode: '',
	      loading: false,
	      temp: '',
				steps:{
					office_id:''
				}
	    }
	  },
	  mounted(){
			this.initReceive();
	  },
	  methods:{
					enterRoute(){
						axios.post('/store-receive',{'barcode':this.barcode}).then((response)=> 
							{
									let val = response.data
									if( val == 0){
										this.$snotify.error('Document not found or not logged');
										this.barcode = ''
									}else{
										this.$snotify.success('Document received');
										this.initReceive()
										this.barcode = ''
									}
							})
						.catch((error)=> this.errors = error.response.data.errors)
					//	location.reload();
					},
					initReceive(){
						this.loading = !this.loading
						axios.post('view-receive',{'barcode':this.$root.document.barcode})
						.then((response)=> {
							this.loading = !this.loading
							this.lists = this.temp = response.data})
						.catch((error)=> this.errors = error.response.data.errors);
					}
	    }
	}
</script>