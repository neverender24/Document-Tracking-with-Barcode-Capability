<template>
	<div>
		<div class="alert alert-success"><h5>{{ title }}</h5></div>
		<table class="table table-hover">
				<thead>
					<tr>
						<th>Summary</th>
						<th>Released by</th>
						<th>Release to</th>
						<th>Received at</th>
						<th>Received by</th>
						<th>Released at</th>
						<th>barcode</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="item,key in routes">
						<td>{{ calculateDate(item.release_at, item.receive_at) }}</td>
						<td>{{ item.released_by == null ? '':item.released_by.name  }}</td>
						<td>{{ item.office == null ? '':item.office.office_prefix }}</td>
						<td>{{ item.receive_at | moment("MMM-DD-YYYY hh:mmA")}}</td>
						<td>{{ item.received_by == null ? '':item.received_by.name }}</td>
						<td>{{ item.release_at | moment("MMM-DD-YYYY hh:mmA") }}</td>
						<td>{{ item.barcode }}</td>
						<td>{{ item.remarks }}</td>
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
		methods: {
			calculateDate(released, received){
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
		}
		}
	}
</script>
