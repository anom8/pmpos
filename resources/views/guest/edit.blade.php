@extends('layouts.layout')

@section('title', 'Edit Guest')

@section('styles')
	<!-- Summernote CSS -->
	<link href="{{ URL::asset('assets/plugins/summernote/summernote.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
@endsection

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
                        <li>
                            <a href="{{ route('guest.index') }}">Guest</a>
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

					{!! Form::model($guest, ['method' => 'PATCH','class' => 'form-horizontal', 'autocomplete' => 'off', 'files'=>true, 'route' => ['guest.update', $guest->guest_id]]) !!}

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
					        	{{ Form::text('phone', Input::old('phone'), array('class' => 'form-control')) }}
					        </div>
					    </div>

					    <div class="form-group">
					        {{ Form::label('domicile', 'Domicile', array('class' => 'col-md-2 control-label', 'for' => 'domicile')) }}
					        <div class="col-md-10">
					        	{{ Form::text('domicile', Input::old('domicile'), array('class' => 'form-control')) }}
					        </div>
					    </div>

					    <hr />
						
						<div class="form-group">
	                        <div class="col-sm-offset-2 col-sm-10">
					    		{{ Form::submit('Update Guest!', array('class' => 'btn btn-primary')) }}
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

    <script src="{{ URL::asset('assets/plugins/moment/moment.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/multiselect/js/jquery.multi-select.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/jquery-quicksearch/jquery.quicksearch.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    

    <script src="{{ URL::asset('assets/plugins/timepicker/bootstrap-timepicker.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    {{-- <script src="{{ URL::asset('assets/pages/jquery.form-advanced.init.js') }}"></script> --}}
    <script type="text/javascript">
        $(document).ready(function () {
            $('#datepicker').datepicker({
            	autoclose: true,
            	startDate: '1d',
                format: "dd MM yyyy",
                clearBtn: true,
            });

            $(":file").filestyle({input: false});

        });
    </script>
@endsection