@extends('layouts.layout')

@section('title', 'Current Transaction')

@section('styles')
    <link href="{{ URL::asset('assets/plugins/custombox/css/custombox.min.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Transaction History</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('/') }}">Dashboard</a>
                        </li>
                        <li>
                            <a href="{{ route('transaction-history.index') }}">Transaction History</a>
                        </li>
                        <li class="active">
                            Detail
                        </li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- end row -->

        <div class="row">
			{{-- <div class="col-sm-12">
				<div class="pull-right">
	                <a class="btn btn-success" href="{{ route('transaction.create') }}"> Add New Ads</a>
	            </div>
			</div> --}}
        </div>

        <br />

        @if (Session::has('message'))
		    <div class="alert alert-info">{{ Session::get('message') }}</div>
		@endif

       	<div class="row">
			<div class="col-sm-4">
				<div class="card-box">
					<h5>Order Info</h5>
					<hr>
					<div class="row">
						<div class="col-md-4">Order #</div>
						<div class="col-md-8"><span class="strong">: {{ $transaction->code() }}</span></div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-4">Cashier</div>
						<div class="col-md-8"><span class="strong">: {{ ($transaction->user) ? $transaction->user->name : "-" }}</span></div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-4">Date</div>
						<div class="col-md-8"><span class="strong">: {{ \MyDate::toReadableDate($transaction->created_at, false, true) }}</span></div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-4">Item Type</div>
						<div class="col-md-8"><span class="strong">: {{ $transaction->detail->count() }}</span></div>
					</div>
					<br>
					
					<div class="row">
						<div class="col-md-4">Status</div>
						<div class="col-md-8">: 
							@if($transaction->status=='pending')
								<span class="label label-inverse">Pending</span>
							@elseif($transaction->status=='printbill')
								<span class="label label-warning">Print Bill</span>
							@elseif($transaction->status=='finished')
								<span class="label label-success">Finished</span>
							@elseif($transaction->status=='lost')
								<span class="label label-danger">Lost</span>
							@else
								<span class="label label-danger">Void</span>
							@endif
						</div>
					</div>
					<br>

					@if ($transaction->promotion_id!=null)
					<div class="row">
						<div class="col-md-4">Promotion</div>
						<div class="col-md-8">: 
							{{ $transaction->promotion->name ." (". ($transaction->promotion->type=='percent' ? $transaction->promotion->value ."%" : $transaction->promotion->value ." off") .")" }}
						</div>
					</div>
					<br>
					@endif

					<div class="row">
						<div class="col-md-4">Payment</div>
						<div class="col-md-8">: 
							{{ $transaction->paymentMethod->name }}
						</div>
					</div>
					<br>

					<div class="row">
						<div class="col-md-4">Price Category</div>
						<div class="col-md-8">: 
							{{ ucfirst($transaction->price_category) }}
						</div>
					</div>
					<br>

					@if ($transaction->name)
						<div class="row">
							<div class="col-md-4">Name</div>
							<div class="col-md-8">:
								{{ ucfirst($transaction->name) }}
							</div>
						</div>
						<br>
					@endif

					@if ($transaction->status=='void')
						<div class="row">
							<div class="col-md-4">Voided By</div>
							<div class="col-md-8">: 
								Store Manager
							</div>
						</div>

						<br>
						<div class="row">
							<div class="col-md-4">Remarks</div>
							<div class="col-md-8">: 
								{{ $transaction->remarks }}
							</div>
						</div>
					@elseif ($transaction->status=='lost')
						<div class="row">
							<div class="col-md-4">Lost By</div>
							<div class="col-md-8">: 
								Cashier
							</div>
						</div>
					@endif
					<br>

					<div class="row">
						@if ($transaction->status!='void' and $transaction->status!='lost')
							<div class="col-md-6">
								<a href="{{ route('transaction-history.print', $transaction->transaction_id) }}" class="btn btn-success btn-block" onclick='return confirm("Are you sure?")'><i class="fa fa-print"></i> Print Struk</a>
							</div>
							<div class="col-md-6">
								{{-- {!! Form::open(['method' => 'DELETE','route' => ['transaction.destroy', $transaction->transaction_id],'style'=>'display:inline']) !!}
					            {!! Form::button('<span class="fa fa-trash"></span> Void', ['type' => 'submit', 'class' => 'btn btn-danger btn-block', 'onclick'=>'return do_check();']) !!}
					            {!! Form::close() !!} --}}
					            <a  class="btn btn-danger btn-block waves-effect waves-light" data-toggle="modal" data-target="#custom-modal" id="void"><span class="fa fa-trash"></span> Void</a>
							</div>
						@endif
					</div>
				</div>

			</div>

			<div class="col-sm-8">
				<div class="card-box">
					<h5>Order Detail</h5>
					<hr>
					<div class="table-rep-plugin">
						<div class="table-responsive" data-pattern="priority-columns">
							<table id="tech-companies-1" class="table  table-striped">
								<thead>
									<tr>
										<th>#</th>
										{{-- <th data-priority="4" width="15%">Image</th> --}}
										<th data-priority="1" width="30%">product</th>
										<th data-priority="1" width="" class="text-right">Price</th>
										<th data-priority="1" width="10%" class="text-center">Quantity</th>
										<th data-priority="1" width="" class="text-right">Subtotal</th>
									</tr>
								</thead>
								<tbody>

									@if($transaction->detail->count() > 0)
								            @foreach($transaction->detail as $dt)

								                <tr>
													<th>{{ ++$i }}</th>
													<td>{{ $dt->product->name }}</td>
													<td class="text-right">{{ \MyNumber::toReadableAngka($dt->price, false) }}</td>
													<td class="text-center">{{ $dt->quantity }}</td>
													<td class="text-right">{{ \MyNumber::toReadableAngka($dt->subtotal, false) }}</td>
												</tr>

								            @endforeach

								            <tr class="success">
								            	<td></td>
								            	<td colspan="3"><h4>Total</h4></td>
								            	<td colspan="2" class="text-right"><h4>{{ \MyNumber::toReadableHarga($transaction->total, false) }}</h4></td>
								            </tr>

								            @if ($transaction->promotion_id != null)
									            <tr class="success">
									            	<td></td>
									            	<td colspan="3"><h4>Discount</h4></td>
									            	<td colspan="2" class="text-right"><h4>{{ \MyNumber::toReadableHarga($transaction->discount, false) }}</h4></td>
									            </tr>
								            @endif

								            <tr class="success">
								            	<td></td>
								            	<td colspan="3"><h4>Grand Total</h4></td>
								            	<td colspan="2" class="text-right"><h4>{{ \MyNumber::toReadableHarga($transaction->grand_total, false) }}</h4></td>
								            </tr>

								            <tr class="success">
								            	<td></td>
								            	<td colspan="3"><h4>Paid</h4></td>
								            	<td colspan="2" class="text-right"><h4>{{ \MyNumber::toReadableHarga($transaction->paid, false) }}</h4></td>
								            </tr>

								            <tr class="success">
								            	<td></td>
								            	<td colspan="3"><h4>Payable</h4></td>
								            	<td colspan="2" class="text-right"><h4>{{ \MyNumber::toReadableHarga($transaction->payable, false) }}</h4></td>
								            </tr>
								    @else
								        <p>no data.</p>
								    @endif
								</tbody>
							</table>
						</div>
					</div>

				</div>
			</div>
		</div>
		<!-- end row -->

		<div id="custom-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Notes</h4>
                    </div>
                    <form action="" autocomplete="off">
	                    <div class="modal-body">
	                        <div class="row">
	                            <div class="col-md-12">
	                                <div class="form-group">
	                                    <label for="modal-password" class="control-label">Password</label>
	                                    <input type="password" class="form-control" id="modal-password" placeholder="Masukkan Password">
	                                </div>
	                            </div>
	                        </div>
	                        
	                        <div class="row">
	                            <div class="col-md-12">
	                                <div class="form-group no-margin">
	                                    <label for="modal-remark" class="control-label">Remark</label>
	                                    <textarea class="form-control autogrow" id="modal-remark" placeholder="Tulis catatan" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 104px;"></textarea>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="modal-footer">
	                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
	                        <button type="button" class="btn btn-info waves-effect waves-light" id="modal-save">Save changes</button>
	                    </div>
                    </form>
                </div>
            </div>
        </div><!-- /.modal -->

    </div>
