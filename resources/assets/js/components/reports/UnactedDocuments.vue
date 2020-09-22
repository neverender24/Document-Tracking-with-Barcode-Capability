<template>
	<div class="container">
		<div class="loading" v-if="loading">Loading&#8230;</div>
		<h5>Unacted Documents</h5>

		<div class="row">
			<div class="col-md-9">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text" id="basic-addon1"><div class="fa fa-search"></div></span>
					</div>
					<input type="text" class="form-control" v-model="tableData.search" placeholder="Search" @change="getResults()">
					<select v-model="tableData.length" @change="getResults()">
						<option value="10" selected="selected">10</option>
						<option value="20">20</option>
						<option value="30">30</option>
					</select>
				</div>
			</div>
			<div class="col-md-3">
				<input type="date" class="form-control" v-model="tableData.date_from" @change="getResults()">
			</div>
		</div>
		<hr>
		<datatable :columns="columns">
			<tbody>
				<tr v-for="item in lists" :key="item.id">
					<!-- <td>{{ item.office == null ? '':item.office.office_prefix}}</td>
					<td>{{ item.receive_at }}</td>
					<td>{{ item.received_by == null ? '':item.received_by.name }}</td>
					<td>{{ item.release_at }}</td>
					<td>{{ item.released_by == null ? '':item.released_by.name }}</td>
					<td>{{ item.barcode }}</td>
					<td>{{ item.document == null ? '':item.document.document_title }}</td> -->
					<td>{{ item.document_code }}</td>
					<td>{{ item.document_title }}</td>
					<td>{{ item.document_type.document_type_prefix }}</td>
				</tr>
			</tbody>
		</datatable>
		<pagination :pagination="pagination"
		 @prev="getResults(pagination.prevPageUrl)" 
		 @next="getResults(pagination.nextPageUrl)"
		 ></pagination>
	</div>
</template>

<script>
	import Datatable from '../helpers/datatable.vue';
	import Pagination from '../helpers/pagination.vue';

	export default
	{
		components:{
			datatable: Datatable,
			pagination: Pagination
		},
	  data(){
		  let sortOrders = {};

		  let columns = [
			  { width: '10%', label: 'Code', name: 'Release to'},
			  { width: '60%', label: 'Title', name: 'Received at'},
			  { width: '40%', label: 'Type', name: 'Received by'},
		  ]


	    return{
			columns: columns,
			tableData: {
				draw: 0,
				length: 10,
				search: '',
				column: 0,
				dir: 'desc',
				date_from: '',
			},
			pagination: {
				lastPage: '',
				currentPage: '',
				total: '',
				lastPageUrl: '',
				nextPageUrl: '',
				prevPageUrl: '',
				from: '',
				to: ''
			},
	      lists:{},
	      loading: false,
			temp: '',
			totalRecords: 0,
	    }
	  },
	  mounted(){
		  this.getResults();
		  console.log(this.pagination)
	  },
	  methods:{
			  
			configPagination(data) {
				this.pagination.lastPage = data.last_page
				this.pagination.currentPage = data.current_page
				this.pagination.total = data.total
				this.pagination.lastPageUrl = data.last_page_url
				this.pagination.prevPageUrl = data.prev_page_url
				this.pagination.nextPageUrl = data.next_page_url
				this.pagination.from = data.from
				this.pagination.to = data.to
			},

	        getResults(url = 'unacted_documents') {

			  	this.loading = !this.loading
			  
				this.tableData.draw++;
	            axios.get(url, {params: this.tableData})
	            .then(response => {
				  this.loading = !this.loading;

					let data = response.data

					if (this.tableData.draw == data.draw) {
						this.lists = data.data.data
						this.configPagination(data.data)
					}
					
	            })
			},
			
	    }
	}
</script>