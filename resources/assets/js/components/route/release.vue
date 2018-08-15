<template>
	<div class="container">

		<div class="alert alert-danger d-none" role="alert">
			<span v-if="errors.length">
				<b>Please correct the following error(s):</b>
					<li v-for="error in errors">{{ error }}</li>
			</span>
		</div>

	<div class="row">
        <div class="col-md-6 order-md-6 mb-6">

				<!-- Generate Sub-Documents -->
			<div class="container document d-none">
				<h6>Scan Documents</h6>
				<table class="table">
						<tbody>
								<tr v-for="(row, index) in list.subDocuments">
										<td><input type="text" class="code form-control form-control-sm border-secondary" @blur="removeEmptyDocument(row,index)" v-on:keyup.enter="addSubDocuments(row,index)" v-model="row.code" v-focus></td>
										<td><input type="text" class="form-control form-control-sm" v-model="row.title" disabled></td>
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
				<h6>Route to</h6>
				<table class="table">
							<tbody>
									<tr v-for="(row, index) in list.process">
											<td>
												<select class="form-control form-control-sm border-secondary"  name="offices[]" v-model="row.office_id" v-focus>
													<option value=''></option>
													<option v-for="(value,key) in offices" v-bind:item="value" :value="value.id">
														{{ value.office_name }}
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
					<h3>Releasing</h3>
			<div class="form-group">
				<textarea type="text" class="form-control border-secondary" v-model="list.remarks" placeholder="Enter Remarks" v-focus></textarea>
			</div>

		

			<div class="col-md-12 col-sm-12 col-xs-12 form-group">
			  <span class="btn btn-primary" @click="validate">Save</span>
			  <router-link to="/view-documents" class="btn btn-danger">Cancel</router-link>
			  <span class="btn btn-success" @click="addSubDocuments" id="subDoc">Add Document</span>
				<span class="btn btn-success" @click="addSteps">Add Route</span>
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
					remarks: '',
		      subDocuments: [],
					process: []
		  	},
		    errors:{},
				offices:{},
				document_type: {},
		  }
		},
		directives: {
		  focus: {
		    // directive definition
		    inserted: function (el) {
		      	el.focus()
		    }
		  }
		},
		mounted(){
			this.selectDocumentType();
			this.selectOffice();
		},
		methods:{
				removeEmptyDocument: function(row, index) {
					if(row.code == ''){
						this.subDocuments.splice(index, 1)
					}
				},
				validate(){
					let checkRoute = false;
					let checkDocument = false;

					var filteredProcess= this.list.process.filter(function(pr){
							if(pr.office_id){ return true }
						})
					if(filteredProcess.length){checkRoute = true}

					var filteredDocument= this.list.subDocuments.filter(function(pr){
							if(pr.code){ return true }
						})
					if(filteredDocument.length){checkDocument = true}


					if(this.list.remarks && checkRoute && checkDocument) return this.save();
					this.errors = [];
					this.$snotify.error('Fix some errors')
					$('.alert').removeClass('d-none')
					if(!this.list.remarks) this.errors.push("Just add some remarks.");
					if(!checkRoute) this.errors.push("There should be at least 1 route.");
					if(!checkDocument) this.errors.push("There should be at least 1 Document.");
					return false
			},
			save(){
			    axios.post('/release',this.$data.list).then((response)=> 
			        {
								this.$snotify.success('Document marked as released')
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
			      .catch((error)=> this.errors = error.response.data.errors)
			    this.$router.push({ path: 'view-documents' })
			},
			addSubDocuments: function(row, index) {
					$('.document').removeClass('d-none')
					if($('.code').filter(function(){ return this.value == row.code }).length > 1){
					//	alert("Already Scanned")
						row.code = ""
						
					}else{
						this.list.subDocuments.push({
						    code: "",
						    title: "",
						});
					}

					var elem = document.createElement('tr');

					//getting sub-documents via document_code\
					if(index !== undefined)
					{
						axios.post('get-subdocument', this.list.subDocuments[index])
						.then((response)=>{ 
							this.list.subDocuments[index].title = response.data.document_title;
						})
						.catch((error)=> this.errors = error.response.data.errors)
				}
	    },
			addSteps: function(row, index) {
				$('.route').removeClass('d-none')

				this.list.process.push({
						office_id: "",
				});
				var elem = document.createElement('tr');
			},
			removeStep: function(index) {
					if(this.list.process.length == 1){
						$('.route').addClass('d-none')
					}
					this.list.process.splice(index, 1);
			},
			removeSubDocument: function(index) {
					if(this.list.subDocuments.length == 1){
						$('.document').addClass('d-none')
					}
					this.list.subDocuments.splice(index, 1);
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
			}
			
		}
	}
</script>
