@extends('layouts.layout')

@section('title', 'Create Pages')

@section('styles')
	<!-- Summernote CSS -->
	<link href="{{ URL::asset('assets/plugins/summernote/summernote.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Pages</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('/') }}">Dashboard</a>
                        </li>
                        <li>
                            <a href="{{ route('pages.index') }}">Pages</a>
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

					{{ Form::open(array('url' => 'pages', 'class' => 'form-horizontal', 'method' => 'POST')) }}

			        	{{ Form::hidden('_token', csrf_token()) }}

					    <div class="form-group">
					        {{ Form::label('name', 'Name', array('class' => 'col-md-2 control-label', 'for' => 'name')) }}
					        <div class="col-md-10">
					        	{{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
					        </div>
					    </div>

					    <div class="form-group">
					        {{ Form::label('content', 'Content', array('class' => 'col-md-2 control-label', 'for' => 'content')) }}
					        <div class="col-md-10">
					        	{{ Form::textarea('content', Input::old('content'), array('class' => 'summernote')) }}
					        </div>
					    </div>

					    <hr />
						
						<div class="form-group">
	                        <div class="col-sm-offset-2 col-sm-10">
					    		{{ Form::submit('Add Page!', array('class' => 'btn btn-primary')) }}
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