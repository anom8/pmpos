<?php $i=1; ?>
<table>
	<tr>
		<th>No.</th>
		<th>Name</th>
		<th>Price</th>
		<th>Quantity</th>
		<th>Total Price</th>
	</tr>
	@foreach($items as $it)
		<tr>
			<td>{{ $i++ }}</td>
			<td>{{ $it->name }}</td>
			<td>{{ $it->price }}</td>
			<td>{{ $it->qty }}</td>
			<td>{{ $it->sbt }}</td>
		</tr>
	@endforeach
</table>