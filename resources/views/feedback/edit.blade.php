@extends('layouts.layout')

@section('title', 'Edit Point')

@section('content')

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Point</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('/') }}">Dashboard</a>
                        </li>
                        <li>
                            <a href="{{ route('point.index') }}">Point</a>
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

					{!! Form::model($point, ['method' => 'PATCH','class' => 'form-horizontal', 'route' => ['point.update', $point->id_point]]) !!}

			        	{{ Form::hidden('_token', csrf_token()) }}

                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-md-2 control-label', 'for' => 'name')) }}
                            <div class="col-md-10">
                                {{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
                            </div>
                        </div>

					    <div class="form-group">
					        {{ Form::label('value', 'Point', array('class' => 'col-md-2 control-label', 'for' => 'value')) }}
					        <div class="col-md-10">
					        	{{ Form::text('value', Input::old('value'), array('class' => 'form-control')) }}
					        </div>
					    </div>

					    <hr />
						
						<div class="form-group">
	                        <div class="col-sm-offset-2 col-sm-10">
					    		{{ Form::submit('Update Point!', array('class' => 'btn btn-primary')) }}
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
	<!-- Summernote CSS -->
	<script src="{{ URL::asset('assets/plugins/summernote/summernote.min.js') }}"></script>

	<script>

        jQuery(document).ready(function(){

            $('.summernote').summernote({
                height: 350,                 // set editor height
                minHeight: null,             // set minimum height of editor
                maxHeight: null,             // set maximum height of editor
                focus: false                 // set focus to editable area after initializing summernote
            });

            $('.inline-editor').summernote({
                airMode: true
            });

        });
    </script>

@endsection