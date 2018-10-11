<template>
	<div>
		<div class="alert alert-danger d-none" role="alert">
			<span v-if="errors.length">
				<b>Please correct the following error(s):</b>
				<li v-for="error in errors">{{ error }}</li>
			</span>
		</div>

		<div class="loading" v-if="loading">Loading&#8230;</div>

		<div class="row">
			<div class="col-md-6 order-md-6 mb-6">
				<!-- Generate Sub-Documents -->
				<div class="container document" v-if="subDocuments.length > 0">
					<h6>Scan Sub Documents</h6>
					<table class="table">
						<tbody>
							<tr v-for="(row, index) in subDocuments">
								<td><input type="text" class="code form-control form-control-sm border-secondary" v-on:keyup.enter="addSubDocuments(row,index)" v-model="row.document_code" v-focus></td>
								<td><input type="text" class="form-control form-control-sm" v-model="row.document_title" disabled></td>
								<td>
									<a v-on:click="removeSubDocument(row,index);" style="cursor: pointer"><span class="fa fa-trash fa-lg" style="color:red"></span></a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
	        	<!-- End Generate Sub-Documents -->

				<!-- Generate First Route -->
				<div class="container route" v-if="process.length > 0">
					<h6>Route to</h6>
					<table class="table">
						<tbody>
							<tr v-for="(row, index) in process">
								<td>
									<select class="form-control form-control-sm border-secondary" v-model="row.office_id">
										<option value=''></option>
										<option v-for="(value,key) in offices" v-bind:item="value" :value="value.id">
											{{ value.office_name }}
										</option>
									</select>
								</td>
								<td>
									<a v-on:click="removeStep(row, index);" style="cursor: pointer"><span class="fa fa-trash fa-lg" style="color:red"></span></a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- End Generate Sub-Documents -->
			</div>

			<div class="col-md-6 order-md-1">
				<h4>Edit Document</h4>
				
				<div class="form-group">
					<select v-model="list.document_type_id" class="form-control border-secondary">
						<option value=''>Select Document Type</option>
						<option v-for="(value,key) in document_type" v-bind:item="value" :value="value.id">
							{{ value.document_type }}
						</option>
					</select>
				</div>

				<div class="form-group">
					<input type="text" class="form-control" v-model="list.document_code" readonly>
				</div>

				<div class="form-group">
					<textarea type="text" class="form-control border-secondary" placeholder="Title keywords" v-model="list.document_title" rows="5"></textarea>
				</div>

				<div class="form-group">
					<input type="date" class="form-control border-secondary" v-model="list.document_date">
				</div>

				<div class="col-md-12 col-sm-12 col-xs-12 form-group">
					<span class="btn btn-primary" @click="validate">Save</span>
					<span to="/print" class="btn btn-danger" @click="print">Print</span>
					<span class="btn btn-success" @click="addSubDocuments" id="subDoc">Sub-Documents</span>
					<span class="btn btn-success" @click="addSteps">Add Route</span>
				</div>
			</div>
		</div>
	</div>
</template>
<script>

	import VueBarcode from 'vue-barcode';

	export default{
		props: {
			list: {},
			barcode: ''
		},
		data() {
		  return{
			subDocuments: [],
			process: [],
		    errors:{},
			offices:{},
			document_type: {},
			loading: false,
		  }
		},
		components: {
		    'barcode': VueBarcode
		},
		mounted(){
			this.selectDocumentType();
			this.selectOffice();
		},
		directives: {
		  focus: {
		    // directive definition
		    inserted: function (el) {
		      	el.focus()
		    }
		  }
		},
		methods:{
			removeEmptyDocument: function(row, index) {
				if(row.document_code == ''){
					this.subDocuments.splice(index, 1)
				}
			},

			validate(){

				if(this.list.document_title && this.list.document_type_id) return this.save();
				this.errors = [];
				this.$message({
					type: 'error',
					message: 'Fix errors',
				});
				$('.alert').removeClass('d-none')
				if(!this.list.document_title) this.errors.push("Keywords required.");
				if(!this.list.document_type_id) this.errors.push("Document Type required.");
				return false
			},

			save(){
				this.loading = !this.loading
				axios.patch(`/documents/${this.list.id}`,{
					 'document':this.list,
					 'subDocuments':this.subDocuments,
					 'routes' :this.process
				})
				.then((response)=>{
					this.$message({
						type: 'success',
						message: 'Updated successfully',
					});
					this.$emit('closeEdit') 
					this.loading = !this.loading
				 })
				.catch((error)=> this.errors = error.response.data.errors)
			},

			addSubDocuments: function(row, index) {
					$('.document').removeClass('d-none')
				if($('.code').filter(function(){ return this.value == row.code }).length > 1){
				//	alert("Already Scanned")
					row.document_code = ""
					
				}else{
					this.subDocuments.push({
							document_code: "",
							document_title: "",
					});
				}

				//getting sub-documents via document_code
				if(index !== undefined)
				{
					axios.post('get-subdocument', this.subDocuments[index])
					.then((response)=>{ 
						this.subDocuments[index].document_title = response.data.document_title;
					})
					.catch((error)=> this.errors = error.response.data.errors)
				}
	    	},

			addSteps: function(row, index) {
				$('.route').removeClass('d-none')
				this.process.push({
						office_id: "",
				});
			},

			removeStep: function(row, index) {
				this.process.splice(index, 1);
			},

			removeSubDocument: function(row, index) {
				this.subDocuments.splice(index, 1);
			},

			selectDocumentType(){
					axios.post('view-document-types')
					.then((response)=>{ 
					  this.document_type = response.data;
					})
					.catch((error)=> this.errors = error.response.data.errors)
			},

			selectOffice(){
					axios.post('get-offices')
					.then((response)=>{ 
					  this.offices = response.data;
					})
					.catch((error)=> this.errors = error.response.data.errors)
			},

			print: function() {
				this.loading = !this.loading
					axios.post('print-form', {'barcode':this.list.document_code})
					.then((response)=>{
						this.loading = !this.loading
						window.open("/pdf?id="+this.list.document_code+"&title="+this.list.document_title+"&office="+this.$root.user.office_name+"&name="+this.$root.user.user_name)
					})
					.catch((error)=> this.errors = error.response.data.errors)
					
	    	}
		}
	}
</script>
