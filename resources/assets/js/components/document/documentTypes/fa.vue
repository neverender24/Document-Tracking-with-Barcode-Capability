<template>
    <div class="form-group">
        <div class="card">
            <div class="card-header py-0">Title Guide</div>
            <div class="card-body py-2 mb-1">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Date:</label>
                    <div class="col-sm-8">
                        <input
                            type="date"
                            class="form-control form-control-sm"
                            v-model="inputs.date"
                            @change="generateTitle()"
                        >
                    </div>
                    <label class="col-sm-4 col-form-label">Client:</label>
                    <div class="col-sm-8">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="inputs.client"
                            @change="generateTitle()"
                        >
                    </div>
                    <label class="col-sm-4 col-form-label">Age:</label>
                    <div class="col-sm-8">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="inputs.age"
                            @change="generateTitle()"
                        >
                    </div>
                    <label class="col-sm-4 col-form-label">Address:</label>
                    <div class="col-sm-8">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="inputs.address"
                            @change="generateTitle()"
                        >
                    </div>
                    <label class="col-sm-4 col-form-label">Amount:</label>
                    <div class="col-sm-8">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="inputs.amount"
                            @change="generateTitle()"
                        >
                    </div>
                    <label class="col-sm-4 col-form-label">Purpose:</label>
                    <div class="col-sm-8">
                        <select
                            v-model="inputs.purpose"
                            class="form-control border-secondary form-control-sm"
                            @change="generateTitle()"
                        >
                            <option value=""></option>
                            <option value="Burial">Burial</option>
                            <option value="Medical">Medical</option>
                            <option value="Fire Victim">Fire Victim</option>
                            <option value="Rehabilitation">Rehabilitation</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <label class="col-sm-4 col-form-label">MoP:</label>
                    <div class="col-sm-8">
                        <select
                            v-model="inputs.payment"
                            class="form-control border-secondary form-control-sm"
                            @change="generateTitle()"
                        >
                            <option value=""></option>
                            <option value="Cash">Cash</option>
                            <option value="Check">Check</option>
                        </select>
                    </div>
                    <label class="col-sm-4 col-form-label">Beneficiary</label>
                    <div class="col-sm-8">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="inputs.patient"
                            @keyup="generateTitle()"
                        >
                    </div>
                    <div class="col-sm-12">
                        <small
                            class="text-danger py-1"
                        >Append below if there are additional searchable keywords.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            inputs: {
                date: "",
                client: "",
                age: "",
                address: "", 
                amount: "", 
                purpose: "", 
                payment: "", 
                patient: "", 
            },
        }
    },

    methods: {
        generateTitle() {
            var title =
                this.inputs.date +
                "; " +
                this.inputs.client +
                "; " +
                this.inputs.age +
                "; " +
                this.inputs.address +
                "; " +
                this.inputs.amount +
                "; " +
                this.inputs.purpose +
                "; " +
                this.inputs.payment +
                "; " +
                this.inputs.patient +
                "; "

            this.removeChars(title)

        },

        removeChars(text) {
            var regexp = new RegExp("#([^\\s]*)", "g");
            this.$emit("setDocumentTitle", text.replace(regexp, "No."));
        },
    }
}
</script>