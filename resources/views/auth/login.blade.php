@extends('layouts.layout_login')

@section('title', 'Login')

@section('content')

<!-- HOME -->
        <section>
            <div class="container-alt">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="wrapper-page">

                            <div class="m-t-40 account-pages">
                                <div class="text-center account-logo-box" style="background-color: #ddd">
                                    <h2 class="text-uppercase">
                                        MB POS
                                    </h2>
                                    <!--<h4 class="text-uppercase font-bold m-b-0">Sign In</h4>-->
                                </div>
                                <div class="account-content">
                                    <!-- <form class="form-horizontal" action="#"> -->
                                    {{ Form::open(array('url' => 'login', 'class' => 'form-horizontal', 'autocomplete' => 'off')) }}

                                    @if (Session::has('message'))
                                        <div class="alert alert-danger">{{ Session::get('message') }}</div>
                                    @endif

                                    <p>
                                        {{ $errors->first('email') }}
                                        {{ $errors->first('password') }}
                                    </p>

                                        <div class="form-group ">
                                            <div class="col-xs-12">
                                                <!-- <input class="form-control" type="text" required="" placeholder="Username"> -->
                                                {{ Form::text('email', Input::old('email'), array('class' => 'form-control', 'placeholder' => 'Email', 'autofocus' => 'autofocus')) }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <!-- <input class="form-control" type="password" required="" placeholder="Password"> -->
                                                {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password')) }}
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <div class="col-xs-12">
                                                <div class="checkbox checkbox-success">
                                                    <input id="checkbox-signup" type="checkbox" checked name="remember">
                                                    <label for="checkbox-signup">
                                                        Remember me
                                                    </label>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="form-group account-btn text-center m-t-10">
                                            <div class="col-xs-12">
                                                <!-- <button class="btn w-md btn-bordered btn-danger waves-effect waves-light" type="submit">Log In</button> -->
                                                {{ Form::button('Log In', array('type' => 'submit', 'class' => 'btn w-md btn-bordered btn-danger waves-effect waves-light')) }}
                                            </div>
                                        </div>

                                    <!-- </form> -->
                                    {{ Form::close() }}

                                    <div class="clearfix"></div>

                                </div>
                            </div>
                            <!-- end card-box-->

                        </div>
                        <!-- end wrapper -->

                    </div>
                </div>
            </div>
          </section>
          <!-- END HOME -->

@endsection