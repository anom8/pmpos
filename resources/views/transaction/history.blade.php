@extends('layouts.layout')

@section('title', 'Transaction History')

@section('styles')
    <link href="{{ URL::asset('assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
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
                        <li class="active">
                            Transaction History
                        </li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- end row -->

        <div class="row">
        	<div class="col-md-12">
        		Statistic: <p style="margin-bottom: 5px"></p>
        		<div style="font-size: 13px">
	        		@if(!isset($_GET['sid']))
	        			<strong>All ({{ $total_transaction->total->count() }})</strong>
	    			@else
						<a href="{{ URL::to('transaction-history') }}">All</a> ({{ $total_transaction->total->count() }})
					@endif

					&nbsp;|&nbsp;

					@if(isset($_GET['sid']) && $_GET['sid']=='pending')
	        			<strong>Pending ({{ $total_transaction->pending->count() }})</strong>
	    			@else
						<a href="{{ URL::to('transaction-history?sid=pending') }}">Pending</a> ({{ $total_transaction->pending->count() }})
					@endif

					&nbsp;|&nbsp;

					@if(isset($_GET['sid']) && $_GET['sid']=='printbill')
	        			<strong>Print Bill ({{ $total_transaction->printbill->count() }})</strong>
	    			@else
						<a href="{{ URL::to('transaction-history?sid=printbill') }}">Print Bill</a> ({{ $total_transaction->printbill->count() }})
					@endif

					&nbsp;|&nbsp;

					@if(isset($_GET['sid']) && $_GET['sid']=='finished')
	        			<strong>Finished ({{ $total_transaction->finished->count() }})</strong>
	    			@else
						<a href="{{ URL::to('transaction-history?sid=finished') }}">Finished</a> ({{ $total_transaction->finished->count() }})
					@endif

					&nbsp;|&nbsp;

					@if(isset($_GET['sid']) && $_GET['sid']=='void')
	        			<strong>Void ({{ $total_transaction->void->count() }})</strong>
	    			@else
						<a href="{{ URL::to('transaction-history?sid=void') }}">Void</a> ({{ $total_transaction->void->count() }})
					@endif

					&nbsp;|&nbsp;

					@if(isset($_GET['sid']) && $_GET['sid']=='lost')
	        			<strong>Lost ({{ $total_transaction->lost->count() }})</strong>
	    			@else
						<a href="{{ URL::to('transaction-history?sid=lost') }}">Lost</a> ({{ $total_transaction->lost->count() }})
					@endif
				</div>
            </div>
        </div>

            <hr>
		<div class="row">
			<div class="col-md-2">
            	Cashier: <p style="margin-bottom: 5px"></p>
				{{ Form::select('user', $user, (isset($_GET['user']) ? $_GET['user']:null), ['class' => 'form-control input-sm', 'id' => 'user']) }}
			</div>

			<div class="col-md-2">
				Date Start: <p style="margin-bottom: 5px"></p>
        		{{ Form::text('date_start', (isset($_GET['date_start'])) ? $_GET['date_start']:date('d-m-Y'), array('class' => 'form-control input-sm', 'id' => 'date_start', 'placeholder' => 'Date Start')) }}
			</div>

			<div class="col-md-2">
				Date End: <p style="margin-bottom: 5px"></p>
        		{{ Form::text('date_end', (isset($_GET['date_end'])) ? $_GET['date_end']:date('d-m-Y'), array('class' => 'form-control input-sm', 'id' => 'date_end', 'placeholder' => 'Date End')) }}
			</div>

			<div class="col-md-2">
				Grand Total: <p style="margin-bottom: 5px"></p>
        		<h4>{{ \MyNumber::toReadableHarga($grand_total, false) }}</h4>
			</div>

			<div class="col-md-2">
				Jumlah Transaksi: <p style="margin-bottom: 5px"></p>
        		<h4>{{ $count_trans }}</h4>
			</div>

			<div class="col-md-2">
				Export to: <p style="margin-bottom: 5px"></p>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-12">
								<a href="{{ URL::to('print-report?').Request::getQueryString() }}" class="btn btn-primary btn-block">Excel</a>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-12">
								<a href="{{ URL::to('print-csv-report?').Request::getQueryString() }}" class="btn btn-primary btn-block">CSV</a>
							</div>
						</div>
					</div>
				</div>
			</div>

	        <br>
        </div>

        <br />

        @if (Session::has('message'))
		    <div class="alert alert-info">{{ Session::get('message') }}</div>
		@endif

       	<div class="row">
			<div class="col-sm-12">
				<div class="card-box">

					<div class="table-rep-plugin">
						<div class="table-responsive" data-pattern="priority-columns">
							<table id="tech-companies-1" class="table  table-striped">
								<thead>
									<tr>
										<th>ID</th>
										<th data-priority="1" width="10%">Order #</th>
										<th data-priority="1" width="10%">Cashier</th>
										<th data-priority="2" width="10%">Table</th>
										<th data-priority="4" width="15%">Date &amp; Time</th>
										<th data-priority="4" width="10%" class="text-center">Item Type</th>
										<th data-priority="4" width="10%" class="text-center">Quantity</th>
										<th data-priority="4" width="10%" class="text-center">Grand Total</th>
										<th data-priority="3" width="" class="text-center">Status</th>
										<th data-priority="5" width="15%" class="text-right">Action</th>
									</tr>
								</thead>
								<tbody>

									@if($transaction)
								            @foreach($transaction as $dt)

								                <tr>
													<th>{{ ++$i }}</th>
													<td>{{ $dt->code() }}</td>
													<td>{{ ($dt->user) ? $dt->user->name : "-" }}</td>
													<td>{{ ($dt->table) ? $dt->table->number : "-" }}</td>
													<td>{{ \MyDate::toReadableDate($dt->created_at, false, true) }}</td>
													<td class="text-center">{{ $dt->detail->count() }}</td>
													<td class="text-center">{{ $dt->detail->sum('quantity') }}</td>
													<td class="text-center strong">
														{{ \MyNumber::toReadableAngka($dt->grand_total, false) }}
													</td>
													<td>
														@if($dt->status=='pending')
															<span class="label label-info">Pending</span>
														@elseif($dt->status=='printbill')
															<span class="label label-warning">Print Bill</span>
														@elseif($dt->status=='finished')
															<span class="label label-success">Finished</span>
														@elseif($dt->status=='lost')
															<span class="label label-inverse">Lost</span>
														@else
															<span class="label label-danger">Void</span>
														@endif
													</td>
													<td class="text-right">
														@if($dt->status=='pending')
															<a href="{{ route('transaction.edit', $dt->transaction_id) }}" class="btn btn-sm btn-info"><span class="fa fa-edit"></span></a>
														@else
															<a href="{{ route('transaction-history.show', $dt->transaction_id) }}" class="btn btn-sm btn-primary"><span class="fa fa-eye"></span></a>
														@endif
														{{-- {!! Form::open(['method' => 'DELETE','route' => ['transaction-history.destroy', $dt->transaction_id],'style'=>'display:inline']) !!}
											            {!! Form::button('<span class="fa fa-trash"></span>', ['type' => 'submit', 'class' => 'btn btn-danger btn-sm', 'onclick'=>'return confirm("Are you sure?")']) !!}
											            {!! Form::close() !!} --}}
													</td>
												</tr>
								            @endforeach
								        </ul>
								    @else
								        <p>no data.</p>
								    @endif
								</tbody>
							</table>
						</div>

						{!! $transaction->appends(Input::except('page'))->render() !!}

					</div>

				</div>
			</div>
		</div>
		<!-- end row -->

    </div>
