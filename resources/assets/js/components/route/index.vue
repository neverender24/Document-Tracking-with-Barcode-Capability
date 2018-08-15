<template>
	<div class="container">
		<h4>Route</h4>
		
		<div class="loading" v-if="loading">Loading&#8230;</div>

		<table class="table table-striped">
				<thead>
					<tr>
						<th>Released by</th>
						<th>Release to</th>
						<th>Received at</th>
						<th>Received by</th>
						<th>Released at</th>
						<th>barcode</th>
						<th>Title</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="item,key in temp">
						<td>{{ item.released_by == null ? '':item.released_by.name }}</td>
						<td>{{ item.office == null ? '':item.office.office_prefix }}</td>
						<td>{{ item.receive_at }}</td>
						<td>{{ item.received_by == null ? '':item.received_by.name }}</td>
						<td>{{ item.release_at }}</td>
						<td>{{ item.barcode }}</td>
						<td>{{ item.document_title }}</td>
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
				barcode: '',
	      loading: false,
	      temp: '',
				steps:{
					office_id:''
				}
	    }
	  },
	  mounted(){
			this.loading = !this.loading
	  	axios.post('view-routes',{'barcode':this.$root.document.barcode})
			.then((response)=> {
				this.loading = !this.loading
				this.lists = this.temp = response.data})
			.catch((error)=> this.errors = error.response.data.errors);
	  }
	}
</script>