</div>

@endsection

@section('scripts')
	<script src="{{ URL::asset('assets/js/modernizr.min.js') }}"></script>
	<script src="{{ URL::asset('assets/plugins/custombox/js/custombox.min.js') }}"></script>
	<script src="{{ URL::asset('assets/plugins/custombox/js/legacy.min.js') }}"></script>
	<script type="text/javascript">
		var passconfirm = '150292';
		var transaction_id = {{ $transaction->transaction_id }};
		$(document).ready(function () {
			$('#modal-save').on('click', function(e) {
				e.preventDefault();
				var pass = $('#modal-password').val();

				if (pass == passconfirm) {
					if ($('#modal-remark').val() == '') 
						alert('Remarks tidak boleh kosong');
					else {
						$.ajax({
		                   url: '{{ url("/") }}/void-order/'+transaction_id,
		                   data: {
		                      data: {
		                      	'user_id': '{{ auth()->user()->user_id }}',
		                      	'remarks': $('#modal-remark').val()
		                      }
		                   },
		                   type: 'post',
		                   error: function() {
		                      alert('Error');
		                   },
		                   dataType: 'json',
		                   success: function(data) {
		                        if(data.status) {
		                            location.reload();
		                        }
		                   }
		                });
					}

				} else {
					alert('Password Salah!');
				}
			});
		});
	</script>
@endsection