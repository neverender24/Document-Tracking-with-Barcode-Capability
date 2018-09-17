<template>
	<div class="container">
		<h5>Released Documents</h5>
		
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
						label: 'Release to',
						field: this.getNullOffice,
					},
					{
						label: 'Received at',
						field: 'receive_at',
						type: 'number',
					},
					{
						label: 'Received by',
						field: this.getNullReceived,
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
						label: 'Released by',
						field: this.getNullReleased,
						filterOptions: {
							enabled: true,
						},
					},
					{
						label: 'barcode',
						field: 'barcode',
						type: 'number',
					},
										{
						label: 'Title',
						field: 'document.document_title',
						type: 'number',
					},
				],
	      lists:{},
	      loading: false,
	      temp: '',
	    }
	  },
	  mounted(){
	  	this.getResults();
	  },
	  methods:{
					getNullReleased(rowObj){
						return rowObj.released_by == null ? '':rowObj.released_by.name
					},
					getNullReceived(rowObj){
						return rowObj.received_by == null ? '':rowObj.received_by.name
					},
					getNullOffice(rowObj){
						return rowObj.office == null ? '':rowObj.office.office_prefix
					},
	        getResults(page) {
	          this.loading = !this.loading
	            if (typeof page === 'undefined') {
	              page = 1;
	            }
	            axios.get('released-documents')
	            .then(response => {
	              this.loading = !this.loading;
	              this.lists = this.temp = response.data
	              return response;
	            })
	        }
	    }
	}
</script>