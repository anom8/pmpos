@extends('layouts.layout')

@section('title', 'Edit Order')

@section('styles')
    <!-- Summernote CSS -->
    <link href="{{ URL::asset('assets/plugins/summernote/summernote.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="content">
    <div class="container">
        <br>
        <div class="row" id="loading">
            <div class="alert alert-info">
                <i class="fa fa-spinner fa-spin"></i> Loading...
            </div>
        </div>

        <div class="row" id="content" style="display: none">
            <!-- Chosen Item -->
            <div class="col-sm-7">
                <h5>Edit Order</h5>
                <div class="card-box">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Table No:</label>
                                </div>
                                <div class="col-md-12">
                                    {{ Form::select('table_no', $table, $transaction->table_id, array('class' => 'form-control input-sm ', 'id' => 'table_no')) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Type</label>
                                </div>
                                <div class="col-md-12">
                                    {{ Form::select('price', ['disajikan'=>'Dine In (HIDANG)', 'rames'=>'Dine In (KHUSUS)', 'takeaway'=>'Takeaway'], $transaction->type, array('class' => 'form-control input-sm ', 'id' => 'type')) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Price Category</label>
                                </div>
                                <div class="col-md-12">
                                    {{ Form::select('price', ['general'=>'Outlet', 'gojek'=>'Gojek'], $transaction->price_category, array('class' => 'form-control input-sm ', 'id' => 'price')) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>

                    <!-- Table -->
                    <div class="table-rep-plugin" id="cart">
                        <div class="table-responsive" data-pattern="priority-columns">
                            <table id="list-order" class="table table-striped">
                                <thead>
                                    <tr>
                                        {{-- <th>#</th> --}}
                                        <th data-priority="1" width="30%">Menu</th>
                                        {{-- <th data-priority="1" width="15%">Price</th> --}}
                                        <th data-priority="1" width="1%">Qty</th>
                                        <th data-priority="1" width="15%">Subtotal</th>
                                        <th data-priority="1" width="">Note</th>
                                        <th data-priority="5" width="20%" class="text-right">Action</th>
                                    </tr>
                                </thead>
                                @if ($transaction)
                                    <tbody>
                                        @foreach ($transaction->detail as $t)
                                            <tr id="{{ $t->product_id }}" class="order" data-printer="{{ $t->product->printer_id }}" data-price="{{ $t->product->price }}" data-pricegojek="{{ $t->product->price_gojek }}" data-qty="{{ $t->quantity }}">
                                                <td class="strong"><h5>{{ $t->product->name }}</h5></td>
                                                <td class="qty"><h5>{{ $t->quantity }}</h5></td>
                                                <td class="subtotal"><h5>{{ \MyNumber::toReadableAngka($t->subtotal, false, true) }}</h5></td>
                                                <td class="note"><input class="form-control note input-sm" id="note_{{ $t->product_id }}" value="{{ $t->note }}"></td>
                                                <td class="text-right">
                                                    <button data-id="{{ $t->product_id }}" data-price="{{ $t->price }}" class="btn btn-warning btn-xs reduce"><i class="fa fa-minus"></i></button>
                                                    <button data-id="{{ $t->product_id }}" data-price="{{ $t->price }}" class="btn btn-success btn-xs add"><i class="fa fa-plus"></i></button>
                                                    <button data-id="{{ $t->product_id }}" data-price="{{ $t->price }}" class="btn btn-danger btn-xs remove"><i class="fa fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @endif
                            </table>
                        </div>
                    </div>
                    <!-- End Table -->
                </div>
            </div>

            <!-- Items -->
            <div class="col-sm-5">
                <h5>Item List <small class="text-danger pull-right"><i class="fa fa-exclamation-circle"></i> Click items to add to Order</small></h5>
                <div class="card-box">
                    <div class="row">
                        <ul class="nav nav-tabs">
                            @foreach($sub_categories as $sub_category)
                                <li {{ $loop->iteration == 1 ? 'class="active"' : '' }}>
                                   <a href="#{{ $sub_category->sub_category_id }}" data-toggle="tab" aria-expanded="{{ $loop->iteration == 1 ? 'true' : '' }}">
                                        <span class="visible-xs"><i class="fa fa-home"></i></span>
                                        <span class="hidden-xs">{{ $sub_category->name }}</span>
                                    </a>    
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach($sub_categories as $sub_category)
                                <div class='tab-pane {{ $loop->iteration == 1 ? "active" : "" }}' id="{{ $sub_category->sub_category_id }}">
                                    @foreach($products as $product)
                                        @if($sub_category->sub_category_id == $product->sub_category_id)
                                            <div class="" style="margin-bottom: 10px; float: left; display: inline; margin-right: 10px;">
                                                <button class="btn btn-inverse btn-block btn-sm product-list" data-id="{{ $product->product_id }}" data-name="{{ $product->name }}" data-price="{{ $product->price + 0 }}" data-pricegojek="{{ $product->price_gojek + 0 }}" data-printer="{{ ($product->printer_id) ? $product->printer_id:null }}">
                                                    <strong>{{ strtoupper($product->name) }}</strong>
                                                    {{-- <br>
                                                    <small>{{ $product->price }}</small> --}}
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    <!-- @foreach($products as $product)
                        <div class="col-md-4" style="margin-bottom: 10px;">
                            <button class="btn btn-inverse btn-block btn-sm product-list" data-id="{{ $product->product_id }}" data-name="{{ $product->name }}" data-price="{{ $product->price + 0 }}">
                                <strong>{{ strtoupper($product->name) }}</strong>
                                {{-- <br>
                                <small>{{ $product->price }}</small> --}}
                            </button>
                        </div>
                    @endforeach -->
                    </div>
                </div>
            </div>

            {{-- <div class="col-md-12"> --}}
                <div id="new-order-bottom">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-5"><h5>Subtotal</h5></div>
                                <div class="col-md-7 text-right">
                                    <h5 id="total">{{ \MyNumber::toReadableHarga($transaction->total, false, true) }}</h5>
                                    <input type="hidden" name="total_value" value="{{ $transaction->total }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><h5>Pajak</h5></div>
                                <div class="col-md-8 text-right"><h5>10%</h5></div>
                            </div>
                            <div class="row">
                                <div class="col-md-5"><h5>Grand Total</h5></div>
                                <div class="col-md-7 text-right">
                                    <h5 id="grand-total" class="text-primary">{{ \MyNumber::toReadableHarga($transaction->grand_total, false, true) }}</h5>
                                    <input type="hidden" name="grand_total_value" value="{{ $transaction->grand_total }}"
                                    >
                                    <input type="hidden" name="discount_value" value="{{ $transaction->discount }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-5"><h5>Metode</h5></div>
                                <div class="col-md-7 text-right">
                                    {{ Form::select('payment_method', $payment_method, $transaction->payment_method_id, array('class' => 'form-control', 'id' => 'payment_method')) }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5"><h5>Note</h5></div>
                                <div class="col-md-7 text-right">
                                    <input type="text" name="note" id="note" class="form-control" placeholder="" value="{{ $transaction->note }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5"><h5>Name</h5></div>
                                <div class="col-md-7 text-right">
                                    <input type="text" name="name" id="name" class="form-control" placeholder="" value="{{ $transaction->name }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-5"><h5>Promosi</h5></div>
                                <div class="col-md-7">
                                    <select name="promotion" id="promotion" class="select2">
                                        <option value="0" title="-" <?=($transaction->promotion_id==0 || $transaction->promotion_id==null) ? "selected":"" ?>>-</option>
                                        @if($promotion)
                                            @foreach($promotion as $promo)
                                                <option value="{{ $promo->promotion_id }}" title="{{ $promo->description }}" data-value="{{ $promo->value }}" data-type="{{ $promo->type }}" <?=($transaction->promotion_id==$promo->promotion_id) ? "selected":"" ?>>{{ $promo->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5"><h5>Bayar</h5></div>
                                <div class="col-md-7 text-right">
                                    <input type="number" class="form-control" id="paid" style="text-align: right;" value="{{ $transaction->paid }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5"><h5>Kembalian</h5></div>
                                <div class="col-md-7 text-right">
                                    <h5 id="payable" class="text-danger">{{ \MyNumber::toReadableHarga($transaction->payable, false, true) }}</h5>
                                    <input type="hidden" name="payable" value="{{ $transaction->payable }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-success btn-lg btn-block" id="save_btn">
                                        Save
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-danger btn-lg btn-block" id="send_to_kitchen" disabled="disabled">
                                        To Kitchen
                                    </button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-info btn-lg btn-block" id="print_bill">
                                        Print Bill
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-primary btn-lg btn-block" id="finish_order" disabled="disabled">
                                        Finish
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {{-- </div> --}}
        </div>
        <!-- end row -->

    </div>
</div>

@endsection

@section('scripts')
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript">
        var sendToKitchenList = [];
        var transaction_id = {{ $transaction->transaction_id }};
        $(document).ready(function () {
            $('#loading').hide();
            $('#content').show();

            $("input[type=number]").focus(function() { $(this).select(); } );

            $("#promotion").select2({
                templateResult: function(option) {
                    var $option = $(
                      '<div><strong>' + option.text + '</strong></div><div>' + option.title + '</div>'
                    );
                    return $option;
                }
            });

            if($('#paid').val() != '') {
                $('#finish_order').prop('disabled', false);
            }

            $('#save_btn').on('click', function() {
                // console.log(sendToKitchenList);
                var btn = $(this);
                btn.html('<i class="fa fa-spinner fa-spin"></i>');
                btn.prop('disabled', true);

                var order = {
                    'user_id': '{{ auth()->user()->user_id }}',
                    'table_id': $('#table_no :selected').val(),
                    'promotion_id': $('#promotion :selected').val(),
                    'type': $('#type :selected').val(),
                    'payment_method': $('#payment_method :selected').val(),
                    'price': $('#price :selected').val(),
                    'payable': $('input[name="payable"]').val(),
                    'total': $('input[name="total_value"]').val(),
                    'grand_total': $('input[name="grand_total_value"]').val(),
                    'discount': $('input[name="discount_value"]').val(),
                    'paid': $('#paid').val(),
                    'note': $('#note').val(),
                    'name': $('#name').val(),
                    'status': 'pending',
                    'items': [],
                    'kitchen_list': []
                }
                $( ".order" ).each(function(index) {
                    tmp = {
                        id: $(this).attr('id'),
                        quantity: $(this).data('qty'),
                        price: $(this).data('price'),
                        note: $('#note_' + $(this).attr('id')).val(),
                        printer: $(this).data('printer'),
                        subtotal: parseInt($(this).find('.qty').text()) * parseInt($(this).data('price'))
                    };
                    order['items'][index] = tmp;
                });

                console.log(order);

                if (transaction_id != null)
                    var url = '{{ url("/") }}/update-order/'+transaction_id;
                else
                    var url = '{{ url("/") }}/new-order';

                $.ajax({
                   url: url,
                   data: {
                      data: order
                   },
                   type: 'post',
                   error: function() {
                      alert('error');
                   },
                   dataType: 'json',
                   success: function(data) {
                        if(data.status) {
                            // sendToKitchenList = [];
                            // console.log(data.transaction_id);
                            transaction_id = data.transaction_id;
                            table_id = data.table;
                            btn.html('Save');
                            btn.prop('disabled', false);
                            $('#print_bill').prop('disabled', false);
                            // btn.prop('disabled', true);
                            // btn.html('<i class="fa fa-check-circle"></i> Success');
                            // setTimeout(function() {
                            //     window.location = "{{ url('/') }}/transaction-current";
                            // }, 1000);

                            // const socket = io.connect("<?= env('SOCKET_URL', 'http://localhost:3003') ;?>");

                            // var data_send = {
                            //  "table_id": table_id,
                            //  "transaction_id": transaction_id
                            // };
                            // socket.emit('new transaction added dashboard', JSON.stringify(data_send));
                        }
                   }
                });
            });

            $('#print_bill').on('click', function() {
                // console.log(sendToKitchenList);
                var btn = $(this);
                btn.html('<i class="fa fa-spinner fa-spin"></i>');
                btn.prop('disabled', true);

                var order = {
                    'user_id': '{{ auth()->user()->user_id }}',
                    'table_id': $('#table_no :selected').val(),
                    'promotion_id': $('#promotion :selected').val(),
                    'type': $('#type :selected').val(),
                    'payment_method': $('#payment_method :selected').val(),
                    'price': $('#price :selected').val(),
                    'payable': $('input[name="payable"]').val(),
                    'total': $('input[name="total_value"]').val(),
                    'grand_total': $('input[name="grand_total_value"]').val(),
                    'discount': $('input[name="discount_value"]').val(),
                    'paid': $('#paid').val(),
                    'note': $('#note').val(),
                    'name': $('#name').val(),
                    'status': 'pending',
                    'items': [],
                    'kitchen_list': []
                }
                $( ".order" ).each(function(index) {
                    tmp = {
                        id: $(this).attr('id'),
                        quantity: $(this).data('qty'),
                        price: $(this).data('price'),
                        note: $('#note_' + $(this).attr('id')).val(),
                        printer: $(this).data('printer'),
                        subtotal: parseInt($(this).find('.qty').text()) * parseInt($(this).data('price'))
                    };
                    order['items'][index] = tmp;
                });

                console.log(order);

                if (transaction_id != null)
                    var url = '{{ url("/") }}/print-bill/'+transaction_id;
                else
                    var url = '{{ url("/") }}/new-order';

                $.ajax({
                   url: url,
                   data: {
                      data: order
                   },
                   type: 'post',
                   error: function() {
                      alert('error');
                   },
                   dataType: 'json',
                   success: function(data) {
                        if(data.status) {
                            // sendToKitchenList = [];
                            // console.log(data.transaction_id);
                            transaction_id = data.transaction_id;
                            table_id = data.table;
                            btn.html('Print Bill');
                            btn.prop('disabled', false);

                            $.each(data.error_printer, function(index, value) {
                                toastr["error"](value);
                            });
                            // btn.prop('disabled', true);
                            // btn.html('<i class="fa fa-check-circle"></i> Success');
                            // setTimeout(function() {
                            //     window.location = "{{ url('/') }}/transaction-current";
                            // }, 1000);

                            // const socket = io.connect("<?= env('SOCKET_URL', 'http://localhost:3003') ;?>");

                            // var data_send = {
                            //  "table_id": table_id,
                            //  "transaction_id": transaction_id
                            // };
                            // socket.emit('new transaction added dashboard', JSON.stringify(data_send));
                        }
                   }
                });
            });
            
            $('#send_to_kitchen').on('click', function() {
                // console.log(sendToKitchenList);
                var btn = $(this);
                btn.html('<i class="fa fa-spinner fa-spin"></i>');
                btn.prop('disabled', true);

                // sendToKitchenList.each(function(index, value) {
                for (var i = 0; i < sendToKitchenList.length; i++) {
                    sendToKitchenList[i]['note'] = $('#note_' + sendToKitchenList[i]['id']).val();
                };

                var order = {
                    'user_id': '{{ auth()->user()->user_id }}',
                    'table_id': $('#table_no :selected').val(),
                    'promotion_id': $('#promotion :selected').val(),
                    'type': $('#type :selected').val(),
                    'payment_method': $('#payment_method :selected').val(),
                    'price': $('#price :selected').val(),
                    'payable': $('input[name="payable"]').val(),
                    'total': $('input[name="total_value"]').val(),
                    'grand_total': $('input[name="grand_total_value"]').val(),
                    'discount': $('input[name="discount_value"]').val(),
                    'paid': $('#paid').val(),
                    'note': $('#note').val(),
                    'name': $('#name').val(),
                    'status': 'pending',
                    'items': [],
                    'kitchen_list': sendToKitchenList
                }
                $( ".order" ).each(function(index) {
                    tmp = {
                        id: $(this).attr('id'),
                        quantity: $(this).data('qty'),
                        price: $(this).data('price'),
                        note: $('#note_' + $(this).attr('id')).val(),
                        printer: $(this).data('printer'),
                        subtotal: parseInt($(this).find('.qty').text()) * parseInt($(this).data('price'))
                    };
                    order['items'][index] = tmp;
                });

                console.log(order);

                if (transaction_id != null)
                    var url = '{{ url("/") }}/update-order/'+transaction_id;
                else
                    var url = '{{ url("/") }}/new-order';

                $.ajax({
                   url: url,
                   data: {
                      data: order
                   },
                   type: 'post',
                   error: function() {
                      alert('error');
                   },
                   dataType: 'json',
                   success: function(data) {
                        if(data.status) {
                            sendToKitchenList = [];
                            // console.log(data.transaction_id);
                            transaction_id = data.transaction_id;
                            table_id = data.table;
                            btn.html('To Kitchen');
                            $('#print_bill').prop('disabled', false);
                            // btn.prop('disabled', true);
                            // btn.html('<i class="fa fa-check-circle"></i> Success');
                            // setTimeout(function() {
                            //     window.location = "{{ url('/') }}/transaction-current";
                            // }, 1000);

                            // const socket = io.connect("<?= env('SOCKET_URL', 'http://localhost:3003') ;?>");

                            // var data_send = {
                            //  "table_id": table_id,
                            //  "transaction_id": transaction_id
                            // };

                            $.each(data.error_printer, function(index, value) {
                                toastr["error"](value);
                            });
                            // socket.emit('new transaction added dashboard', JSON.stringify(data_send));
                        }
                   }
                });
                sendToKitchenList = [];
            });

            $('#finish_order').on('click', function() {
                var btn = $(this);
                btn.html('<i class="fa fa-spinner fa-spin"></i>');
                btn.prop('disabled', true);
                var order = {
                    'user_id': '{{ auth()->user()->user_id }}',
                    'table_id': $('#table_no :selected').val(),
                    'promotion_id': $('#promotion :selected').val(),
                    'type': $('#type :selected').val(),
                    'payment_method': $('#payment_method :selected').val(),
                    'price': $('#price :selected').val(),
                    'payable': $('input[name="payable"]').val(),
                    'total': $('input[name="total_value"]').val(),
                    'grand_total': $('input[name="grand_total_value"]').val(),
                    'discount': $('input[name="discount_value"]').val(),
                    'paid': $('#paid').val(),
                    'note': $('#note').val(),
                    'name': $('#name').val(),
                    'status': 'finished',
                    'items': []
                }
                $( ".order" ).each(function(index) {
                    tmp = {
                        id: $(this).attr('id'),
                        quantity: $(this).data('qty'),
                        price: $(this).data('price'),
                        note: $('#note_' + $(this).attr('id')).val(),
                        subtotal: parseInt($(this).find('.qty').text()) * parseInt($(this).data('price'))
                    };
                    order['items'][index] = tmp;
                });

                if (transaction_id != null)
                    var url = '{{ url("/") }}/update-order/'+transaction_id;
                else
                    var url = '{{ url("/") }}/new-order';

                $.ajax({
                   url: url,
                   data: {
                      data: order
                   },
                   type: 'post',
                   error: function() {
                      alert('error');
                   },
                   dataType: 'json',
                   success: function(data) {
                        if(data.status) {
                            btn.html('Finish');
                            $.each(data.error_printer, function(index, value) {
                                toastr["error"](value);
                            });
                            
                            if (data.error_printer.length == 0) {
                                setTimeout(function() {
                                    window.location = "{{ url('/') }}/transaction/create";
                                }, 1000);
                            }
                        }
                   }
                });
            })

            $('.product-list').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var price = $(this).data('price');
                var price_gojek = $(this).data('pricegojek');
                var printer_id = $(this).data('printer');

                // check element 
                if(!$('#list-order tr#' + id).length) {
                    add_product(id, name, price, price_gojek, printer_id);
                } else {
                    var price_cat = $('#price :selected').val();
                    if(price_cat == 'general')
                        var price_sel = $(this).data('price');
                    else
                        var price_sel = $(this).data('pricegojek');
                    add_qty(id, price_sel);
                }
            });

            $('#list-order').on('click', 'button.add', function() {
                var id = $(this).data('id');
                var price_cat = $('#price :selected').val();
                if(price_cat == 'general')
                    var price = $('.order#'+ id).data('price');
                else
                    var price = $('.order#'+ id).data('pricegojek');
                add_qty(id, price);
            });

            $('#list-order').on('click', 'button.reduce', function() {
                var id = $(this).data('id');
                var price_cat = $('#price :selected').val();
                if(price_cat == 'general')
                    var price = $('.order#'+ id).data('price');
                else
                    var price = $('.order#'+ id).data('pricegojek');
                minus_qty(id, price);
            });

            $('#list-order').on('click', 'button.remove', function() {
                var id = $(this).data('id');
                remove_product(id);
            });

            $('#paid').on('keyup', function() {
                var grand_total = parseFloat($('input[name="grand_total_value"]').val());
                var paid = parseFloat($(this).val());
                var payable = paid - grand_total;

                if (payable >= 0) {
                    // console.log(payable);
                    $('#payable').text(add_rupiah_string(rupiah_currency(payable.toFixed())));
                    $('input[name="payable"]').val(payable.toFixed());
                    $('#finish_order').prop('disabled', false);
                } else {
                    $('#payable').text(add_rupiah_string(rupiah_currency(0)));
                    $('input[name="payable"]').val(0);
                    $('#finish_order').prop('disabled', true);
                }
            });

            $('#price').on('change', function() {
                change_price_category($(this).val());
            });

            // $('#promotion').on("select2:selecting", function(e) { 
            //    grand_total();
            //    console.log('promo : '+ $('#promotion :selected').val());
            // });

            $('#promotion').on('change', function() {
              // var data = $(".select2 option:selected").text();
              // $("#test").val(data);
              grand_total();
              // console.log('Promotional: '+ $('#promotion :selected').val());
            })

            $(":file").filestyle({input: false});

            // const socket = io.connect('http://localhost:3003');
            const socket = io.connect('http://139.59.251.117:3003');

            socket.on('connect', function () {
                console.log('Connected');
            });

            // io.on('connection', function () {
     //         $('#finish_order').click(function(e) {
     //             e.preventDefault();
     //             var data = {
                    //  "table_id": 1,
                    //  "type": "disajikan"
                    // };
        //             socket.emit('new transaction', JSON.stringify(data));
     //         });
            // });

        });

        function change_price_category(cat)
        {
            var product = [];

            $( ".order" ).each(function(index, value) {
                // console.log("dd: " + value.dataset.price);
                var id = value.id;
                if(cat == 'general')
                    var price = value.dataset.price;
                else
                    var price = value.dataset.pricegojek;
                var qty = value.dataset.qty;

                $( ".order#" + id ).find('.subtotal').html('<h5>' + rupiah_currency(price*qty) + '</h5>');
            });

            // if(cat == 'general') {
            //     $('.product-list').each(function(index) {
            //         var price_outlet = $(this).data('price');
            //         var id = $(this).data('id')

            //         $.each(product,function(k,v) {
            //             if(v.id == id) {
            //                 var price = price_outlet * v.quantity;
            //                 // console.log(price);
            //                 $( ".order#" + id ).find('.subtotal').text(rupiah_currency(price));
            //             }
            //         });
            //     });
            // } else {
            //     $('.product-list').each(function(index) {
            //         var price_gojek = $(this).data('price_gojek');
            //         var id = $(this).data('id')

            //         $.each(product,function(k,v) {
            //             if(v.id == id) {
            //                 var price = price_gojek * v.quantity;
            //                 // console.log(price);
            //                 $( ".order#" + id ).find('.price').text(rupiah_currency(price));
            //             }
            //         });
            //     });
            // }

            grand_total();
            // $('#paid').trigger('keyup');
        }

        function add_product(id, name, price, price_gojek, printer_id)
        {
            var number = $('.iteration').length + 1;

            var price_cat = $('#price :selected').val();
            if(price_cat == 'general')
                var price_sel = price;
            else
                var price_sel = price_gojek;

            result = `<tr id="` + id + `" class="order" data-printer="` + printer_id + `" data-price="` + price + `" data-pricegojek="` + price_gojek + `" data-qty="1">
                <td><h5>` + name + `</h5></td>
                <td class="qty"><h5>1</h5></td>
                <td class="subtotal"><h5>` + rupiah_currency(price_sel) + `</h5></td>
                <td class="note"><input type="text" class="form-control input-sm note" id="note_`+ id +`"></td>
                <td class="text-right">
                    <button data-id="` + id + `" class="btn btn-warning btn-sm btn-xs reduce"><i class="fa fa-minus"></i></button>
                    <button data-id="` + id + `" class="btn btn-success btn-sm btn-xs add"><i class="fa fa-plus"></i></button>
                    <button data-id="` + id + `" class="btn btn-danger btn-sm btn-xs remove"><i class="fa fa-trash"></i></button>
                </td>

            </tr>`;

            $("#list-order tbody").append(result);
            grand_total();
            modifyKitchenList(id, '+');
        }

        function remove_product(id)
        {
            $('#list-order tr#' + id).remove();
            // $('#list-order tr#note_block_' + id).remove();
            grand_total();
            modifyKitchenList(id, '-');
        }

        function add_qty(id, price)
        {
            console.log('PR: ' + price);
            var self = $('.order#' + id);
            var current_qty = parseInt(self.data('qty'));
            var new_qty = current_qty + 1;
            
            self.find('.qty').html('<h5>' + new_qty + '</h5>');
            self.attr('data-qty', new_qty);
            self.data('qty', new_qty);

            $('.order#' + id + ' .subtotal').html('<h5>' + rupiah_currency(price * new_qty) + '</h5>');

            grand_total();
            modifyKitchenList(id, '+');
        }

        function minus_qty(id, price)
        {
            var self = $('.order#' + id);
            var current_qty = parseInt(self.data('qty'));
            
            if(current_qty - 1 > 0) {
                var new_qty = current_qty - 1;
                self.find('.qty').html('<h5>' + new_qty + '</h5>');
                self.attr('data-qty', new_qty);
                self.data('qty', new_qty);

                $('.order#' + id + ' .subtotal').html('<h5>' + rupiah_currency(price * new_qty) + '</h5>');
            } else {
                $('.order#' + id).remove();
                // $('#note_block_' + id).remove();
            }

            grand_total();
            modifyKitchenList(id, '-');
        }

        function grand_total()
        {
            var sub_total = 0;
            var grand_total = 0;
            var promotion = $('#promotion option:selected');
            var discount = 0;
            var pajak = 0.1;
            $( ".order" ).each(function(index) {
                // sub_total += parseInt($(this).find('.subtotal').text().replace('.',''));
                var price_cat = $('#price :selected').val();
                if (price_cat == "general")
                    sub_total += parseInt($(this).data('price')) * parseInt($(this).data('qty'));
                else
                    sub_total += parseInt($(this).data('pricegojek')) * parseInt($(this).data('qty'));
            });

            $('input[name="total_value"]').val(sub_total);
            $('#total').text(add_rupiah_string(rupiah_currency(sub_total)));

            console.log(promotion.val());
            // console.log(promotion_el.data('type'));
            
            if (promotion.val() != 0) {
                var type = promotion.data('type');
                var value = parseInt(promotion.data('value'));
                if (type == 'percent') {
                    discount = ((sub_total * value) / 100);
                } else {
                    discount = value;
                }
                sub_total = sub_total - discount;
                console.log('cal promo');
            }

            grand_total = sub_total + (sub_total * pajak);

            
            $('input[name="discount_value"]').val(discount);
            $('input[name="grand_total_value"]').val(grand_total.toFixed());
            $('#grand-total').text(add_rupiah_string(rupiah_currency(grand_total.toFixed())));
            $('#paid').trigger('keyup');
        }

        function add_rupiah_string(str)
        {
            return 'Rp. ' + str;
        }

        function rupiah_currency(bilangan)
        {
            var reverse = bilangan.toString().split('').reverse().join(''),
                ribuan  = reverse.match(/\d{1,3}/g);
                ribuan  = ribuan.join('.').split('').reverse().join('');
            return ribuan;
        }

        function modifyKitchenList(id, opr='+') {
            // console.log('#note: ' + id);
            // console.log("Test: " + $("#note_" + id).val());
            if (opr == '+') {
                var i = in_array(id, sendToKitchenList);
                if (i != -1) {
                    sendToKitchenList[i]['qty'] = sendToKitchenList[i]['qty'] + 1;
                } else {
                    var note = $('#note_' + id).val();
                    var printer = $('.order#' + id).data('printer');
                    sendToKitchenList.push({id: id, qty: 1, note: note, printer: printer});
                    // sendToKitchenList[i] = 1;
                }
            } else {
                if (id in sendToKitchenList) {
                    if (sendToKitchenList[i] - 1 == 0)
                        delete sendToKitchenList[i];
                    else
                        sendToKitchenList[i] = sendToKitchenList[i] - 1;
                }
            }
            console.log(sendToKitchenList.length);
            if (sendToKitchenList.length > 0)
                $('#send_to_kitchen').prop('disabled', false);
            else
                $('#send_to_kitchen').prop('disabled', true);
        }

        function in_array(value, array) {
            // if ($.inArray(value, $.map(arr, function(v) { return v[0]; })) > -1) {
            //     return true;
            // } else 
            //     return false;

            for(var i=0, j=array.length; i<j; i++) {
                if(array[i].id == value) {
                    return i;
                }
            }
            return -1;
        }
    </script>
@endsection