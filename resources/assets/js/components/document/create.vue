<template>
	<div class="container">

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
					<div class="container document d-none">
							<h6><strong><em>Sub Documents (Scan Barcode)</em></strong></h6>
							<table class="table">
									<tbody>
											<tr v-for="(row, index) in subDocuments">
													<td><input type="text" class="code form-control form-control-sm border-secondary" @blur="removeEmptyDocument(row,index)" v-on:keyup.enter="addSubDocuments(row,index)" v-model="row.code" v-focus></td>
													<td><input type="text" class="form-control form-control-sm" v-model="row.title" readonly></td>
													<td>
															<a v-on:click="removeSubDocument(index);" style="cursor: pointer"><span class="fa fa-trash fa-lg" style="color:red"></span></a>
													</td>
											</tr>
									</tbody>
							</table>
					</div>

					<!-- End Generate Sub-Documents -->

					<!-- Generate First Route -->
						<div class="container route d-none">
							<h6><strong><em>Route to (Select Office)</em></strong></h6>
							<table class="table">
									<tbody>
											<tr v-for="(row, index) in process">
													<td>
														<select class="form-control form-control-sm border-secondary" v-model="row.office_id">
															<option value=''></option>
															<option v-for="(value,key) in offices" v-bind:item="value" :value="value.id">
																{{ value.office_prefix }} - {{ value.office_name }}
															</option>
														</select>
													</td>
													<td>
															<a v-on:click="removeStep(index);" style="cursor: pointer"><span class="fa fa-trash fa-lg" style="color:red"></span></a>
													</td>
											</tr>
									</tbody>
							</table>
						</div>
					<!-- End Generate Sub-Documents -->
          
        </div>

		<div class="col-md-6 order-md-1">
			<h4>Add Document</h4>
			
			<div class="form-group">
        <select v-model="list.document_type_id" class="form-control border-secondary">
          <option value='0'>Select Document Type</option>
          <option v-for="(value,key) in document_type" v-bind:item="value" :value="value.id" >
						{{ value.document_type }}
          </option>
        </select>
      </div>

			<div class="form-group">
				<input type="text" class="form-control" v-model="list.document_code" readonly>
			</div>
			<div class="form-group">
				<barcode v-bind:value="list.document_code">
			    	Scan barcode!
			  	</barcode>
			</div>

			<div class="form-group" >
				<textarea type="text" class="form-control border-secondary" placeholder="Title keywords" v-model="list.document_title" rows="5"></textarea>
			</div>

			<div class="form-group">
				<input type="date" class="form-control border-secondary" v-model="list.document_date">
			</div>

			

			<div class="col-md-12 col-sm-12 col-xs-12 form-group">
			  <span class="btn btn-primary" @click="validate">Save</span>
			  <span to="/print" class="btn btn-danger" @click="print">Print</span>
				<router-link to="/view-documents" class="btn btn-danger">Cancel</router-link>
			  <span class="btn btn-success" @click="addSubDocuments" id="subDoc">Sub-Documents</span>
				<span class="btn btn-success" @click="addSteps" id="sub">Add Route</span>
			</div>
		</div>
	</div>
	</div>
</template>
<script>

	import VueBarcode from 'vue-barcode';

	export default{
		data() {
		  return{
		    list:{
		      document_title: '',
		      document_code: Math.floor(Math.random() * 26) + Date.now(), 
		      document_date: '',
					document_type_id: '',
		  	},
		  	barcodeValue: 'test',
				subDocuments: [],
				process: [],
		    errors:{},
				offices:{},
				office:'',
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
				if(row.code == ''){
					this.subDocuments.splice(index, 1)
				}
			},
			validate(){
					let checkRoute = false;

					var filteredProcess= this.process.filter(function(pr){
							if(pr.office_id){ return true }
						})
					if(filteredProcess.length){checkRoute = true}


					if(this.list.document_title && this.list.document_type_id && checkRoute) return this.save();
					this.errors = [];
					this.$snotify.error('Fix some errors')
					$('.alert').removeClass('d-none')
					if(!this.list.document_title) this.errors.push("Keywords required.");
					if(!this.list.document_type_id) this.errors.push("Document Type required.");
					if(!checkRoute) this.errors.push("There should be at least 1 route.");
					return false
			},
			save(){		
			    axios.post('/documents',[this.$data.list, this.subDocuments, this.process]).then((response)=> 
			        {
								this.$router.push({ path: 'view-documents' })
								this.$snotify.success('Document Added Successfully')
			          this.$parent.list.push(response.data);
			          this.$parent.list.sort(function(a,b){
			            if(a.created_at > b.created_at){
			              return -1;
			            }else if(a.created_at < b.created_at){
			              return 1;
			            }
			          })
			          this.list = ""
								
			        })
			      .catch((error)=> {
						//	this.errors = error.response.data.errors
						//	this.$snotify.error('Fix some errors')
						})
			},
			addSubDocuments: function(row, index) {
				//remove empty input
				if(row.code == ''){
					this.subDocuments.splice(index, 1)
				}

				$('.document').removeClass('d-none')
				if($('.code').filter(function(){ return this.value == row.code }).length > 1){
				//	alert("Already Scanned")
					row.code = ""
					
				}else{
					this.subDocuments.push({
							code: "",
							title: "",
					});
				}

				var elem = document.createElement('tr');

				//getting sub-documents via document_code
				if(index !== undefined)
				{
					axios.post('get-subdocument', this.subDocuments[index])
					.then((response)=>{ 
						this.subDocuments[index].title = response.data.document_title;
					})
					.catch((error)=> this.errors = error.response.data.errors)
				}
	    },
			addSteps: function(row, index) {
				$('.route').removeClass('d-none')
				this.process.push({
						office_id: "",
				});
				var elem = document.createElement('tr');
			},
			removeStep: function(index) {
				if(this.process.length == 1){
						$('.route').addClass('d-none')
					}
					this.process.splice(index, 1);
			},
			removeSubDocument: function(index) {
				if(this.subDocuments.length == 1){
						$('.document').addClass('d-none')
					}
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
