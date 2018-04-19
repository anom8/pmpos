@extends('layouts.layout')

@section('title', 'Edit Category')

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
                        <li>
                            <a href="{{ route('category.index') }}">Category</a>
                        </li>
                        <li class="active">
                            Edit
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

					{!! Form::model($cat, ['method' => 'PATCH','class' => 'form-horizontal', 'route' => ['category.update', $cat->id_cat]]) !!}

			        	{{ Form::hidden('_token', csrf_token()) }}

					    <div class="form-group">
					        {{ Form::label('category_name', 'Name', array('class' => 'col-md-2 control-label', 'for' => 'category_name')) }}
					        <div class="col-md-10">
					        	{{ Form::text('category_name', Input::old('category_name'), array('class' => 'form-control')) }}
					        </div>
					    </div>

					    <div class="form-group">
					        {{ Form::label('color', 'Color', array('class' => 'col-md-2 control-label', 'for' => 'color')) }}
					        <div class="col-md-10">
					        	<div class="input-group bootstrap-touchspin">
						        	{{-- <input id="demo1" type="text" value="55" name="demo1" class="form-control" style="display: block;"> --}}
						        	{{ Form::text('color', Input::old('color'), array('class' => 'form-control')) }}
						        	<span class="input-group-addon bootstrap-touchspin-postfix" style="background-color: rgb({{ $cat->color }})" id="color-preview">&nbsp;&nbsp;&nbsp;</span>
					        	</div>
					        </div>
					    </div>

					    <hr />
						
						<div class="form-group">
	                        <div class="col-sm-offset-2 col-sm-10">
					    		{{ Form::submit('Update Category!', array('class' => 'btn btn-primary')) }}
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