</div>

@endsection

@section('scripts')
	<script src="{{ URL::asset('assets/plugins/moment/moment.js') }}"></script>
	<script src="{{ URL::asset('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>

   <script type="text/javascript">

   		$(document).ready(function() {
   			$('#date_start').datetimepicker({
            	format: "DD-MM-YYYY"
            });
            $('#date_end').datetimepicker({
            	format: "DD-MM-YYYY",
            	useCurrent: false //Important! See issue #1075
            });

   			$('#date_start').on('dp.change', function(e) {
   				$('#date_end').data("DateTimePicker").minDate(e.date);
            	var date = $(this).val();
            	var url = "{{ Request::fullUrl() }}";
            	var query = "?date_start=" + date;
            	var end_param = getUrlParameter('date_end');
            	var user_param = getUrlParameter('user');
            	var status_param = getUrlParameter('sid');

            	if (end_param) {
            		query += "&date_end=" + end_param;
            	} else {
            		query += "&date_end=" + $("#date_end").val();
            	}

            	if (user_param) {
            		query += "&user=" + user_param;
            	}

            	if (status_param) {
            		query += "&sid=" + status_param;
            	}

            	window.location.href = "{{ Request::url() }}" + query;
            });

   			$('#date_end').on('dp.change', function(e) {
            	var date = $(this).val();
            	var url = "{{ Request::fullUrl() }}";
            	var query = "?date_end=" + date;
            	var start_param = getUrlParameter('date_start');
            	var user_param = getUrlParameter('user');
            	var status_param = getUrlParameter('sid');

            	if (start_param) {
            		query += "&date_start=" + start_param;
            	} else {
            		query += "&date_start=" + $("#date_start").val();
            	}

            	if (user_param) {
            		query += "&user=" + user_param;
            	}

            	if (status_param) {
            		query += "&sid=" + status_param;
            	}

            	window.location.href = "{{ Request::url() }}" + query;
            });

			$('#user').change( function() {
			    var editor = $(this).val();
			    var url = "{{ Request::fullUrl() }}";
			    var query = "?user=" + editor;
			    var date_param = getUrlParameter('date');
            	var status_param = getUrlParameter('sid');

            	if (date_param) {
            		query += "&date=" + date_param;
            	}

            	if (status_param) {
            		query += "&sid=" + status_param;
            	}

            	window.location.href = "{{ Request::url() }}" + query;
			});

			$('#status').change( function() {
			    var status = $(this).val();
			    var url = "{{ Request::fullUrl() }}";
			    var query = "?status=" + status;
			    var date_param = getUrlParameter('date');
            	var editor_param = getUrlParameter('editor');

            	if (date_param) {
            		query += "&date=" + date_param;
            	}

            	if (editor_param) {
            		query += "&editor=" + editor_param;
            	}

            	window.location.href = "{{ Request::url() }}" + query;
			});

			$('#search-clear').click( function() {
                window.location.href = "{{ Request::url() }}";
			});

			$('#search-button').click( function() {
			    var val = $('#search-input').val();
                var url = "{{ Request::fullUrl() }}";
			    var param = getParameterByName('cid', url.replace("&amp;", '&'));
   				if(param!=="")
            		window.location.href = "{{ Request::url() }}?search=" + val + "&cid=" + param;
                else
                	window.location.href = "{{ Request::url() }}?search=" + val;
			});

			$('#search-input').keypress(function(e){
		        if(e.which == 13){//Enter key pressed
		            var val = $(this).val();
		            var url = "{{ Request::fullUrl() }}";
				    var param = getParameterByName('cid', url.replace("&amp;", '&'));
	   				if(param!=="")
            		window.location.href = "{{ Request::url() }}?search=" + val + "&cid=" + param;
                else
                	window.location.href = "{{ Request::url() }}?search=" + val;
		        }
		    });

		    var getUrlParameter = function(name) {
		        var url = window.location.href;
		        if (name) {
		            name = name.replace(/[\[\]]/g, "\\$&");
		            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		                results = regex.exec(url);
		            if (!results) return '';
		            if (!results[2]) return '';
		            return decodeURIComponent(results[2].replace(/\+/g, " "));
		        }
		    };

		    function getParameterByName( name,href )
			{
			  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
			  var regexS = "[\\?&]"+name+"=([^&#]*)";
			  var regex = new RegExp( regexS );
			  var results = regex.exec( href );
			  if( results == null )
			    return "";
			  else
			    return decodeURIComponent(results[1].replace(/\+/g, " "));
			}
   		});

   </script>

@endsection