<template>
    <div id="transaction_details_modal" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Transaction Details | Document breakdown per type</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="modal_close()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="card">
              <div class="card-header">
                <h5>Received</h5>
              </div>
              <div class="card-body" :class="{'text-center': loading}">
                <div class="spinner-border" role="status" v-if="loading">
                  <span class="sr-only">Loading...</span>
                </div>
                <table v-else class="table table-hover table-striped table-sm">
                  <tr v-for="item in data.received">
                      <td>{{ item.document_type }}</td>
                      <td>{{ item.tots }}<span v-if="item.approve!=null">({{ item.approve }})</span></td>
                  </tr>
                </table>
              </div>
            </div>
            <hr>
            <div class="card">
              <div class="card-header">
                <h5>Released</h5>
              </div>
              <div class="card-body" :class="{'text-center': loading}">
                <div class="spinner-border" role="status" v-if="loading">
                  <span class="sr-only">Loading...</span>
                </div>
                <table v-else class="table table-hover table-striped table-sm">
                  <tr v-for="item in data.released">
                      <td>{{ item.document_type }}</td>
                      <td>{{ item.tots }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="modal_close()">Close</button>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
export default {

    props: ['fromTo'],

    data() {
        return {
            data: {},
            loading: false,
            document_type: [],
            selectDocumentType: 0
        }
    },

    mounted() {
        $(function() {
            $("#transaction_details_modal").modal('show')
        })
        this.getDetails()
    },

    methods: {
        getDetails() {
            this.loading = true
            axios.post('getTransactionDetails', this.fromTo).then(response => {
                this.data = response.data
                this.loading = false
            })
        },


        modal_close() {
            this.$emit('modal_close');
        }
    }
}
</script>