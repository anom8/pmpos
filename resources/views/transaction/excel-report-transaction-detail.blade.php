<table>
	<tr>
		<td colspan="3"><h5>Transaction Detail</h5></td>
	</tr>
	<tr></tr>
	<tr>
		<th>No. Transaction</th>
		<th>Payment Method</th>
		<th>Date</th>
		<th>Total</th>
		<th>Note</th>
		<th>Name</th>
		<th>Remarks</th>
	</tr>
	@foreach($transaction_get as $tg)
		<tr>
			<td>{{ $tg->code() }}</td>
			<td>{{ $tg->paymentMethod->name }}</td>
			<td>{{ $tg->created_at }}</td>
			<td>{{ $tg->sum }}</td>
			<td>{{ $tg->note }}</td>
			<td>{{ $tg->name }}</td>
			<td>{{ $tg->remarks }}</td>
		</tr>
	@endforeach
	<tr></tr>

</table>