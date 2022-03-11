<template>
    <div id="time_modal" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Transaction Details | Document time consumed</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="modal_close()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="card">
              <div class="card-header">
                <div class="row">
                  <div class="col-4">
                    <select
                        v-model="selectedDocumentType"
                        class="form-control form-control-sm border-secondary"
                        @change="getDetails()"
                    >
                        <option value=""></option>
                        <option
                            v-for="(value,key) in documentType"
                            :value="value.id"
                        >{{ value.document_type }}</option>
                    </select>
                  </div>
                  <div class="col-3">
                    <input type="text" class="form-control form-control-sm border-secondar" v-model="barcode" @change="getDetails()" placeholder="Search barcode...">
                  </div>
                  <div class="col-3">
                    <select class="form-control form-control-sm border-secondary" v-model="selectedScope" @change="getDetails()">
                      <option value="1" selected>Entire Process</option>
                      <option value="2">Office Exclusive</option>
                    </select>
                  </div>
                  <div class="col-2">
                    <download-excel class= "btn btn-sm btn-primary btn-block" :data="excelData">
                      Export
                    </download-excel>
                  </div>
                </div> 
              </div>
              <div class="card-body" :class="{'text-center': loading}">
                <div class="spinner-border" role="status" v-if="loading">
                  <span class="sr-only">Loading...</span>
                </div>
                <table v-else class="table table-hover table-striped table-sm">
                  <thead>
                    <tr>
                        <td>Barcode</td>  
                        <td>Document Title</td>
                        <td>Office Time</td>
                        <td>Travel Time</td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in data">
                        <td>{{ item['barcode'] }}</td>
                        <td class="w-50">
                          <div class="expanded-text">
                            <p class="text">
                              <span class="short-name">{{ item['title'].substring(0,40) }}</span>
                              <span class="longer-name">{{ item['title']}}</span>
                            </p>
                          </div>
                        </td>
                        <td v-html="item['officeTime']"></td>
                        <td>{{ item['travelTime'] }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <hr>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="modal_close()">Close</button>
          </div>
        </div>
      </div> 
    </div>
</template>
<style>
  /* Container for the text that expands on hover */
  .expanded-text {
      width: 100%;
  }
  /* Longer name hidden by default  */
  span.longer-name{
      display:none;
  }
  /* On hover, hide the short name */
  .expanded-text:hover span.short-name{
      display:none;
  }
  /* On hover, display the longer name.  */
  .expanded-text:hover span.longer-name{
      display:block;
  }
</style>

<script>
export default {

    props: ['fromTo'],

    data() {
        return {
            data: {},
            loading: false,
            documentType: {},
            selectedDocumentType: 0,
            selectedScope: 1,
            barcode: "",
            excelData:[]
        }
    },

    mounted() {
        $(function() {
            $("#time_modal").modal('show')
        })
        
        this.getDocumentTypes()
        this.getDetails()
    },

    methods: {
        getDetails() {
            this.loading = true

            var payload = {
              range: this.fromTo,
              documentType: this.selectedDocumentType,
              scope: this.selectedScope,
              barcode: this.barcode
            }

            axios.post('get-time-summary', payload).then(response => {
              this.data = response.data


              this.excelData = _.cloneDeep(response.data)
              var vm = this

              _.forEach(this.excelData, function(value, index) {
                var officeTime = value.officeTime.split(',')
                var travelTime = value.officeTime.split(',')
                  vm.excelData[index].barcode = value.barcode
                  vm.excelData[index].title = value.title
                  vm.excelData[index].officeTimeDays = officeTime[0].replace('D', '')
                  vm.excelData[index].officeTimeHours = officeTime[1].replace('H', '')
                  vm.excelData[index].officeTimeMinutes = officeTime[2].replace('M', '')
                  vm.excelData[index].officeTimeSeconds = officeTime[3].replace('S', '')
                  vm.excelData[index].officeTime = value.officeTime
                  vm.excelData[index].travelTimeDays = travelTime[0].replace('D', '')
                  vm.excelData[index].travelTimeHours = travelTime[1].replace('H', '')
                  vm.excelData[index].travelTimeMinutes = travelTime[2].replace('M', '')
                  vm.excelData[index].travelTimeSeconds = travelTime[3].replace('S', '')
              })

              this.loading = false
            })
        },

        getDocumentTypes() {
            axios
                .post("view-document-types")
                .then(response => {
                    this.documentType = response.data;
                })
                .catch(error => (this.errors = error.response.data.errors));
        },

        modal_close() {
            this.$emit('modal_close');
        }
    }
}
</script>