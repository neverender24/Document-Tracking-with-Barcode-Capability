<template>
	<div class="">
		<div class="loading" v-if="loading">Loading&#8230;</div>
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text" id="basic-addon1"><div class="fa fa-search"></div></span>
			</div>
			<input type="text" class="form-control" v-model="tableData.search" placeholder="Search" @input="getAllDocuments()">
			<select v-model="tableData.length" @change="getAllDocuments()">
				<option value="15" selected="selected">15</option>
				<option value="25">25</option>
				<option value="50">50</option>
			</select>
		</div>

		<datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
			<tbody>
				<tr v-for="item in documents" :key="item.document_code" @click="getRoute(item.document_code)">
					<td>{{ secondsToHms(item.routes.map(fields).reduce(sum, 0)) }}</td>
					<td>{{ item.document_code }}</td>
					<td>{{ item.document_title }}</td>
					<td v-html="item.document_type_prefix"></td>
					<td>{{ item.created_at }}</td>
				</tr>
			</tbody>
		</datatable>
		<pagination :pagination="pagination"
			@prev="getAllDocuments(pagination.prevPageUrl)" 
			@next="getAllDocuments(pagination.nextPageUrl)"
		></pagination>

		<el-dialog
			title="ROUTES"
			custom-class="routeModal"
			:visible.sync="openRoutes"
			width="85%">
			<route-index :routes="routeData" @deleteRoute="getRoute" :title="routeTitle"></route-index>
			<span slot="footer" class="dialog-footer">
				<el-button type="primary" @click="openRoutes = false">Close</el-button>
			</span>
		</el-dialog>

	</div>
</template>

<style>
	.routeModal {
		height: auto;
	}
	.el-dialog__body{
		padding-top: 0px;
	}
</style>

<script>
	import RouteIndex from './../route/index.vue';
	import Datatable from '../helpers/datatable.vue';
	import Pagination from '../helpers/pagination.vue';

	export default
	{
		props: {
			refreshDatatable:''
		},

		components: {
			RouteIndex,
			datatable: Datatable,
			pagination: Pagination
		},

	  	data() {
		  	let sortOrders = {};

		  	let columns = [
			  { width: '10%', label: 'Time', name: 'Time'},
			  { width: '15%', label: 'Barcode', name: 'Barcode'},
			  { width: '40%', label: 'Document Title', name: 'document_title'},
			  { width: '10%', label: 'Type', name: 'Type'},
			  { width: '15%', label: 'Created On', name: 'Created On'},
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
				loading: false,
				openRoutes: false,
				documents: [],
				routeData: {},
				routeTitle: '',
			}
	  	},

	  	mounted(){
			this.getAllDocuments();
	  	},

		watch:{
		  	refreshDatatable: function() {
				this.getAllDocuments()
			}
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
				this.getAllDocuments()
			},

			getIndex(array, key, value) {
				return array.findIndex(i=>i[key]==value)
			},

			getAllDocuments(url = 'all-documents') {
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
			},

			getRoute(barcode) {
				this.loading = !this.loading
				axios.post('view-routes',{'barcode':barcode})
					.then((response)=> {
						this.loading = !this.loading
						this.routeData = response.data
						this.routeTitle = response.data[0].document.document_title
						this.openRoutes = true
					})
					.catch((error)=> this.errors = error.response.data.errors);
			},

			fields(id) {
				var rel = new Date(id.updated_at);
				var rec = new Date(id.created_at);
				var seconds = (rel.getTime() - rec.getTime()) / 1000; //1440516958

				return seconds
			},
			sum(item1, item2) {
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