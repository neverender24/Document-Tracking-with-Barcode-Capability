<template>
	<div class="container">
		
		<el-row :gutter="20">
			<el-col :span="5"><div class="grid-content"><h4>Documents</h4></div></el-col>
			<el-col :span="1" :offset="16">
				<div class="grid-content"><el-button type="primary" icon="el-icon-plus" @click="openAddDocument = true" round size="small">Add</el-button>
				</div>
			</el-col>
		</el-row>

		<el-tabs v-model="activeName">
			<el-tab-pane label="All" name="first">
				
				<!-- <router-link>All Documents <router-link to="/add-documents" class="btn btn-round btn-primary btn-sm">Add</router-link></router-link> -->
				<div class="loading" v-if="loading">Loading&#8230;</div>
				<vue-good-table
				:columns="columns"
				:rows="allDocuments"
				:search-options="{enabled: true,}"
				styleClass="vgt-table condensed"
				:pagination-options="{
					enabled: true,
					perPage: 10, 
				}">
					<template slot="table-row" slot-scope="props">
						<span v-if="props.column.field == 'action'">
							<div class="btn-group" role="group" aria-label="Basic example">
								<el-button size="mini" type="success" icon="el-icon-tickets" @click="getRoute(props.row.document_code)">Routes</el-button>
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
			</el-tab-pane>

			<el-tab-pane label="Personal" name="second">
				
		<!-- <h4>My Documents <router-link to="/add-documents" class="btn btn-round btn-primary btn-sm">Add</router-link></h4> -->
		<div class="loading" v-if="loading">Loading&#8230;</div>

				<vue-good-table
				:columns="columns"
				:rows="myDocuments"
				styleClass="vgt-table condensed"
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
								<div class="btn-group" role="group">
									<!-- <router-link to="/edit-document" v-on:click.native="edit(props.row.id, props.row.document_code)" class="btn btn-warning btn-sm">Edit </router-link> -->
									<el-button size="mini" type="success" icon="el-icon-tickets" @click="edit(props.row.id, props.row.document_code)">Edit</el-button>
									<el-button size="mini" type="success" icon="el-icon-tickets" @click="getRoute(props.row.document_code)">Routes</el-button>
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
				
			</el-tab-pane>
		</el-tabs>

		<el-dialog
			title="ROUTES"
			custom-class="routeModal"
			:visible.sync="openRoutes"
			width="85%">
			<route-index :routes="routeData" :title="routeTitle"></route-index>
			<span slot="footer" class="dialog-footer">
				<el-button type="primary" @click="openRoutes = false">Close</el-button>
			</span>
		</el-dialog>

		<el-dialog
			:close-on-click-modal="false" 
			:close-on-press-escape ="false"
			custom-class="routeModal"
			:visible.sync="openAddDocument"
			width="75%">
			<add-document></add-document>
			<span slot="footer" class="dialog-footer">
				<el-button type="primary" @click="openAddDocument=false">Close</el-button>
			</span>
		</el-dialog>

		<el-dialog
			:close-on-click-modal="false" 
			:close-on-press-escape ="false"
			custom-class="routeModal"
			:visible.sync="openEditDocument"
			width="75%">
			<edit-document :list="editData"></edit-document>
			<span slot="footer" class="dialog-footer">
				<el-button type="primary" @click="openEditDocument = false">Close</el-button>
			</span>
		</el-dialog>

	</div>

</template>
<style>
.routeModal {
	height: auto;
}
</style>

<script>
	import RouteIndex from './../route/index.vue';
	import AddDocument from './../document/create.vue';
	import EditDocument from './../document/edit.vue';

	export default
	{
	  components: {
		  RouteIndex,
		  AddDocument,
		  EditDocument
	  },
	  data() {
	    return {
			activeName: 'first',
			openRoutes: false,
			openAddDocument: false,
			openEditDocument: false,
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
			loading: false,
			allDocuments: [],
			myDocuments: [],
			routeData: {},
			routeTitle: '',
			document_id:'',
			barcode:'',
			editData:{}
	    }
	  },

	  mounted(){
		this.getAllDocuments();
		this.getMyDocuments();
		this.getUser();
	  },
	  methods:{
		  	edit(id, barcode) {

				this.document_id = id;
				this.barcode = barcode;

				this.loading = !this.loading
				axios.post('/edit-documents', {id})
				.then(response => {
					this.loading = !this.loading;
					this.editData = response.data
				})

				this.openEditDocument = true
			},
	        getAllDocuments() {
	          this.loading = !this.loading
	            axios.get('all-documents')
	            .then(response => {
	              this.loading = !this.loading;
	              this.allDocuments = response.data
	            })
			},
			
			getMyDocuments() {
	          this.loading = !this.loading
	            axios.get('view-documents')
	            .then(response => {
	              this.loading = !this.loading;
	              this.myDocuments = response.data
	            })
	        },
			getRoute(barcode){
				this.loading = !this.loading
				axios.post('view-routes',{'barcode':barcode})
					.then((response)=> {
						this.loading = !this.loading
						this.routeData = response.data
						this.routeTitle = response.data[0].document.document_title
					})
					.catch((error)=> this.errors = error.response.data.errors);
			
				this.openRoutes = true
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