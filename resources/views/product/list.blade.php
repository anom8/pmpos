@extends('layouts.layout')

@section('title', 'Product')

@section('content')

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Product</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('/') }}">Dashboard</a>
                        </li>
                        <li class="active">
                            Product
                        </li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- end row -->

        <div class="row">
			<div class="col-sm-12">
				<div class="pull-right">
	                <a class="btn btn-success" href="{{ route('product.create') }}"> Add New Product</a>
	            </div>
			</div>
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
										{{-- <th data-priority="4" width="15%">Image</th> --}}
										<th data-priority="1" width="">Name</th>
										<th data-priority="2" width="20%">Sub Category</th>
										<th data-priority="2" width="20%">Printer</th>
										<th data-priority="2" width="10%">Price</th>
										<th data-priority="2" width="10%">Price Gojek</th>
										<th data-priority="5" width="15%" class="text-right">Action</th>
									</tr>
								</thead>
								<tbody>

									@if($product)
								            @foreach($product as $dt)

								                <tr>
													<th>{{ ++$i }}</th>
													{{-- @if ($dt->image!=null)
														<td>{{ HTML::image($dt->thumbnail('small'), null, array('class' => 'img-thumbnail', 'width'=>'100%')) }}</td>
													@else
														<td>{{ HTML::image('assets/images/default_avatar.png', null, array('class' => 'img-thumbnail', 'width'=>'100%')) }}</td>
													@endif --}}
													<td>{{ $dt->name }}</td>
													<td>{{ $dt->sub_category->name }}</td>
													<td>{{ $dt->printer ? $dt->printer->name : '-' }}</td>
													<td>{{ \MyNumber::toReadableHarga($dt->price, false) }}</td>
													<td>{{ \MyNumber::toReadableHarga($dt->price_gojek, false) }}</td>
													<td class="text-right">
														<a href="{{ route('product.edit', $dt->product_id) }}" class="btn btn-sm btn-info"><span class="fa fa-edit"></span></a>
														{!! Form::open(['method' => 'DELETE','route' => ['product.destroy', $dt->product_id],'style'=>'display:inline']) !!}
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

						{!! $product->render() !!}

					</div>

				</div>
			</div>
		</div>
		<!-- end row -->

    </div>
</div>

@endsection