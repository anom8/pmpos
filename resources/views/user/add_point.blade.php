@extends('layouts.layout')

@section('title', 'Add Point User')

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
                            Add Point
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

					{{ Form::open(array('url' => 'user/savePoint', 'class' => 'form-horizontal', 'files'=>true, 'method' => 'POST')) }}

			        	{{ Form::hidden('_token', csrf_token()) }}

					    <div class="form-group">
					        {{ Form::label('full_name', 'Name', array('class' => 'col-md-2 control-label', 'for' => 'full_name')) }}
					        <div class="col-md-10">
					        	{{ Form::text('full_name', Input::old('full_name'), array('class' => 'form-control disabled')) }}
					        </div>
					    </div>

					    <div class="form-group">
					        {{ Form::label('email', 'Email', array('class' => 'col-md-2 control-label', 'for' => 'email')) }}
					        <div class="col-md-10">
					        	{{ Form::text('email', Input::old('email'), array('class' => 'form-control disabled')) }}
					        </div>
					    </div>
					    
					    <hr />

					    <div class="form-group">
					        {{ Form::label('birthday', 'Birthday', array('class' => 'col-md-2 control-label', 'for' => 'birthday')) }}
					        <div class="col-md-10">
					        	{{ Form::date('birthday', \Carbon\Carbon::now(), array('class' => 'form-control')) }}
					        </div>
					    </div>
					    <div class="form-group">
					        {{ Form::label('location', 'Location', array('class' => 'col-md-2 control-label', 'for' => 'location')) }}
					        <div class="col-md-10">
					        	{{ Form::text('location', Input::old('location'), array('class' => 'form-control')) }}
					        </div>
					    </div>
					    <div class="form-group">
					        {{ Form::label('quotes', 'Quotes', array('class' => 'col-md-2 control-label', 'for' => 'quotes')) }}
					        <div class="col-md-10">
					        	{{ Form::textarea('quotes', Input::old('quotes'), array('class' => 'form-control', 'rows' => 3)) }}
					        </div>
					    </div>

					    <hr />

					    <div class="form-group">
					        {{ Form::label('facebook', 'Facebook', array('class' => 'col-md-2 control-label', 'for' => 'facebook')) }}
					        <div class="col-md-10">
					        	{{ Form::text('facebook', Input::old('facebook'), array('class' => 'form-control')) }}
					        </div>
					    </div>
					    <div class="form-group">
					        {{ Form::label('twitter', 'Twitter', array('class' => 'col-md-2 control-label', 'for' => 'twitter')) }}
					        <div class="col-md-10">
					        	{{ Form::text('twitter', Input::old('twitter'), array('class' => 'form-control')) }}
					        </div>
					    </div>
					    <div class="form-group">
					        {{ Form::label('gender', 'Gender', array('class' => 'col-md-2 control-label', 'for' => 'gender')) }}
					        <div class="col-md-10">
					        	{{ Form::select('gender', ['m' => 'Male', 'f' => 'Female'], 'm', array('class' => 'form-control')) }}
					        </div>
					    </div>

					    <hr />

					    <div class="form-group">
					        {{ Form::label('avatar', 'Avatar', array('class' => 'col-md-2 control-label', 'for' => 'avatar')) }}
				        	<div class="col-md-10">
					        	{{ Form::file('avatar', Input::old('avatar'), array('class' => 'filestyle', 'data-buttonname' => 'btn-default')) }}
					        </div>
					    </div>

					    <hr />
						
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