@extends('layouts.layout')

@section('title', 'Current Transaction')

@section('content')

<div class="content">
    <div class="container">
        <!-- end row -->

        <br />

        @if (Session::has('message'))
		    <div class="alert alert-info">{{ Session::get('message') }}</div>
		@endif

       	<div class="row">
			<div class="col-md-2">
				<!-- <h5>Takeaway</h5> -->
				<div class="row">
					<div class="col-md-12">
						<a href="{{ route('transaction-current.show', 0) }}" id="takeaway" class="{{ (isset($id) and $id==0) ? 'selected':'' }}">Takeaway</a>
					</div>
				</div>

				<hr>

				@if($table_top && $table_middle && $table_bottom)
					<!-- <h5>Table List</h5> -->
					{{-- <div class="card-box"> --}}
					<div class="row">
						<div class="col-md-4">
							<ul class="order-list">
								<?php $x = 0; ?>
					            @foreach($table_top as $t)
					            	
										@if ($t->transaction_id)
											@if ($id != 0)
												@if($id != -1 and $transaction_current->transaction_id == $t->transaction_id)
													<li class="selected" data-transaction="{{ $t->transaction_id }}">
												@else
													@if($t->status == 'printbill')
														<li class="printbill" data-transaction="{{ $t->transaction_id }}">
													@else
														<li class="active" data-transaction="{{ $t->transaction_id }}">
													@endif
												@endif
											@else
												@if($t->status == 'printbill')
													<li class="printbill" data-transaction="{{ $t->transaction_id }}">
												@else
													<li class="active" data-transaction="{{ $t->transaction_id }}">
												@endif
											@endif
										@else
											<li data-transaction="-1">
										@endif

										@if ($t->transaction_id)
											<a href="{{ route('transaction.edit', $t->transaction_id) }}" data-id="{{ $t->transaction_id }}" id="table_{{ $t->table_id }}">
										@else
											<!-- <a href="#" data-id=""> -->
											<a href="{{ route('transaction.create_with_table', $t->table_id) }}" id="table_{{ $t->table_id }}">
										@endif
												<div class="table">
													{{ $t->number }}
												</div>
											</a>
										</li>
									<?php $x++; ?>
								@endforeach
							</ul>
						</div>
						<div class="col-md-4">
							<ul class="order-list">
								<?php $x = 0; ?>
					            @foreach($table_middle as $t)
					            						            	
										@if ($t->transaction_id)
											@if ($id != 0)
												@if($id != -1 and $transaction_current->transaction_id == $t->transaction_id)
													<li class="selected" data-transaction="{{ $t->transaction_id }}">
												@else
													@if($t->status == 'printbill')
														<li class="printbill" data-transaction="{{ $t->transaction_id }}">
													@else
														<li class="active" data-transaction="{{ $t->transaction_id }}">
													@endif
												@endif
											@else
												@if($t->status == 'printbill')
													<li class="printbill" data-transaction="{{ $t->transaction_id }}">
												@else
													<li class="active" data-transaction="{{ $t->transaction_id }}">
												@endif
											@endif
										@else
											<li data-transaction="-1">
										@endif

										@if ($t->transaction_id)
											<a href="{{ route('transaction.edit', $t->transaction_id) }}" data-id="{{ $t->transaction_id }}" id="table_{{ $t->table_id }}">
										@else
											<a href="{{ route('transaction.create_with_table', $t->table_id) }}" id="table_{{ $t->table_id }}">
										@endif
												<div class="table">
													{{ $t->number }}
												</div>
											</a>
										</li>
									<?php $x++; ?>
								@endforeach
							</ul>
						</div>
						<div class="col-md-4">
							<ul class="order-list">
					            @foreach($table_bottom as $t)
					            	
										@if ($t->transaction_id)
											@if ($id != 0)
												@if($id != -1 and $transaction_current->transaction_id == $t->transaction_id)
													<li class="selected" data-transaction="{{ $t->transaction_id }}">
												@else
													@if($t->status == 'printbill')
														<li class="printbill" data-transaction="{{ $t->transaction_id }}">
													@else
														<li class="active" data-transaction="{{ $t->transaction_id }}">
													@endif
												@endif
											@else
												@if($t->status == 'printbill')
													<li class="printbill" data-transaction="{{ $t->transaction_id }}">
												@else
													<li class="active" data-transaction="{{ $t->transaction_id }}">
												@endif
											@endif
										@else
											<li data-transaction="-1">
										@endif
									
										@if ($t->transaction_id)
											<a href="{{ route('transaction.edit', $t->transaction_id) }}" data-id="{{ $t->transaction_id }}" id="table_{{ $t->table_id }}">
										@else
											<a href="{{ route('transaction.create_with_table', $t->table_id) }}" id="table_{{ $t->table_id }}">
										@endif
												<div class="table">
													{{ $t->number }}
												</div>
											</a>
										</li>
									<?php $x++; ?>
								@endforeach
							</ul>
						</div>
					</div>
				@endif
			</div>

			@if(!$transaction_current)
				<div class="col-md-10">
					<h5>Order Detail</h5>
					<div class="alert alert-warning">
						Data order kosong!
					</div>
				</div>
			@endif

			@if($transaction_current)

				@if(isset($id) and $id==0)
					<div class="col-sm-10 max-height">
						<table class="table table-consended table-striped">
							<thead>
								<tr>
									<th>Transction ID</th>
									<th>Date &amp; Time</th>
									<th>Name</th>
									<th>Note</th>
									<!-- <th>Variant Qty</th>
									<th>Total Qty</th> -->
									<th class="text-right">Total Cost</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@foreach($transaction_current as $tc)
									<tr>
										<td>{{ $tc->code() }}</td>
										<td>{{ \MyDate::toReadableDate($tc->created_at, FALSE, TRUE) }}</td>
										<td>{{ ($tc->name) ? $tc->name : "-" }}</td>
										<td>{{ ($tc->note) ? $tc->note : "-" }}</td>
										<!-- <td>{{ $tc->detail->count() }}</td>
										<td>{{ $tc->detail->sum('quantity') }}</td> -->
										<td class="text-right">{{ \MyNumber::toReadableAngka($tc->grand_total, FALSE) }}</td>
										<td>
											<a href="{{ route('transaction.edit', $tc->transaction_id) }}" class="btn btn-info btn-sm btn-block"><i class="fa fa-edit"></i> Edit</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					@if($id != -1)
						<div class="col-sm-6">
							<!-- <h5>Transaction #{{ $transaction_current->transaction_id }}</h5> -->
							<div class="card-box">
								<div class="row">
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-6">
												<span class="strong"><h5>Meja {{ ($transaction_current->table) ? $transaction_current->table->number:"-" }}</h5></span>
											</div>
											<div class="col-md-6 text-right">
												<div class="">{{ \MyDate::toReadableDate($transaction_current->created_at, false, true) }}</div>
												<div style="margin-top: 2px">Type: <strong>{{ strtoupper($transaction_current->type) }}</strong></div>
												<div style="margin-top: 2px">Status:
													@if($transaction_current->status=='pending')
														<span class="label label-inverse">Pending</span>
													@elseif($transaction_current->status=='printbill')
														<span class="label label-warning">Print Bill</span>
													@elseif($transaction_current->status=='finished')
														<span class="label label-success">Finished</span>
													@else
														<span class="label label-danger">Void</span>
													@endif
												</div>
											</div>
										</div>
									</div>
									{{-- <div class="col-md-8">
										
									</div> --}}
								</div>

								<hr>

								<div class="table-rep-plugin max-height-2">
									<div class="table-responsive" data-pattern="priority-columns">
										<table id="tech-companies-1" class="table table-striped">
											<thead>
												<tr>
													<th>#</th>
													{{-- <th data-priority="4" width="15%">Image</th> --}}
													<th data-priority="1" width="30%">Product</th>
													<th data-priority="1" width="20%" class="text-right">Price</th>
													<th data-priority="1" width="" class="text-center">Qty</th>
													<th data-priority="1" width="20%" class="text-right">Subtotal</th>
													{{-- <th data-priority="5" width="15%" class="text-right">Action</th> --}}
												</tr>
											</thead>
											<tbody>

												@if($transaction_current)
											            @foreach($transaction_current->detail as $dt)

											                <tr>
																<th>{{ ++$i }}</th>
																<td class="strong">{{ $dt->product->name }}</td>
																<td class="text-right">{{ \MyNumber::toReadableAngka($dt->price, false, true) }}</td>
																<td class="text-center">{{ $dt->quantity }}</td>
																<td class="text-right">{{ \MyNumber::toReadableAngka($dt->subtotal, false) }}</td>
																{{-- <td class="text-right">
																	{!! Form::open(['method' => 'DELETE','route' => ['transaction-current.destroy-item', $dt->transaction_detail_id],'style'=>'display:inline']) !!}
														            {!! Form::button('<span class="fa fa-trash"></span>', ['type' => 'submit', 'class' => 'btn btn-danger btn-sm', 'onclick'=>'return confirm("Are you sure?")']) !!}
														            {!! Form::close() !!}
																</td> --}}
															</tr>
															@if($dt->note)
															<tr class="order-note">
																<td></td>
																<td colspan="4" class="text-italic">
																	** {{ $dt->note }}
																</td>
															</tr>
															@endif

											            @endforeach
											    @else
											        <p>no data.</p>
											    @endif
											</tbody>
										</table>
									</div>
								</div>

							</div>
						</div>

						<div class="col-md-4">
							<!-- <h5>Action</h5> -->
							
							<div class="card-box">
								<div class="row">
									<div class="col-md-6">
										<a class="btn btn-primary btn-lg btn-block" href="{{ route('transaction.create') }}"><i class="fa fa-plus-circle"></i> New Order</a>
									</div>
									<div class="col-md-6">
										<a href="{{ route('transaction.edit', $transaction_current->transaction_id) }}" class="btn btn-info btn-lg btn-block"><i class="fa fa-edit"></i> Edit</a>
									</div>
								</div>
								<!-- <br> -->
								<!-- <div class="row">
									{{-- <div class="col-md-6">
										{!! Form::model($transaction_current, ['method' => 'PATCH','class' => 'form-horizontal', 'autocomplete' => 'off', 'files'=>true, 'route' => ['transaction-current.finish', $transaction_current->transaction_id]]) !!}
											{{ Form::submit('Finish', array('class' => 'btn btn-success btn-lg btn-block')) }}
										{{ Form::close() }}
									</div> --}}
									<div class="col-md-6">
										{!! Form::open(['method' => 'DELETE','route' => ['transaction-current.destroy', $transaction_current->transaction_id],'style'=>'display:inline']) !!}
							            {!! Form::button('<span class="fa fa-remove"></span> Cancel', ['type' => 'submit', 'class' => 'btn btn-danger btn-lg btn-block', 'onclick'=>'return confirm("Are you sure?")']) !!}
							            {!! Form::close() !!}
									</div>
								</div> -->
							</div>
							
							<div class="card-box">
								<div class="row">
									<div class="col-md-12">
										<table class="table">
											<tr class="danger">
								            	<td width="40%"><h5>Subtotal</h5></td>
								            	<td class="text-right">
								            		<h5 id="total">
								            			{{ \MyNumber::toReadableAngka($transaction_current->total, false) }}
								            		</h5>
								            		<input type="hidden" name="total" value="{{ $transaction_current->total }}">
								            	</td>
								            </tr>
											<tr class="danger">
								            	<td width="40%"><h5>Pajak</h5></td>
								            	<td class="text-right">
								            		<h5 id="total">
								            			10%
								            		</h5>
								            	</td>
								            </tr>
								            <tr class="danger">
								            	<td width="40%"><h5>Grand Total</h5></td>
								            	<td class="text-right">
								            		<h5 id="total">
								            			{{ \MyNumber::toReadableAngka($transaction_current->grand_total, false) }}
								            		</h5>
								            		<input type="hidden" name="grand_total" value="{{ $transaction_current->grand_total }}">
								            	</td>
								            </tr>
										</table>
									</div>
								</div>
							</div>
						</div>
					@else
						<div class="col-sm-10 max-height">
							<table class="table table-consended table-striped" id="table_all">
								<thead>
									<tr>
										<th>Transction ID</th>
										<th>Date &amp; Time</th>
										<th>Type / Table</th>
										<th>Name</th>
										<th>Note</th>
										<!-- <th>Variant Qty</th>
										<th>Total Qty</th> -->
										<th class="text-right">Total Cost</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@foreach($transaction_current as $tc)
										<tr>
											<td>{{ $tc->code() }}</td>
											<td>{{ \MyDate::toReadableDate($tc->created_at, FALSE, TRUE) }}</td>
											<td>{!! ($tc->table_id) ? "<strong>MEJA ". $tc->table->number ."</strong>" : "Takeaway" !!}</td>
											<td>{{ ($tc->name) ? $tc->name : "-" }}</td>
											<td>{{ ($tc->note) ? $tc->note : "-" }}</td>
											<!-- <td>{{ $tc->detail->count() }}</td>
											<td>{{ $tc->detail->sum('quantity') }}</td> -->
											<td class="text-right">{{ \MyNumber::toReadableAngka($tc->grand_total, FALSE) }}</td>
											<td>
												<a href="{{ route('transaction.edit', $tc->transaction_id) }}" class="btn btn-info btn-sm btn-block"><i class="fa fa-edit"></i> Edit</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
				@endif
			@endif
		</div>

		<!-- end row -->



    </div>
