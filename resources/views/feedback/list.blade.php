@extends('layouts.layout')

@section('title', 'Feedback')

@section('content')

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Feedback</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('/') }}">Dashboard</a>
                        </li>
                        <li class="active">
                            Feedback
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
	                <a class="btn btn-success" href="{{ route('point.create') }}"> Add New Point</a>
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
										<th data-priority="1" width="25%">Name &amp; Email</th>
										<th data-priority="3" width="10%">Subject</th>
										<th data-priority="3" width="">Message</th>
										<th data-priority="3" width="10%">Date</th>
										<th data-priority="4" width="10%" class="text-right">Action</th>
									</tr>
								</thead>
								<tbody>

									@if($feedback)
								            @foreach($feedback as $dt)

								                <tr>
													<th>{{ ++$i }}</th>
													<td>
														@if($dt->user)
															<a href="{{ route('user.show', $dt->id_usr) }}">
																<span class="glyphicon glyphicon-user"></span>
																{{ $dt->user->full_name }}
															</a>
															<br />
														@endif
														<a href="mailto:{{ $dt->email }}">
															<span class="glyphicon glyphicon-envelope"></span>
															{{ $dt->email }}
														</a>
													</td>
													<td>{{ $dt->subject }}</td>
													<td><p>{{ $dt->desc }}</p></td>
													<td>{{ $dt->log_feedback }}</td>
													<td class="text-right">
														{!! Form::open(['method' => 'DELETE','route' => ['feedback.destroy', $dt->id_feedback],'style'=>'display:inline']) !!}
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

						{!! $feedback->render() !!}

					</div>

				</div>
			</div>
		</div>
		<!-- end row -->

    </div>
</div>

@endsection