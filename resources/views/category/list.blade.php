@extends('layouts.layout')

@section('title', 'Category')

@section('content')

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Category</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('/') }}">Dashboard</a>
                        </li>
                        <li class="active">
                            Category
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
	                <a class="btn btn-success" href="{{ route('category.create') }}"> Add New Category</a>
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
										<th width="10%">ID</th>
										<th data-priority="1" width="">Name</th>
										<th data-priority="1" width="10%" class="text-center">Color</th>
										<th data-priority="4" width="10%" class="text-right">Action</th>
									</tr>
								</thead>
								<tbody>
									<!-- <tr>
										<th>1</th>
										<td>Http://google.com</td>
										<td>Lorem ipsum</td>
										<td></td>
										<td>
											<a href="" class="btn btn-sm btn-info"><span class="fa fa-edit"></span></a>
											<a href="" class="btn btn-sm btn-danger"><span class="fa fa-trash"></span></a>
										</td>
									</tr> -->

									@if($cat)
								            @foreach($cat as $dt)

								                <tr>
													<th>{{ $dt->id_cat }}</th>
													<td>{{ $dt->category_name }}</td>
													<td style="background-color: rgb({{ $dt->color }})"></td>
													<td class="text-right">
														<a href="{{ route('category.edit', $dt->id_cat) }}" class="btn btn-sm btn-info"><span class="fa fa-edit"></span></a>
														{!! Form::open(['method' => 'DELETE','route' => ['category.destroy', $dt->id_cat],'style'=>'display:inline']) !!}
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

						{!! $cat->render() !!}

					</div>

				</div>
			</div>
		</div>
		<!-- end row -->

    </div>
</div>

@endsection