</div>

@endsection

@section('scripts')
	<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".select2").select2({
                templateResult: function(option) {
                    var $option = $(
                      '<div><strong>' + option.text + '</strong></div><div>' + option.title + '</div>'
                    );
                    return $option;
                }
            });

			// const socket = io.connect('http://localhost:3003');
			const socket = io.connect("{{ env('SOCKET_URL', 'http://localhost:3003') }}");

            socket.on('connect', function () {
                console.log('Connected');
            });

            socket.on('new transaction created', function (data) {
                console.log(data);
                // var content = data;
                // $('.order-list').append(data); 
                // $('#video').get(0).play();
                if (data.status == "pending")
                	var cl = 'active';
                else
                	var cl = 'printbill';

                var data_trans = $('a#table_'+ data.table_id).parent().data('transaction');
                if (data_trans == -1 || data_trans == data.transaction_id) {
	                $('a#table_'+ data.table_id).attr('href', "{{ url('/') }}/transaction/" + data.transaction_id + "/edit");
	                $('a#table_'+ data.table_id).parent().addClass(cl);
	                $('a#table_'+ data.table_id).parent().attr('data-transaction', data.transaction_id);
	            }

                $.ajax({
			        url: '{{ url("/") }}/transaction/refresh-order',
			        type: 'GET'
			    })
			    .done(function(data) {
					// console.log(data);
	                $('#table_all tbody').html(data);
			    });
            });

            socket.on('request remove link', function (data) {
                console.log("del");
                console.log(data);
                // var content = data;
                // $('a[data-id='+data+']').remove(); 
                // $('#video').get(0).play();

                var data_trans = $('a#table_'+ data.table_id).parent().data('transaction');
                if (data_trans == data.transaction_id) {
	                $('a#table_'+ data.table_id).attr('href', "{{ url('/') }}/transaction/create-with-table/" + data.table_id);
	                $('a#table_'+ data.table_id).parent().removeClass('active');
	                $('a#table_'+ data.table_id).parent().removeClass('selected');
	                $('a#table_'+ data.table_id).parent().removeClass('printbill');
	            }

                $.ajax({
			        url: '{{ url("/") }}/transaction/refresh-order',
			        type: 'GET'
			    })
			    .done(function(data) {
					// console.log(data);
	                $('#table_all tbody').html(data);
			    });
            });


            $('#paid').on('keyup', function() {
            	var grand_total = $('input[name="grand_total"]').val();
            	var paid = parseInt($(this).val());
            	var payable = paid - grand_total;
        		console.log(paid);

            	if(payable > 0) {
            		$('#payable').text(add_rupiah_string(rupiah_currency(payable)));
            		$('input[name="payable"]').val(payable)
            	}
            });
        });


        function add_rupiah_string(str)
        {
        	return 'Rp. ' + str;
        }

        function rupiah_currency(bilangan)
        {
			var	reverse = bilangan.toString().split('').reverse().join(''),
				ribuan 	= reverse.match(/\d{1,3}/g);
				ribuan	= ribuan.join('.').split('').reverse().join('');
			return ribuan;
		}
	</script>
@endsection