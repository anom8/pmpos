@extends('layouts.layout')

@section('title', 'Create User')

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
                        <li>
                            <a href="{{ route('user.index') }}">User</a>
                        </li>
                        <li class="active">
                            Create
                        </li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- end row -->

       	<div class="row">
			<div class="col-sm-12">
				<div class="card-box">

					{{ HTML::ul($errors->all()) }}

					{{ Form::open(array('url' => 'user', 'class' => 'form-horizontal', 'files'=>true, 'method' => 'POST')) }}

			        	{{ Form::hidden('_token', csrf_token()) }}

					    <div class="form-group">
					        {{ Form::label('name', 'Name', array('class' => 'col-md-2 control-label', 'for' => 'name')) }}
					        <div class="col-md-10">
					        	{{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
					        </div>
					    </div>
					    <div class="form-group">
					        {{ Form::label('email', 'Email', array('class' => 'col-md-2 control-label', 'for' => 'email')) }}
					        <div class="col-md-10">
					        	{{ Form::text('email', Input::old('email'), array('class' => 'form-control')) }}
					        </div>
					    </div>
					    <div class="form-group">
					        {{ Form::label('phone', 'Phone', array('class' => 'col-md-2 control-label', 'for' => 'phone')) }}
					        <div class="col-md-10">
					        	{{ Form::number('phone', Input::old('phone'), array('class' => 'form-control')) }}
					        </div>
					    </div>
					    <div class="form-group">
					        {{ Form::label('password', 'Password', array('class' => 'col-md-2 control-label', 'for' => 'password')) }}
					        <div class="col-md-10">
					        	{{ Form::password('password', array('class' => 'form-control')) }}
					        </div>
					    </div>

					    <div class="form-group">
					        {{ Form::label('role_id', 'Role', array('class' => 'col-md-2 control-label', 'for' => 'role_id')) }}
					        <div class="col-md-10">
					        	{{ Form::select('role_id', $role, 1, array('class' => 'form-control')) }}
					        </div>
					    </div>
					    
					    @if(\Auth::user()->role_id==1)
						    <div class="form-group">
						        {{ Form::label('status', 'Status', array('class' => 'col-md-2 control-label', 'for' => 'status')) }}
						        <div class="col-md-10">
						        	{{ Form::select('status', [0 => 'Tidak Aktif', 1 => 'Aktif'], 1, array('class' => 'form-control')) }}
						        </div>
						    </div>
					    @endif
					    
						<div class="form-group">
	                        <div class="col-sm-offset-2 col-sm-10">
					    		{{ Form::submit('Add User!', array('class' => 'btn btn-primary')) }}
	                        </div>
	                    </div>

					{{ Form::close() }}

				</div>
			</div>
		</div>
		<!-- end row -->

    </div>
</div>

@endsection

@section('scripts')
	<script src="{{ URL::asset('assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js') }}"></script>

    <script>

        $(document).ready(function(){

            $(":file").filestyle({input: false});

        });
    </script>

@endsection