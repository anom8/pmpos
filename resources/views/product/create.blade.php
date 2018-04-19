@extends('layouts.layout')

@section('title', 'Create Product')

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
                    <h4 class="page-title">Product</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li>
                            <a href="{{ route('/') }}">Dashboard</a>
                        </li>
                        <li>
                            <a href="{{ route('product.index') }}">Product</a>
                        </li>
                        <li class="active">
                            Add
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

					{{ Form::open(array('url' => 'product', 'class' => 'form-horizontal', 'files'=>true, 'method' => 'POST', 'autocomplete' => 'off')) }}

			        	{{ Form::hidden('_token', csrf_token()) }}

                        <div class="form-group">
                            {{ Form::label('sub_category_id', 'Sub Category', array('class' => 'col-md-2 control-label', 'for' => 'sub_category_id')) }}
                            <div class="col-md-10">
                                {{ Form::select('sub_category_id', $sub_category, 1, array('class' => 'form-control', 'id' => 'sub_category_id')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('printer_id', 'Printer', array('class' => 'col-md-2 control-label', 'for' => 'printer_id')) }}
                            <div class="col-md-10">
                                {{ Form::select('printer_id', $printer, null, array('class' => 'form-control', 'id' => 'printer_id')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('name', 'Name', array('class' => 'col-md-2 control-label', 'for' => 'name')) }}
                            <div class="col-md-10">
                                {{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
                            </div>
                        </div>

					    <div class="form-group">
					        {{ Form::label('rfid_code', 'RFID Code', array('class' => 'col-md-2 control-label', 'for' => 'rfid_code')) }}
					        <div class="col-md-10">
					        	{{ Form::text('rfid_code', Input::old('rfid_code'), array('class' => 'form-control')) }}
					        </div>
					    </div>

					    <div class="form-group">
					        {{ Form::label('description', 'Description', array('class' => 'col-md-2 control-label', 'for' => 'description')) }}
					        <div class="col-md-10">
					        	{{ Form::textarea('description', Input::old('description'), array('class' => 'form-control', 'rows' => '3')) }}
					        </div>
					    </div>

                        <hr />

                        <div class="form-group">
                            {{ Form::label('price', 'Price', array('class' => 'col-md-2 control-label', 'for' => 'price')) }}
                            <div class="col-md-10">
                                {{ Form::text('price', Input::old('price'), array('class' => 'form-control')) }}
                            </div>
                        </div>

                        <div class="form-group">
					        {{ Form::label('price_gojek', 'Price Gojek', array('class' => 'col-md-2 control-label', 'for' => 'price_gojek')) }}
					        <div class="col-md-10">
					        	{{ Form::text('price_gojek', Input::old('price_gojek'), array('class' => 'form-control')) }}
					        </div>
					    </div>

					    <div class="form-group">
					        {{ Form::label('image', 'Image', array('class' => 'col-md-2 control-label', 'for' => 'image')) }}
				        	<div class="col-md-10">
					        	{{ Form::file('image', Input::old('image'), array('class' => 'filestyle', 'data-buttonname' => 'btn-default')) }}
					        </div>
					    </div>

					    <hr />
						
						<div class="form-group">
	                        <div class="col-sm-offset-2 col-sm-10">
					    		{{ Form::submit('Add Product!', array('class' => 'btn btn-primary')) }}
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