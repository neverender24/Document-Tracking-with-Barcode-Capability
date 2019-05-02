<template>
	<div class="container">
		<h4>Receiving</h4>
		<div class="loading" v-if="loading">Loading&#8230;</div>

		<el-input placeholder="Enter barcode no." v-model="barcode" @change="enterRoute()" autofocus="true">
			<template slot="prepend">||||</template>
		</el-input>
		<hr>

		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text" id="basic-addon1"><div class="fa fa-search"></div></span>
			</div>
			<input type="text" class="form-control" v-model="tableData.search" placeholder="Search" @input="initReceive()">
			<select v-model="tableData.length" @change="initReceive()">
				<option value="15" selected="selected">15</option>
				<option value="25">25</option>
				<option value="50">50</option>
			</select>
		</div>

		<datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
			<tbody>
				<tr v-for="item in documents" :key="item.barcode">
					<td>{{ item.released_by.name }}</td>
					<td>{{ item.release_at }}</td>
					<!-- <td>{{ item.office.office_prefix }}</td>  -->
					<td>{{ item.receive_at }}</td>
					<!-- <td>{{ item.received_by.name }}</td> -->
					<td>{{ item.barcode }}</td>
					<td>
							<!-- {{ item.document.document_title == null ? '':item.document.document_title.substr(0,50) }} -->
					</td>
				</tr>
			</tbody>
		</datatable>
		<pagination :pagination="pagination"
			@prev="initReceive(pagination.prevPageUrl)" 
			@next="initReceive(pagination.nextPageUrl)"
		></pagination>

	</div>
	
</template>

<script>
	import Datatable from '../helpers/datatable.vue';
	import Pagination from '../helpers/pagination.vue';

	export default
	{
		components: {
			datatable: Datatable,
			pagination: Pagination
		},
		data(){
			let sortOrders = {};

			let columns = [
				{ width: '20%', label: 'Released by', name: 'Released by'},
				// { width: '25%', label: 'Release to', name: 'Release to'},
				{ width: '15%', label: 'Released on', name: 'Released on'},
				{ width: '15%', label: 'Received on', name: 'Received on'},
				// { width: '7%', label: 'Received by', name: 'Received by'},
				{ width: '15%', label: 'Barcode', name: 'Barcode'},
				{ width: '35%', label: 'Document Title', name: 'Document Title'}
			]

			columns.forEach((column)=>{
				sortOrders[column.name] = -1;
			})

			return {
				columns: columns,
				sortKey: 'document_code',
				sortOrders: sortOrders,
				tableData: {
					draw: 0,
					length: 15,
					search: '',
					column: 0,
					dir: 'desc',
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
				documents:[],
				barcode: '',
				loading: false,
				temp: '',
			}
		},
		mounted(){
			this.initReceive();
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
			sortBy(key) {
				this.sortKey = key;
				this.sortOrders[key] = this.sortOrders[key] * -1;
				this.tableData.column = this.getIndex(this.columns, 'name', key);
				this.tableData.dir = this.sortOrders[key]===1 ?'asc' : 'desc';
				this.initReceive()
			},

			getIndex(array, key, value) {
				return array.findIndex(i=>i[key]==value)
			},

			enterRoute(){
				axios.post('/store-receive',{'barcode':this.barcode}).then((response)=> 
					{
						let val = response.data
						if ( val == 0) {
							this.$message({
								type: 'error',
								message: 'I can\'t find the document'
							});
							this.barcode = ''
						} else {
							this.$message({
								type: 'success',
								message: 'Received successfully',
							});
							this.initReceive()
							this.barcode = ''
						}
					})
				.catch((error)=> this.errors = error.response.data.errors)
			},

			initReceive(url = 'view-receive'){
				this.loading = !this.loading
				axios.get(url, {params: this.tableData})
				.then(response => {
					this.loading = !this.loading;

					let data = response.data

					if (this.tableData.draw == data.draw) {
						this.documents = data.data.data
						this.configPagination(data.data)
					}
				})
			}
	    }
	}
</script>