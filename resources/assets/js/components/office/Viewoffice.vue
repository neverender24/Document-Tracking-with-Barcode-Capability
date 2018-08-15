<template>
	<div>
		<h3>List of Offices</h3>
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Prefix</th>
                </tr>
              </thead>
              <tbody v-for="item,key in temp.data">
                <tr>
                  <td>{{ item.office_name }}</td>
                  <td>{{ item.office_prefix }}</td>
                </tr>
              </tbody>
          </table>
	</div>
</template>

<script>
	export default
	{
	  data(){
	    return{
	      lists:{},
	      loading: false,
	      temp: '',
	    }
	  },
	  mounted(){
	  	this.getResults();
	  },
	  methods:{
	        getResults(page) {
	          this.loading = !this.loading
	            if (typeof page === 'undefined') {
	              page = 1;
	            }
	            axios.get('view-offices')
	            .then(response => {
	              this.loading = !this.loading;
	              this.lists = this.temp = response.data
	              return response;
	            })
	        }
	    }
	}
</script>