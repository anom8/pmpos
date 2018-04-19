@extends('layouts.layout')

@section('title', 'Dashboard')

@section('content')

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">Dashboard</h4>
                    
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">

                    Welcome to Padang Merdeka POS Dashboard!

                </div>
            </div>
        </div>
        <!-- end row -->

        <div class="row">
            @if(auth()->user()->role_id < 4)
                @if ($ob==null)
                    <div class="col-sm-4">

                        <div class="row">
                            <div class="col-sm-12">
                                <h5>Opening Balance</h5>
                                <div class="card-box">
                                    <div class="alert alert-danger">
                                        <i class="fa fa-exclamation-circle"></i> Masukan <strong>Opening Balance</strong>
                                    </div>

                                    {{ Form::open(array('url' => '/open-balance', 'method' => 'POST', 'autocomplete' => 'off')) }}

                                        <div class="form-group">
                                            {{ Form::label('balance', 'Opening Balance', array('class' => 'col-md-12 control-label', 'for' => 'balance')) }}
                                            <div class="col-md-12">
                                                {{ Form::text('balance', Input::old('balance'), array('class' => 'form-control')) }}
                                            </div>
                                        </div>

                                        <div class="clearfix"></div>
                                        <br>
                                        
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                {{ Form::submit('Simpan', array('class' => 'btn btn-primary btn-block')) }}
                                            </div>
                                        </div>

                                    {{ Form::close() }}
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-sm-12">
                                <h5>Opening Balance</h5>
                                <div class="card-box">
                                    <div class="col-md-12">
                                        <h3 class="text-danger">{{ \MyNumber::toReadableHarga($ob->balance, false, true) }}</h3>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-12">
                        <h5>Tanggal</h5>
                        <div class="card-box">
                            <div class="col-md-12">
                                <h3 class="text-primary">{{ \MyDate::toReadableDate(date('Y-m-d')) }}</h3>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($ob!=null)
                @if ($ob->close_at==null)
                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-sm-12">
                                <h5>Close Balance</h5>
                                <div class="card-box">
                                    <div class="col-md-12">
                                        <a href="{{ route('close-balance') }}" class="btn btn-lg btn-danger btn-block">Close Balance</a>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-sm-4">
                    <div class="row">
                        <div class="col-sm-12">
                            <h5>Print Transaction History Today</h5>
                            <div class="card-box">
                                <div class="col-md-12">
                                    <a href="{{ route('print-daily-history') }}" class="btn btn-lg btn-success btn-block">Print History</a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        
        <!-- end row -->

    </div>
</div>

@endsection