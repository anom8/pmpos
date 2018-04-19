@extends('layouts.layout')

@section('title', 'User Profile')

@section('content')

<div class="content">
    <div class="container">
    	<div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">User Profile</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('/') }}">Dashboard</a>
                        </li>
                        <li>
                            <a href="{{ route('user.index') }}">User</a>
                        </li>
                        <li class="active">
                            Profile
                        </li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- end row -->

        @if (Session::has('message'))
		    <div class="alert alert-info">{!! Session::get('message') !!}</div>
	    @elseif (Session::has('success'))
	    	<div class="alert alert-success">{!! Session::get('success') !!}</div>
	    @elseif (Session::has('error'))
	    	<div class="alert alert-error">{!! Session::get('error') !!}</div>
		@endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-lg-3 col-md-4">
                            <div class="text-center card-box">
                                <div class="member-card">
                                    <div class="m-b-10 center-block">
                                        <td>{{ HTML::image($usr->profile->thumbnail('large'), null, array('class' => 'img-thumbnail', 'width'=>'100%')) }}</td>
                                    </div>

                                    <div class="clearfix"></div>

                                    <div class="">
                                        <h4 class="m-b-5">{{ $usr->full_name }}</h4>
                                        <p class="text-muted">{{ $usr->website }}</p>
                                    </div>

                                    <a href="{{ route('user.edit', $usr->id) }}" class="btn btn-success btn-sm w-sm waves-effect m-t-10 waves-light">Edit</a>
                                    @if($usr->banned==0)
										<a href="{{ route('user.ban', $usr->id) }}" class="btn btn-sm btn-danger w-sm waves-effect m-t-10 waves-light">Disable</a>
									@else
										<a href="{{ route('user.ban', $usr->id) }}" class="btn btn-sm btn-primary w-sm waves-effect m-t-10 waves-light">Enable</a>
									@endif

                                    <hr />

                                    <p class="text-muted font-13 m-t-20">
                                        {{ $usr->quotes }}
                                    </p>

                                    <hr />

                                    <div class="text-left">

	                                    <p class="text-muted font-13">
	                                        <h3>
		                                        <small>Point :</small>
		                                        <span class="pull-right">{{ $point }}</span>
	                                        </h3>
	                                    </p>

	                                    <hr />

                                        <p class="text-muted font-13">
                                        	<strong>Full Name :</strong> 
                                        	<br />
                                        	<span>{{ $usr->full_name }}</span>
                                    	</p>

                                        <p class="text-muted font-13">
                                        	<strong>Mobile :</strong>
                                        	<br />
                                        	<span>{{ $usr->phone }}</span>
                                    	</p>

                                        <p class="text-muted font-13">
	                                        <strong>Email :</strong>
	                                        <br />
	                                        <span>{{ $usr->email }}</span>
                                        </p>

                                        <p class="text-muted font-13">
	                                        <strong>Location :</strong>
	                                        <br />
	                                        <span>
	                                        	@if($usr->location)
	                                        		{{ $usr->location }}
                                        		@else
                                        			-
                                    			@endif
	                                        </span>
                                        </p>
                                    </div>

                                </div>

                            </div> <!-- end card-box 2 -->

                            <div class="text-center card-box">
                                <div class="member-card">

                                    <div class="row">
                            			<h4 class="header-title m-t-0 m-b-30 text-left">Add Point</h4>
                                    	
                                    	{!! Form::model($usr, ['method' => 'PATCH','class' => 'form-horizontal', 'files'=>true, 'route' => ['user.savePoint', $usr->id]]) !!}

								        	{{ Form::hidden('_token', csrf_token()) }}

										    <div class="form-group">
										        <div class="col-md-8">
										        	{{ Form::text('point', Input::old('point'), array('class' => 'form-control input-sm', 'placeholder'=>'Point')) }}
										        </div>
										        <div class="col-md-4">
										        	{{ Form::submit('Add', array('class' => 'btn btn-sm btn-md btn-primary btn-block')) }}
										        </div>
										    </div>

										{{ Form::close() }}

                                    </div>

                                </div>

                            </div> <!-- end card-box 2 -->

                        </div> <!-- end col -->

                        <div class="col-md-8 col-lg-9">

                        	<div class="card-box">
                                <ul class="nav nav-tabs">
                                    <li>
                                        <a href="{{ route('user.show', $usr->id) }}">
                                            <span class="visible-xs"><i class="fa fa-file"></i></span>
                                            <span class="hidden-xs">Member's Post <strong>({{ $countUsrPost }})</strong></span>
                                        </a>
                                    </li>
                                    <li class="active">
                                        <a href="{{ route('user.points', $usr->id) }}">
                                            <span class="visible-xs"><i class="fa fa-dollar"></i></span>
                                            <span class="hidden-xs">Point History</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="points-post">
                                        
										<div class="table-rep-plugin">
											<div class="table-responsive" data-pattern="priority-columns">
												<table id="tech-companies-1" class="table  table-striped">
													<thead>
														<tr>
															<th width="10%">#</th>
															<th data-priority="1" width="">Activity</th>
															<th data-priority="2" width="30%">Date</th>
															<th data-priority="3" width="20%" class="text-right">Value</th>
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

														@if($usrPoint)
													            @foreach($usrPoint as $dt)

													                <tr>
																		<td>{{ ++$i }}</td>
																		@if($dt->post['id_usr_post']!=null)
																			<td>
																				{{ $dt->activity }}
																				<br />
																				<a href="{{ route('user-posts.edit', $dt->post['id_usr_post']) }}" target="_blank">
																					{{ $dt->post['title_post'] }}
																				</a>
																			</td>
																		@else
																			<td>{{ $dt->activity }}</td>
																		@endif
																		<td>{{ \MyDate::toReadableDate($dt->log_point, FALSE, TRUE) }}</td>
																		<td class="text-right">{{ $dt->value }}</td>
																	</tr>
													            @endforeach
													        </ul>
													    @else
													        <p>no data.</p>
													    @endif
													</tbody>
												</table>
											</div>

											{!! $usrPoint->appends(Input::except('page'))->render() !!}

										</div>

                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End row -->



    </div> <!-- container -->

</div> <!-- content -->

@endsection