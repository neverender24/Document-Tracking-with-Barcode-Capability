<template>
	<div>
		<el-alert type="info" :closable="false"><strong>Document:</strong> {{ title }}</el-alert>

		<table class="table table-hover table-sm">
			<thead>
				<tr>
					<th>Summary</th>
					<th>Released by</th>
					<th>Release to</th>
					<th>Released at</th>
					<th>Received by</th>
					<th>Received at</th>
					<th>Barcode</th>
					<th>Remarks</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="item,key in routes">
					<!-- <td v-if="key!=0">{{routes[key-1].receive_at}}</td> -->
					<td v-if="key!=0">{{ calculateDate(item.release_at, routes[key-1].receive_at) }}</td>
					<td v-else>{{ calculateStartDate(item.release_at, item.receive_at) }}</td>
					<td>{{ item.released_by == null ? '':item.released_by.name  }}</td>
					<td>{{ item.office == null ? '':item.office.office_prefix }}</td>
					<td>{{ item.release_at | moment("MMM-DD-YY hh:mmA") }}</td>
					<td>{{ item.received_by == null ? '':item.received_by.name }}</td>
					<td>{{ item.receive_at | moment("MMM-DD-YY hh:mmA")}}</td>
					<td>{{ item.barcode }}</td>
					<td v-if="remarks(item.remarks) == 'return'" class="text-center">
						<el-popover placement="left" trigger="click" :content="item.remarks">
							<el-button slot="reference" size="mini" type="danger" round>View</el-button>
						</el-popover>
					</td>
					<td v-else-if="remarks(item.remarks) == 'remarks'" class="text-center">
						<el-popover placement="left" trigger="click" :content="item.remarks">
							<el-button slot="reference" size="mini" type="warning" round>View</el-button>
						</el-popover>
					</td>
					<td v-else class="text-center">
						{{ item.remarks}}
					</td>
					<td v-if="item.received_by == null && item.released_by.id==$root.user.user_id &&  item.receive_at==null && routes.length > 1">
						<el-tooltip class="item" effect="dark" content="Delete" placement="right" :enterable="false">
							<el-button size="mini" type="danger" icon="el-icon-delete" circle @click="deleteRoute(item.id, item.barcode)"></el-button>
						</el-tooltip>
					</td>
					<td v-else>
						<el-button size="mini" type="danger" icon="el-icon-delete" circle disabled=""></el-button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>

	export default
	{
		props: {
			routes: {},
			title: ''
		},
		mounted() {
			$(function () {
				$('[data-toggle="tooltip"]').tooltip()
			})
		},
		methods: {
			deleteRoute(id, barcode) {
				this.loading = !this.loading
				this.$confirm('This will permanently delete the record. Continue?', 'Warning', {
				confirmButtonText: 'OK',
				cancelButtonText: 'Cancel',
				type: 'warning'
				}).then(() => {
					axios.post('delete-route',{'id': id})
					.then((response)=>{
						this.loading = !this.loading

						this.$message({
							type: 'success',
							message: 'Deleted successfully'
						});
						this.$emit('deleteRoute',barcode)
					})
					.catch((error)=> this.errors = error.response.data.errors)        
				});

			},
			
			calculateDate(released, received){
				var moment = require('moment');
				var rel = new Date(released);
				var rec = new Date(received);
				var seconds = (rel.getTime() - rec.getTime()) / 1000; //1440516958
				var newdate = this.secondsToHms(seconds)

				return newdate
			},

			calculateStartDate(released, received){
				var moment = require('moment');
				var rel = new Date(released);
				var rec = new Date(received);
				var seconds = (rec.getTime() - rel.getTime()) / 1000; //1440516958
				var newdate = this.secondsToHms(seconds)

				return newdate
			},

			secondsToHms(d) {
				d = Number(d);
				var h = Math.floor(d / 3600);
				var m = Math.floor(d % 3600 / 60);
				var s = Math.floor(d % 3600 % 60);

				var hDisplay = h > 0 ? h + (h == 1 ? " hour, " : " hour, ") : "";
				var mDisplay = m > 0 ? m + (m == 1 ? " min, " : " min, ") : "";
				var sDisplay = s > 0 ? s + (s == 1 ? " sec" : " sec") : "";

				return hDisplay + mDisplay + sDisplay; 
			},

			remarks(remark) {
				console.log(remark)
				if (remark.toLowerCase().includes('return')) {
					return 'return'
				} else if (remark.toLowerCase().includes('remarks')) {
					return 'remarks'
				} else {
					return 'good'
				}
			}
		}
	}
</script>
