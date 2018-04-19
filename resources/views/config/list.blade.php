@extends('layouts.layout')

@section('title', 'Config')

@section('content')

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Config</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('/') }}">Dashboard</a>
                        </li>
                        <li class="active">
                            Config
                        </li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- end row -->

        {{-- <div class="row">
			<div class="col-sm-12">
				<div class="pull-right">
	                <a class="btn btn-success" href="{{ route('config.create') }}"> Add New Config</a>
	            </div>
			</div>
        </div> --}}

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
										<th width="5%">ID</th>
										<th data-priority="1">Name</th>
										<th data-priority="2" width="10%">Value</th>
										<th data-priority="3" width="10%" class="text-right">Action</th>
									</tr>
								</thead>
								<tbody>

									@if($config)
								            @foreach($config as $dt)

								                <tr>
													<th>{{ ++$i }}</th>
													<td>{{ $dt->name }}</td>
													<td>{{ $dt->value }}</td>
													<td class="text-right">
														<a href="{{ route('config.edit', $dt->id_config) }}" class="btn btn-sm btn-info"><span class="fa fa-edit"></span></a>
														{!! Form::open(['method' => 'DELETE','route' => ['config.destroy', $dt->id_config],'style'=>'display:inline']) !!}
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

						{!! $config->render() !!}

					</div>

				</div>
			</div>
		</div>
		<!-- end row -->

    </div>
</div>

@endsection