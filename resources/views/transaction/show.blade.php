@extends('layouts.layout')

@section('title', 'Current Transaction')

@section('content')

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Transaction</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('/') }}">Dashboard</a>
                        </li>
                        <li class="active">
                            Transaction
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
			<div class="col-sm-3">

			</div>

			<div class="col-sm-9">
				<div class="card-box">

					<div class="row">
						<div class="col-md-6 pull-right">
							<div class="row">
								<div class="col-md-12">Date
								<span class="strong">: {{ \MyDate::toReadableDate($transaction->created_at, false) }}</span></div>
							</div>
						</div>
					</div>

					<hr>

					<div class="table-rep-plugin">
						<div class="table-responsive" data-pattern="priority-columns">
							<table id="tech-companies-1" class="table  table-striped">
								<thead>
									<tr>
										<th>ID</th>
										<th data-priority="4" width="15%">Image</th>
										<th data-priority="1" width="30%">product</th>
										<th data-priority="1" width="">Price</th>
										<th data-priority="1" width="">Quantity</th>
										<th data-priority="1" width="">Subtotal</th>
										<th data-priority="5" width="15%" class="text-right">Action</th>
									</tr>
								</thead>
								<tbody>

									@if($transaction)
								            @foreach($transaction->detail as $dt)

								                <tr>
													<th>{{ ++$i }}</th>

													@if ($dt->product->image!=null)
														<td>{{ HTML::image($dt->product->thumbnail('small'), null, array('class' => 'img-thumbnail', 'width'=>'100%')) }}</td>
													@else
														<td>{{ HTML::image('assets/images/default_avatar.png', null, array('class' => 'img-thumbnail', 'width'=>'100%')) }}</td>
													@endif

													<td>{{ $dt->product->name }}</td>
													<td>{{ \MyNumber::toReadableHarga($dt->price, false) }}</td>
													<td>{{ $dt->quantity }}</td>
													<td>{{ \MyNumber::toReadableHarga($dt->subtotal, false) }}</td>
													<td class="text-right">
														{{-- <a href="{{ route('transaction.changeStatus', $dt->id_transaction) }}" class="btn btn-sm btn-primary">Change Status</a> --}}
														{{-- <br /> --}}
														{{-- <br /> --}}
														<a href="{{ route('transaction.edit', $dt->id_transaction) }}" class="btn btn-sm btn-info"><span class="fa fa-edit"></span></a>
														{!! Form::open(['method' => 'DELETE','route' => ['transaction.destroy', $dt->id_transaction],'style'=>'display:inline']) !!}
											            {!! Form::button('<span class="fa fa-trash"></span>', ['type' => 'submit', 'class' => 'btn btn-danger btn-sm', 'onclick'=>'return confirm("Are you sure?")']) !!}
											            {!! Form::close() !!}
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
					</div>

				</div>
			</div>
		</div>
		<!-- end row -->

    </div>
</div>

@endsection