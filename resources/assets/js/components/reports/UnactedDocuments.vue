<template>
	<div class="container">
		<h5>Received Documents</h5>
		

		</div>
</template>

<script>
export default {
  data(){
    return {
			lists:{},
			loading: false
    };
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
	            axios.get('received-documents')
	            .then(response => {
	              this.loading = !this.loading;
	              this.lists = this.temp = response.data
	              return response;
	            })
	        }
	    }
};
</script>