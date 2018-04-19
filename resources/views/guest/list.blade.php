@extends('layouts.layout')

@section('title', 'Guest')

@section('content')

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Guest</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('/') }}">Dashboard</a>
                        </li>
                        <li class="active">
                            Guest
                        </li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- end row -->

        <div class="row">
			<div class="col-sm-12">
				{{-- <div class="pull-right">
	                <a class="btn btn-success" href="{{ route('guest.create') }}"> Add New Guest</a>
	            </div> --}}
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
										<th data-priority="1" width="20%">Name</th>
										<th data-priority="1" width="">Email</th>
										<th data-priority="2" width="15%">Phone</th>
										<th data-priority="2" width="">Domicile</th>
										<th data-priority="5" width="15%" class="text-right">Action</th>
									</tr>
								</thead>
								<tbody>

									@if($guest)
								            @foreach($guest as $dt)

								                <tr>
													<th>{{ ++$i }}</th>
													<td>{{ $dt->name }}</td>
													<td>{{ $dt->email }}</td>
													<td>{{ $dt->phone }}</td>
													<td>{{ $dt->domicile }}</td>
													<td class="text-right">
														<a href="{{ route('guest.edit', $dt->guest_id) }}" class="btn btn-sm btn-info"><span class="fa fa-edit"></span></a>
														{!! Form::open(['method' => 'DELETE','route' => ['guest.destroy', $dt->guest_id],'style'=>'display:inline']) !!}
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

						{!! $guest->render() !!}

					</div>

				</div>
			</div>
		</div>
		<!-- end row -->

    </div>
</div>

@endsection