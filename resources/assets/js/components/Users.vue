<template>
    <div class="container">
        <div class="loading" v-if="loading">Loading&#8230;</div>
        <h5>Manage Users</h5>

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
        <datatable :columns="columns">
            <tbody>
                <tr v-for="item in lists" :key="item.id">
                    <td>{{ item.cats }}</td>
                    <td>{{ item.email }}</td>
                    <td>{{ item.office.office_prefix }}</td>
                    <td>
                        <label class="switch">
                        <input type="checkbox" @change="setActiveStatus(item.id)" v-model="item.is_active">
                        <span class="slider round"></span>
                        </label>
                            <svg  xmlns="http://www.w3.org/2000/svg" @click="resetPassword(item.id)" width="20" height="20" fill="currentColor" class="ml-2 bi bi-unlock" viewBox="0 0 16 16">
                            <path d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2zM3 8a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1H3z"/>
                            </svg>
                    </td>
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
    import Datatable from './helpers/datatable.vue';
    import Pagination from './helpers/pagination.vue';

    export default
    {
        components:{
            datatable: Datatable,
            pagination: Pagination
        },
      data(){
          let sortOrders = {};

          let columns = [
              { width: '10%', label: 'CATS#', name: 'CATS#'},
              { width: '15%', label: 'Username', name: 'Username'},
              { width: '15%', label: 'Office', name: 'Office'},
              { width: '30%', label: 'Active', name: 'Active'},
          ]


        return{
            columns: columns,
            tableData: {
                draw: 0,
                length: 10,
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
            isActive: 1,
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

            getResults(url = 'users') {

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

            setActiveStatus(id) {
                axios.post('/user', { id: id }).then( respone => {

                })
            },

            resetPassword(id) {
                let text = "Are you sure you want to reset the password?"
                if (confirm(text) == true) {
                    axios.post('/user-reset', { id: id }).then( respone => {

                    })
                } else {
                    alert('Cancelled')
                }
            }
			
        }
    }
</script>

<style scoped>
    /* The switch - the box around the slider */
    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    /* The slider */
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      -webkit-transition: .4s;
      transition: .4s;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
    }

    input:checked + .slider {
      background-color: #2196F3;
    }

    input:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
      border-radius: 34px;
    }

    .slider.round:before {
      border-radius: 50%;
    }
</style>