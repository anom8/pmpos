<table>
	<tr>
		<td colspan="3"><h5>SALES DETAIL</h5></td>
	</tr>
	<tr>
		<td colspan="2">Gross Sales</td>
		<td>{{ $t_gross_total }}</td>
	</tr>
	<tr>
		<td colspan="2">Discount</td>
		<td>{{ $t_discount }}</td>
	</tr>
	<tr>
		<td colspan="2">Free of Charge</td>
		<td>{{ $t_foc }}</td>
	</tr>
	<tr>
		<td colspan="2">Net Sales</td>
		<td>{{ $t_net_total }}</td>
	</tr>
	<tr>
		<td colspan="2">Tax (10%) Total</td>
		<td>{{ $t_tax_total }}</td>
	</tr>
	<tr>
		<td colspan="2">Total Sales</td>
		<td>{{ $t_sales_total }}</td>
	</tr>
	<tr>
		<td colspan="2">No. of Receipt</td>
		<td>{{ $t_no_of_receipt }}</td>
	</tr>
	<tr>
		<td colspan="2">Avg. Receipt</td>
		<td>{{ $t_avg_receipt }}</td>
	</tr>
	<tr></tr>


	<tr>
		<td colspan="3"><h5>SALES BY MEDIA</h5></td>
	</tr>
	@foreach($transaction_by_media as $tbm)
		<tr>
			<td>{{ $tbm->paymentMethod->name }}</td>
			<td>{{ $tbm->count }}</td>
			<td>{{ $tbm->sum }}</td>
		</tr>
	@endforeach
	<tr>
		<td><strong>Total</strong></td>
		<td>{{ $transaction_by_media_total->count }}</td>
		<td>{{ $transaction_by_media_total->sum }}</td>
	</tr>
	<tr></tr>


	<tr>
		<td colspan="3"><h5>SALES BY CASHIER</h5></td>
	</tr>
	@foreach($transaction_by_cashier as $tbm)
		<tr>
			<td>{{ $tbm->user->name }}</td>
			<td>{{ $tbm->count }}</td>
			<td>{{ $tbm->sum }}</td>
		</tr>
	@endforeach
	<tr>
		<td><strong>Total</strong></td>
		<td>{{ $transaction_by_cashier_total->count }}</td>
		<td>{{ $transaction_by_cashier_total->sum }}</td>
	</tr>
	<tr></tr>


	<tr>
		<td colspan="3"><h5>SALES BY PRICE CATEGORY</h5></td>
	</tr>
	@foreach($transaction_by_pc as $tbm)
		<tr>
			<td>{{ ucfirst($tbm->price_category) }}</td>
			<td>{{ $tbm->count }}</td>
			<td>{{ $tbm->sum }}</td>
		</tr>
	@endforeach
	<tr>
		<td><strong>Total</strong></td>
		<td>{{ $transaction_by_pc_total->count }}</td>
		<td>{{ $transaction_by_pc_total->sum }}</td>
	</tr>
	<tr></tr>


	<tr>
		<td colspan="3"><h5>SALES BY TYPE</h5></td>
	</tr>
	@foreach($transaction_by_type as $tbm)
		<?php
			$name = $tbm->type;
            if ($name == 'disajikan')
                $name = 'Dine In (HIDANG)';
            else if ($name == 'rames')
                $name = 'Dine In (KHUSUS)';
            else
                $name = 'Takeaway';
		?>
		<tr>
			<td>{{ $name }}</td>
			<td>{{ $tbm->count }}</td>
			<td>{{ $tbm->sum }}</td>
		</tr>
	@endforeach
	<tr>
		<td><strong>Total</strong></td>
		<td>{{ $transaction_by_type_total->count }}</td>
		<td>{{ $transaction_by_type_total->sum }}</td>
	</tr>
	<tr></tr>
</table>