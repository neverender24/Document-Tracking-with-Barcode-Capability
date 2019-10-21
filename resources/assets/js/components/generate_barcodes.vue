<template>
    <div>
        <h3>Generate Barcodes</h3>
        <label>How Many? (Max value is 72)</label>
        <input type="number" v-model="total" class="form-control" @change="generateBarcode(total)" />
        <hr />
        <div class="row">
            <div class="col-2 p-0" v-for="row in barcodes">
                <div class="border border-dark">
                    <barcode class="barcode" :value="row"></barcode>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
.barcode {
    width: 100%;
}
</style>
<script>
export default {
    data() {
        return {
            total: 0,
            barcodes: []
        };
    },
    mounted() {
        this.generateBarcode();
    },
    methods: {
        generateBarcode(total) {
            if (total > 72 || total < 6) {
                this.$message({
                        type: "error",
                        message: "Max value is 102 and min value 6"
                    });

                return false
            }

            axios.post("generate-barcodes", {total:this.total}).then(response => {
                this.barcodes = response.data
            });
        },

    }
};
</script>