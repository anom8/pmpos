@extends('layouts.layout')

@section('title', 'User')

@section('content')

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">User</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('/') }}">Dashboard</a>
                        </li>
                        <li class="active">
                            User
                        </li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- end row -->
        <div class="row">
        	<div class="col-md-5">
        		Statistic: <p style="margin-bottom: 5px"></p>
        		<div style="font-size: 13px">
	        		@if(!isset($_GET['sid']))
	        			<strong>All ({{ \MyNumber::toReadableAngka($total_user->total, false) }})</strong>
	    			@else
						<a href="{{ URL::to('user') }}">All</a> ({{ \MyNumber::toReadableAngka($total_user->total, false) }})
					@endif

					&nbsp;|&nbsp;

					@if(isset($_GET['sid']) && $_GET['sid']==0)
	        			<strong>Active ({{ \MyNumber::toReadableAngka($total_user->active, false) }})</strong>
	    			@else
						<a href="{{ URL::to('user?sid=0') }}">Active</a> ({{ \MyNumber::toReadableAngka($total_user->active, false) }})
					@endif

					&nbsp;|&nbsp;

					@if(isset($_GET['sid']) && $_GET['sid']==1)
	        			<strong>Not Active ({{ \MyNumber::toReadableAngka($total_user->inactive, false) }})</strong>
	    			@else
						<a href="{{ URL::to('user?sid=1') }}">Not Active</a> ({{ \MyNumber::toReadableAngka($total_user->inactive, false) }})
					@endif
				</div>
            </div>

			<div class="col-md-5">
				Search:
				<br />
				<div class="input-group">
                	{{ Form::text('search', (isset($_GET['search'])) ? $_GET['search']:"", array('class' => 'form-control input-sm', 'id' => 'search-input')) }}
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-sm waves-effect waves-light btn-default" id="search-clear" data-toggle="tooltip" title="Clear"><i class="fa fa-remove"></i></button>
                    <button type="button" class="btn btn-sm waves-effect waves-light btn-default" id="search-button" data-toggle="tooltip" title="Search"><i class="fa fa-search"></i></button>
                    </span>
                </div>
			</div>
            
            <div class="col-md-2">
            	<br />
        		<a class="btn btn-success btn-block btn-sm" href="{{ route('user.create') }}"> Add New User</a>
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
							<table id="tech-companies-1" class="table table-striped" width="100%">
								<thead>
									<tr>
										<th>#</th>
										<th data-priority="1" width="">Name &amp; Email</th>
										<th data-priority="2" width="15%">Phone</th>
										<th data-priority="2" width="15%">Status</th>
										<th data-priority="5" width="20%" class="text-right">Action</th>
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

									@if($usr)
								            @foreach($usr as $dt)

							            	@if($dt->banned==1)
							                	<tr class="danger">
						                	@else
						                		<tr>
					                		@endif
													<th>{{ $dt->rownum }}</th>
													<td>{!! $dt->name ."<br /> <a href='mailto:".$dt->email."'>". $dt->email ."</a>" !!}</td>
													<td>{{ $dt->phone }}</td>
													<td>
														<div class="col-md-12">
															@if($dt->status==0)
																<span class="label label-danger">User Not Activated</span>
															@else
																<span class="label label-success">User Activated</span>
															@endif
														</div>
													</td>
													<td class="text-right">
															<div class="col-md-12">
																<a href="{{ route('user.edit', $dt->user_id) }}" class="btn btn-sm btn-info" data-toggle="tooltip" title="Edit User"><span class="fa fa-edit"></span></a>
																{!! Form::open(['method' => 'DELETE','route' => ['user.destroy', $dt->user_id],'style'=>'display:inline']) !!}
													            {!! Form::button('<span class="fa fa-trash"></span>', ['type' => 'submit', 'class' => 'btn btn-danger btn-sm', 'onclick'=>'return confirm("Are you sure?")', 'data-toggle'=>'tooltip', 'title'=>'Delete User']) !!}
													            {!! Form::close() !!}

															</div>
														{{-- </div> --}}
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

						{!! $usr->render() !!}

					</div>

				</div>
			</div>
		</div>
		<!-- end row -->

    </div>
</div>

@endsection

@section('scripts')

   <script type="text/javascript">
   	
   		$(document).ready(function() {
			$('#sid').change( function() {
			    var val = $(this).val();
			   	if(val!="")
                	window.location.href = "{{ Request::url() }}?sid=" + val;
                else
                	window.location.href = "{{ Request::url() }}";
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