@extends('layouts.layout')

@section('title', (isset($transaction) ? 'Edit Order':'New Order'))

@section('styles')
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
            <div class="col-sm-6">
				<div class="card-box">
					<div class="row">
                        <div class="col-md-4">
                        	<div class="row">
	    						<div class="col-md-12">
	    							<label>Table No:</label>
	    						</div>
	    						<div class="col-md-12">
	    							{{ Form::select('table_no', $table, (isset($transaction) ? $transaction->table_id : (isset($id_table) ? $id_table:0) ), array('class' => 'form-control input-sm ', 'id' => 'table_no')) }}
	    						</div>
    						</div>
                        </div>
                        <div class="col-md-4">
                        	<div class="row">
	                            <div class="col-md-12">
	                            	<label>Type</label>
	                            </div>
	                            <div class="col-md-12">
                                    {{ Form::select('price', ['disajikan' => 'Hidang', 'rames' => 'Rames Meja', 'takeaway' => 'Takeaway'], (isset($transaction) ? $transaction->type : 'disajikan'), array('class' => 'form-control input-sm ', 'id' => 'type')) }}
	                            </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                        	<div class="row">
	                            <div class="col-md-12">
	                            	<label>Price Category</label>
	                            </div>
	                            <div class="col-md-12">
                                    {{ Form::select('price', ['general'=>'Outlet', 'gojek'=>'Gojek'], (isset($transaction) ? $transaction->price_category : 'general'), array('class' => 'form-control input-sm ', 'id' => 'price')) }}
	                            </div>
                            </div>
                        </div>
					</div>
                    <br>

					<div class="table-rep-plugin" id="cart">
						<div class="table-responsive" data-pattern="priority-columns">
							<table id="list-order" class="table table-striped table-condensed">
								<thead>
									<tr>
										<th data-priority="1" width="30%">Menu</th>
										<th data-priority="1" width="1%">Qty</th>
                                        <th data-priority="1" width="15%">Subtotal</th>
										<th data-priority="1" width="">Note</th>
										<th data-priority="5" width="27%" class="text-right">Action</th>
									</tr>
								</thead>
								<tbody>

                                    @if (isset($transaction))
                                            @foreach ($transaction->detail as $t)
                                                <tr id="{{ $t->product_id }}" class="order" data-printer="{{ $t->product->printer_id }}" data-price="{{ $t->product->price }}" data-pricegojek="{{ $t->product->price_gojek }}" data-qty="{{ $t->quantity }}">
                                                    <td class="strong"><h6>{{ $t->product->name }}</h6></td>
                                                    <td class="qty"><h6>{{ $t->quantity }}</h6></td>
                                                    <td class="subtotal"><h6>{{ \MyNumber::toReadableAngka($t->subtotal, false, true) }}</h6></td>
                                                    <td class="note"><input class="form-control note input-sm" id="note_{{ $t->product_id }}" value="{{ $t->note }}"></td>
                                                    <td class="text-right">
                                                        <div class="action">
                                                            <button data-id="{{ $t->product_id }}" data-price="{{ $t->price }}" class="btn btn-warning btn-sm reduce"><i class="fa fa-minus"></i></button>
                                                            <button data-id="{{ $t->product_id }}" data-price="{{ $t->price }}" class="btn btn-success btn-sm add"><i class="fa fa-plus"></i></button>
                                                            <button data-id="{{ $t->product_id }}" data-price="{{ $t->price }}" class="btn btn-danger btn-sm remove"><i class="fa fa-trash"></i></button>
                                                        </div>
                                                        <button class="btn btn-info btn-sm btn-block sent-btn hide" disabled>Terkirim</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                    @endif
								</tbody>
							</table>
						</div>
					</div>

                    <br>
                    <div id="transaction_handler">
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4"><h6>METODE</h6></div>
                                    <div class="col-md-8 text-right">
                                        {{ Form::select('payment_method', $payment_method, (isset($transaction) ? $transaction->payment_method_id : 0), array('class' => 'form-control input-sm', 'id' => 'payment_method')) }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4"><h6>PROMOSI</h6></div>
                                    <div class="col-md-8">
                                        <select name="promotion" id="promotion" class="select2 input-sm">
                                            <option value="0" title="-" <?=(isset($transaction) && ($transaction->promotion_id==0 || $transaction->promotion_id==null)) ? "selected":"" ?>>-</option>
                                            @if($promotion)
                                                @foreach($promotion as $promo)
                                                    <option value="{{ $promo->promotion_id }}" title="{{ $promo->description }}" data-value="{{ $promo->value }}" data-type="{{ $promo->type }}" <?=(isset($transaction) && ($transaction->promotion_id==$promo->promotion_id)) ? "selected":"" ?>>{{ $promo->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4"><h6>NOTE</h6></div>
                                    <div class="col-md-8 text-right">
                                        <input type="text" name="note" id="note" class="form-control input-sm" placeholder="" value="{{ isset($transaction) ? $transaction->note:'' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4"><h6>NAME</h6></div>
                                    <div class="col-md-8 text-right">
                                        <input type="text" name="name" id="name" class="form-control input-sm" placeholder="" value="{{ isset($transaction) ? $transaction->name:'' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6>SUBTOTAL</h6>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <h6 id="total">{{ isset($transaction) ? \MyNumber::toReadableAngka($transaction->total, false, true) : 0 }}</h6>
                                        <input type="hidden" name="total_value" value="{{ isset($transaction) ? $transaction->total:'' }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4"><h6>PAJAK</h6></div>
                                    <div class="col-md-8 text-right"><h6>10%</h6></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4"><h6>TOTAL</h6></div>
                                    <div class="col-md-8 text-right">
                                        <h6 id="grand-total" class="text-primary"> {{ isset($transaction) ? \MyNumber::toReadableAngka($transaction->grand_total, false, true) : 0 }}</h6>
                                        <input type="hidden" name="grand_total_value" value="{{ isset($transaction) ? $transaction->grand_total:'' }}">
                                        <input type="hidden" name="discount_value" value="{{ isset($transaction) ? $transaction->discount:'' }}">
                                    </div>
                                </div>

                                @if(auth()->user()->role_id < 4)
                                    <div class="row">
                                        <div class="col-md-4"><h6>BAYAR</h6></div>
                                        <div class="col-md-8 text-right">
                                            <input type="text" class="form-control input-sm" id="paid" style="text-align: right;" value="{{ isset($transaction) ? MyNumber::toReadableAngka($transaction->paid, false, true) : '' }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"><h6>KEMBALIAN</h6></div>
                                        <div class="col-md-8 text-right">
                                            <h6 id="payable" class="text-danger">{{ isset($transaction) ? \MyNumber::toReadableAngka($transaction->payable, false, true) : 0 }}</h6>
                                            <input type="hidden" name="payable" value="{{ isset($transaction) ? $transaction->payable : '' }}">
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" class="form-control input-sm" id="paid" style="text-align: right;" value="{{ isset($transaction) ? $transaction->paid : '' }}">
                                    <input type="hidden" name="payable" value="{{ isset($transaction) ? $transaction->payable : '' }}">
                                @endif

                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            @if(auth()->user()->role_id < 4)
                                <div class="col-md-2">
                                    <button class="btn btn-inverse btn-block" id="save_btn">
                                        Save
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-block <?= (!strpos(url()->current(), 'edit') ? 'btn-secondary' : 'btn-info') ;?>" id="send_to_kitchen" <?= (!strpos(url()->current(), 'edit') ? 'disabled="disabled"':'') ;?>>
                                        Send
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-block <?= (!strpos(url()->current(), 'edit') ? 'btn-secondary' : 'btn-primary') ;?>" id="print_bill" <?= (!strpos(url()->current(), 'edit') ? 'disabled="disabled"':'') ;?>>
                                        Bill
                                    </button>
                                </div>
								@if (!strpos(url()->current(), 'edit'))
									<div class="col-md-2">
										<button class="btn btn-block btn-secondary" id="finish_order" disabled="disabled">
											Finish
										</button>
									</div>
								@else
									@if (count($transaction->detail))
										<div class="col-md-2">
											<button class="btn btn-block {{ round($transaction->paid) < 1 ? ' btn-secondary' : 'btn-success' }}" id="finish_order"{{ round($transaction->paid) < 1 ? ' disabled' : '' }}>
												Finish
											</button>
										</div>
									@else
										<div class="col-md-2">
											<button class="btn btn-block btn-success" id="finish_order">
												Finish
											</button>
										</div>
									@endif
								@endif
                                <div class="col-md-2">
                                    <button class="btn btn-block <?= (!strpos(url()->current(), 'edit') ? 'btn-secondary' : 'btn-warning') ;?>" id="lost_bill" <?= (!strpos(url()->current(), 'edit') ? 'disabled="disabled"':'') ;?>>
                                        Lost
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-block <?= (!strpos(url()->current(), 'edit') ? 'btn-secondary' : 'btn-danger') ;?>" data-toggle="modal" data-target="#custom-modal" id="void" <?= (!strpos(url()->current(), 'edit') ? 'disabled="disabled"':'') ;?>>
                                        Void
                                    </button>
                                </div>
                            @else
                                <div class="col-md-6">
                                    <button class="btn btn-inverse btn-block" id="save_btn">
                                        Save
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-info btn-block" id="send_to_kitchen" <?= (!strpos(url()->current(), 'edit') ? 'disabled="disabled"':'') ;?>>
                                        Send
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
				</div>
			</div>

			<div class="col-sm-5">
                <div class="card-box">
                    <div class="row text-right">
                        <h3 id="input-calc">-</h3>
                    </div>
                </div>
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
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
					</div>
				</div>
			</div>


            <div class="col-md-1">
                <div class="card-box">
                    <div class="row">
                        <button class="btn btn-inverse btn-block btn-lg num" data-value="1">1</button>
                        <button class="btn btn-inverse btn-block btn-lg num" data-value="2">2</button>
                        <button class="btn btn-inverse btn-block btn-lg num" data-value="3">3</button>
                        <button class="btn btn-inverse btn-block btn-lg num" data-value="4">4</button>
                        <button class="btn btn-inverse btn-block btn-lg num" data-value="5">5</button>
                        <button class="btn btn-inverse btn-block btn-lg num" data-value="6">6</button>
                        <button class="btn btn-inverse btn-block btn-lg num" data-value="7">7</button>
                        <button class="btn btn-inverse btn-block btn-lg num" data-value="8">8</button>
                        <button class="btn btn-inverse btn-block btn-lg num" data-value="9">9</button>
                        <button class="btn btn-inverse btn-block btn-lg num" data-value="0">0</button>
                    </div>
                </div>
            </div>
		</div>

        <div id="custom-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Notes</h4>
                    </div>
                    <form action="" autocomplete="off">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="modal-password" class="control-label">Password</label>
                                        <input type="password" class="form-control" id="modal-password" placeholder="Masukkan Password">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group no-margin">
                                        <label for="modal-remark" class="control-label">Remark</label>
                                        <textarea class="form-control autogrow" id="modal-remark" placeholder="Tulis catatan" style="overflow: hidden; word-wrap: break-word; resize: horizontal; height: 104px;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-info waves-effect waves-light" id="modal-save">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<?php
/*
 * chmod -R 777 bootstrap/cache/.
 * chmod -R 777 storage/.
 */
?>

@section('scripts')
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/jquery.mask.js') }}"></script>
    <script type="text/javascript">
        // START EDIT
        @if (isset($transaction))
            @if ($transaction->paid > 0)
                $('#finish_order').prop('disabled', false);
            @endif
        @endif
        // END EDIT
        var sendToKitchenList = [];
        var passconfirm = '150292';
        var transaction_id = {{ (isset($transaction)) ? $transaction->transaction_id:"null" }};
        var table_id = null;
        var num_calc = "";

        $(document).ready(function() {
            $('#paid').mask("000.000.000.000.000", {reverse: true});

            const socket = io.connect("{{ env('SOCKET_URL', 'http://localhost:3003') }}");
            $('#loading').hide();
            $('#content').show();

            // $("input[type=number]").focus(function() {
            $("#paid").focus(function() {
                $('#paid').popover('show');
                $(this).select();
            });

            // $("input[type=number]").focusout(function() {
            $("#paid").focusout(function() {
                $('#paid').popover('hide');
            });

            $("#promotion").select2({
                templateResult: function(option) {
                    var $option = $(
                      '<div><strong>' + option.text + '</strong></div><div>' + option.title + '</div>'
                    );
                    return $option;
                }
            });

            $('.num').on('click', function() {
                var val = $(this).data('value');
                update_calc(val);
            });

            $('#modal-save').on('click', function(e) {
                e.preventDefault();
                var pass = $('#modal-password').val();
                if (pass == passconfirm) {
                    $('#modal-password').val('');
                    $('#custom-modal').modal('toggle');
                    $('.action').removeClass('hide');
                    $('.sent-btn').addClass('hide');
                } else {
                    alert('Password Salah!');
                }
            });

            $('#lost_bill').on('click', function(e) {
                if (confirm('Lost bill this transaction?')) {
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
                        'status': 'lost',
                        'items': []
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

                    if (transaction_id != null) {
                        var url = '{{ url("/") }}/update-order/'+transaction_id;
                    } else {
                        var url = '{{ url("/") }}/new-order';
                    }

                    $.ajax({
                        url: url,
                        data: {
                            data: order
                        },
                        type: 'post',
                        error: function() {
                            toastr["error"]("Error Saving!");
                            btn.html('Lost');
                            btn.prop('disabled', false);
                        },
                        dataType: 'json',
                        success: function(data) {
                            if(data.status) {
                                transaction_id = data.transaction_id;
                                toastr["success"]("Lost Bill Saved!");
                                setTimeout(function() {
                                    window.location = '{{ url("/") }}/transaction-history/'+transaction_id
                                }, 1000);
                            } else {
                                toastr["error"]("Error Saving!");
                                btn.html('Lost');
                                btn.prop('disabled', false);
                            }
                        }
                    });
                }
            });

            $('#save_btn').on('click', function() {
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
                    //'paid': parseFloat($('#paid').val()),
                    'paid': $('#paid').val().replace('.',''),
                    'note': $('#note').val(),
                    'name': $('#name').val(),
                    'status': 'pending',
                    'items': []
                }
				var tmp;
                $( ".order" ).each(function(index) {
                    var pricesubtotal = parseInt($(this).find('.qty').text()) * parseInt($(this).data('price'));
                    if($('#price :selected').val() == 'gojek') {
                        pricesubtotal = parseInt($(this).find('.qty').text()) * parseInt($(this).data('pricegojek'));
                    }
                    tmp = {
                        id: $(this).attr('id'),
                        quantity: $(this).data('qty'),
                        price: $(this).data('price'),
                        pricegojek: $(this).data('pricegojek'),
                        note: $('#note_' + $(this).attr('id')).val(),
                        printer: $(this).data('printer'),
                        subtotal: pricesubtotal
                    };
                    order['items'][index] = tmp;
                });

                if (transaction_id != null) {
                    var url = '{{ url("/") }}/update-order/'+transaction_id;
                } else {
                    var url = '{{ url("/") }}/new-order';
                }

                $.ajax({
                    url: url,
                    data: {
                        data: order
                    },
                    type: 'post',
                    error: function() {
                        toastr["error"]("Error Saving!");
                        btn.html('Save');
                        btn.prop('disabled', false);
                    },
                    dataType: 'json',
                    success: function(data) {
                        if(data.status) {
                            transaction_id = data.transaction_id;
                            table_id = data.table_id;

                            toastr.clear();
                            toastr["success"]("Data Saved!");
                            var data_send = {
                                "table_id": table_id,
                                "transaction_id": transaction_id,
                                "status": "pending"
                            };
                            socket.emit('new transaction added dashboard', JSON.stringify(data_send));

                            @if (!strpos(url()->current(), 'edit'))
                                setTimeout(function() {
                                    window.location = "{{ url('/') }}/transaction/" + transaction_id + "/edit";
                                });
                            @else
                                var paid = parseFloat($('#paid').val());
                                btn.html('Save');
                                btn.prop('disabled', false);
                                $('#send_to_kitchen').prop('disabled', false).removeClass('btn-secondary').addClass('btn-info');
                                $('#print_bill').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
								if (paid > 0 || tmp === undefined) {
									$('#finish_order').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
								}
                                $('#lost_bill').prop('disabled', false).removeClass('btn-secondary').addClass('btn-warning');
                                $('#void').prop('disabled', false).removeClass('btn-secondary').addClass('btn-danger');
                            @endif
                        } else {
                            toastr["error"]("Error Saving!");
                            btn.html('Save');
                            btn.prop('disabled', false);
                        }
                    }
                });
            });

            // print bill done
            $('#print_bill').on('click', function() {
                if (transaction_id) {
                    var btn = $(this);
                    var url = "{{ env('APP_URL') }}" + '/transaction/bill';
                    var admin_name = "{!! Auth::user()->name !!}";

                    btn.html('<i class="fa fa-spinner fa-spin"></i>');
                    btn.prop('disabled', true);

                    $.ajax({
                        url: url,
                        data: {
                            transaction_id: transaction_id,
                            admin_name: admin_name
                        },
                        type: 'POST',
                        success: function(data) {
                            if (data.status) {
                                btn.html('Bill');
                                btn.prop('disabled', false);
                                toastr["success"]("Bill Printed!");

                                $.each(data.error_printer, function(index, value) {
                                    toastr["error"](value);
                                });

                                $('#paid').focus();

                                var data_send = {
                                    "table_id"      : data.table_id,
                                    "transaction_id": data.transaction_id,
                                    "status"        : "printbill"
                                };

                                socket.emit('new transaction added dashboard', JSON.stringify(data_send));
                            } else {
                                toastr["error"]("Error Saving!");
                                btn.html('Bill');
                                btn.prop('disabled', false);
                            }
                        },
                        error: function() {
                            toastr["error"]("Error Saving!");
                            btn.html('Bill');
                            btn.prop('disabled', false);
                        }
                    });
                }
            });

            $('#send_to_kitchen').on('click', function() {
                if (transaction_id) {
                    var btn = $(this);
                    var url = "{{ env('APP_URL') }}" + '/transaction/send';
                    var admin_name = "{!! Auth::user()->name !!}";

                    btn.html('<i class="fa fa-spinner fa-spin"></i>');
                    btn.prop('disabled', true);

                    $.ajax({
                        url: url,
                        data: {
                            transaction_id: transaction_id,
                            admin_name: admin_name
                        },
                        type: 'POST',
                        success: function(data) {
                            if (data.status) {
                                btn.html('Send');
                                btn.prop('disabled', false);
                                toastr["success"]("Order Sent!");

                                $.each(data.error_printer, function(index, value) {
                                    toastr["error"](value);
                                });

                                var data_send = {
                                    "table_id"      : data.table_id,
                                    "transaction_id": data.transaction_id,
                                    "status"        : "pending"
                                };

                                socket.emit('new transaction added dashboard', JSON.stringify(data_send));
                            } else {
                                toastr["error"]("Error Savinga!");
                                btn.html('Send');
                                btn.prop('disabled', false);
                            }
                        },
                        error: function(err) {
                            toastr["error"]("Error Saving!");
                            btn.html('Send');
                            btn.prop('disabled', false);
                        }
                    });
                }
            });

            // finish order done
            $('#finish_order').on('click', function() {
                if (transaction_id) {
                    var btn = $(this);
                    var url = "{{ env('APP_URL') }}" + '/transaction/finished';
                    var admin_name = "{!! Auth::user()->name !!}";

                    btn.html('<i class="fa fa-spinner fa-spin"></i>');
                    btn.prop('disabled', true);

                    $.ajax({
                        url: url,
                        data: {
                            transaction_id: transaction_id,
                            admin_name: admin_name
                        },
                        type: 'POST',
                        success: function(data) {
                            if(data.status) {
                                $.each(data.error_printer, function(index, value) {
                                    toastr["error"](value);
                                });

                                var data_send = {
                                    "table_id": data.table_id,
                                    "transaction_id": data.transaction_id
                                };

                                socket.emit('finish transaction dashboard', JSON.stringify(data_send));

                                window.location = "{!! url('/') . '/transaction-history/' !!}" + transaction_id;
                            }
                        },
                        error: function() {
                            toastr["error"]("Error Saving!");
                        },
                    });
                }
            });

            $('.product-list').on('click', function() {
                $('#finish_order').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                $('#send_to_kitchen').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary');
                $('#print_bill').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                $('#lost_bill').prop('disabled', true).removeClass('btn-warning').addClass('btn-secondary');
                $('#void').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');

            	var id = $(this).data('id');
            	var name = $(this).data('name');
                var price = $(this).data('price');
                var price_gojek = $(this).data('pricegojek');
            	var printer_id = $(this).data('printer');

            	if(!$('#list-order tr#' + id).length) {
            		add_product(id, name, price, price_gojek, printer_id);
            	} else {
                    var price_cat = $('#price :selected').val();
                    if(price_cat == 'general') {
                        var price_sel = $(this).data('price');
                    } else {
                        var price_sel = $(this).data('pricegojek');
                    }
            		add_qty(id, price_sel);
            	}
            });

            $('#list-order').on('keyup', 'input.note', function() {
                $('#finish_order').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                $('#send_to_kitchen').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary');
                $('#print_bill').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                $('#lost_bill').prop('disabled', true).removeClass('btn-warning').addClass('btn-secondary');
                $('#void').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
            });

            $('#list-order').on('click', 'button.add', function() {
                $('#finish_order').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                $('#send_to_kitchen').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary');
                $('#print_bill').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                $('#lost_bill').prop('disabled', true).removeClass('btn-warning').addClass('btn-secondary');
                $('#void').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
                var id = $(this).data('id');
                var price_cat = $('#price :selected').val();
                if(price_cat == 'general') {
                    var price = $('.order#'+ id).data('price');
                } else {
                    var price = $('.order#'+ id).data('pricegojek');
                }
            	add_qty(id, price);
            });

            $('#list-order').on('click', 'button.reduce', function() {
                $('#finish_order').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                $('#send_to_kitchen').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary');
                $('#print_bill').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                $('#lost_bill').prop('disabled', true).removeClass('btn-warning').addClass('btn-secondary');
                $('#void').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
            	var id = $(this).data('id');
                var price_cat = $('#price :selected').val();
                if(price_cat == 'general') {
                    var price = $('.order#'+ id).data('price');
                } else {
                    var price = $('.order#'+ id).data('pricegojek');
                }
            	minus_qty(id, price);
            });

            $('#list-order').on('click', 'button.remove', function() {
                $('#finish_order').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                $('#send_to_kitchen').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary');
                $('#print_bill').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                $('#lost_bill').prop('disabled', true).removeClass('btn-warning').addClass('btn-secondary');
                $('#void').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
            	var id = $(this).data('id');
            	remove_product(id);
            });

            $('input#note').on('keyup', function() {
                $('#finish_order').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                $('#send_to_kitchen').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary');
                $('#print_bill').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                $('#lost_bill').prop('disabled', true).removeClass('btn-warning').addClass('btn-secondary');
                $('#void').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
            });

            $('input#name').on('keyup', function() {
                $('#finish_order').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                $('#send_to_kitchen').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary');
                $('#print_bill').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                $('#lost_bill').prop('disabled', true).removeClass('btn-warning').addClass('btn-secondary');
                $('#void').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
            });

            $('#paid').on('keyup', function() {
            	var grand_total = parseFloat($('input[name="grand_total_value"]').val());
            	// var paid = parseFloat($(this).val());
                var paid = $(this).val().replace('.','');
            	var payable = paid - grand_total;
            	if (payable >= 0) {
            		$('#payable').text(rupiah_currency(payable));
            		$('input[name="payable"]').val(payable);
                    $('#finish_order').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                    $('#send_to_kitchen').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary');
                    $('#print_bill').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                    $('#lost_bill').prop('disabled', true).removeClass('btn-warning').addClass('btn-secondary');
                    $('#void').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
            	} else {
                    $('#payable').text(rupiah_currency(0));
                    $('input[name="payable"]').val(0);
                    $('#finish_order').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                    $('#send_to_kitchen').prop('disabled', true).removeClass('btn-info').addClass('btn-secondary');
                    $('#print_bill').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                    $('#lost_bill').prop('disabled', true).removeClass('btn-warning').addClass('btn-secondary');
                    $('#void').prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
                }
            });

            $('#price').on('change', function() {
                change_price_category($(this).val());
            });

            $('#promotion').on('change', function() {
                grand_total();
            });

            $('#paid').popover({
                html: true,
                title: 'Paid',
                placement: 'right',
                trigger: 'manual',
                animation: true,
                content: `<a href='#' class='paid_template' data-value='50000'>50.000</a>
                        <br>
                        <a href='#' class='paid_template' data-value='100000'>100.000</a>
                        <br>
                        <a href='#' class='paid_template' data-value='150000'>150.000</a>
                        <br>
                        <a href='#' class='paid_template' data-value='200000'>200.000</a>
                        <br>
                        <a href='#' class='paid_template' data-value='250000'>250.000</a>
                        <br>
                        <a href='#' class='paid_template' data-value='300000'>300.000</a>
                        <br>
                        <a href='#' class='paid_template' data-value='350000'>350.000</a>
                        <br>
                        <a href='#' class='paid_template' data-value='400000'>400.000</a>
                        <br>
                        <a href='#' class='paid_template' data-value='450000'>450.000</a>
                        <br>
                        <a href='#' class='paid_template' data-value='500000'>500.000</a>`
            }).parent().delegate('a.paid_template', 'click', function(e) {
                e.preventDefault();
                var value = $(this).data('value');
                $('#paid').val(rupiah_currency(value));
                // $('#paid').val(value);
                $('#paid').focusout();
                $('#paid').popover('hide');
                grand_total();
            });

            $(":file").filestyle({input: false});
            socket.on('connect', function () {
                console.log('Connected');
            });

        });

        function change_price_category(cat)
        {
            var product = [];

            $( ".order" ).each(function(index, value) {
                var id = value.id;
                if(cat == 'general') {
                    var price = value.dataset.price;
                } else {
                    var price = value.dataset.pricegojek;
                }
                var qty = value.dataset.qty;
                $( ".order#" + id ).find('.subtotal').html('<h6>' + rupiah_currency(price*qty) + '</h6>');
            });

            grand_total();
        }

        function add_product(id, name, price, price_gojek, printer_id)
        {
        	var number = $('.iteration').length + 1;
            var qty = 1;
            if (num_calc!="") {
                qty = parseInt(num_calc);
                update_calc(null);
            }

            var price_cat = $('#price :selected').val();
            if(price_cat == 'general') {
                var price_sel = price * qty;
            } else {
                var price_sel = price_gojek * qty;
            }

        	result = `<tr id="` + id + `" class="order" data-printer="` + printer_id + `" data-price="` + price + `" data-pricegojek="` + price_gojek + `" data-qty="`+ qty +`" data-name="`+ name +`" data-last-qty="0">
    			<td><h6>` + name + `</h6></td>
    			<td class="qty"><h6>`+ qty +`</h6></td>
                <td class="subtotal"><h6>` + rupiah_currency(price_sel) + `</h6></td>
    			<td class="note"><input type="text" class="form-control input-sm note" id="note_`+ id +`"></td>
    			<td class="text-right">
                    <div class="action">
            			<button data-id="` + id + `" class="btn btn-warning btn-sm reduce"><i class="fa fa-minus"></i></button>
            			<button data-id="` + id + `" class="btn btn-success btn-sm add"><i class="fa fa-plus"></i></button>
            			<button data-id="` + id + `" class="btn btn-danger btn-sm remove"><i class="fa fa-trash"></i></button>
                    </div>
                    <button class="btn btn-info btn-sm btn-block sent-btn hide" disabled>Terkirim</button>
    			</td>

            </tr>`;

			$("#list-order tbody").append(result);
            grand_total();
            modifyKitchenList(id, '+', qty);
        }

        function remove_product(id)
        {
            var self = $('#list-order tr#' + id);
            var qty = parseInt(self.data('qty'));
            self.remove();
            grand_total();
            modifyKitchenList(id, '-', qty);
        }

        function add_qty(id, price)
        {
            var self = $('.order#' + id);
            var current_qty = parseInt(self.data('qty'));

            var add_qty = 1;
            if (num_calc!="") {
                add_qty = parseInt(num_calc);
                update_calc(null);
            }

            var new_qty = current_qty + add_qty;

            self.find('.qty').html('<h6>' + new_qty + '</h6>');
            self.attr('data-qty', new_qty);
            self.data('qty', new_qty);

            $('.order#' + id + ' .subtotal').html('<h6>' + rupiah_currency(price * new_qty) + '</h6>');
            $('.order#' + id + ' .action').removeClass('hide');
            $('.order#' + id + ' .sent-btn').addClass('hide');
            grand_total();
            modifyKitchenList(id, '+', add_qty);
        }

        function minus_qty(id, price)
        {
            var self = $('.order#' + id);
            var current_qty = parseInt(self.data('qty'));
            var last_qty = parseInt(self.data('last-qty'));

            if(current_qty - 1 > 0) {
                var new_qty = current_qty - 1;
                self.find('.qty').html('<h6>' + new_qty + '</h6>');
                self.attr('data-qty', new_qty);
                self.data('qty', new_qty);

                $('.order#' + id + ' .subtotal').html('<h6>' + rupiah_currency(price * new_qty) + '</h6>');
                if (new_qty == last_qty) {
                    $('.order#' + id + ' .action').addClass('hide');
                    $('.order#' + id + ' .sent-btn').removeClass('hide');
                }
            } else {
                $('.order#' + id).remove();
            }
            grand_total();
            modifyKitchenList(id, '-', 1);
        }

        function grand_total()
        {
            var sub_total = 0;
        	var grand_total = 0;
            var promotion = $('#promotion option:selected');
            var discount = 0;
            var pajak = 0.1;
            $( ".order" ).each(function(index) {
                var price_cat = $('#price :selected').val();
                if (price_cat == "general") {
                    sub_total += parseInt($(this).data('price')) * parseInt($(this).data('qty'));
                } else {
                    sub_total += parseInt($(this).data('pricegojek')) * parseInt($(this).data('qty'));
                }
            });

            $('input[name="total_value"]').val(sub_total);
            $('#total').text(rupiah_currency(sub_total));

            if (promotion.val() != 0) {
                var type = promotion.data('type');
                var value = parseInt(promotion.data('value'));
                if (type == 'percent') {
                    discount = ((sub_total * value) / 100);
                } else {
                    discount = value;
                }
                sub_total = sub_total - discount;
            }

            grand_total = sub_total + (sub_total * pajak);

            $('input[name="discount_value"]').val(discount);
        	$('input[name="grand_total_value"]').val(grand_total.toFixed());
            $('#grand-total').text(rupiah_currency(grand_total.toFixed()));
            $('#paid').trigger('keyup');
        }

        function add_rupiah_string(str)
        {
        	return 'Rp. ' + str;
        }

        function rupiah_currency(bilangan)
        {
			var	reverse = bilangan.toString().split('').reverse().join(''),
				ribuan 	= reverse.match(/\d{1,3}/g);
				ribuan	= ribuan.join('.').split('').reverse().join('');
			return ribuan;
		}

        function modifyKitchenList(id, opr='+', qty=1)
        {
            if (opr == '+') {
                var i = in_array(id, sendToKitchenList);
                if (i != -1) {
                    sendToKitchenList[i]['qty'] = sendToKitchenList[i]['qty'] + qty;
                } else {
                    var note = $('#note_' + id).val();
                    var printer = $('.order#' + id).data('printer');
                    sendToKitchenList.push({id: id, qty: qty, note: note, printer: printer});
                }
            } else {
                var i = in_array(id, sendToKitchenList);
                if (i != -1) {
                    if (sendToKitchenList[i]['qty'] - qty == 0) {
                        sendToKitchenList.splice(i,1);
                    } else {
                        sendToKitchenList[i]['qty'] = sendToKitchenList[i]['qty'] - qty;
                    }
                }
            }

            // if (sendToKitchenList.length > 0) {
            //     $('#send_to_kitchen').prop('disabled', false);
            // } else {
            //     $('#send_to_kitchen').prop('disabled', true);
            // }
        }

        function in_array(value, array)
        {
            for(var i=0, j=array.length; i<j; i++) {
                if(array[i].id == value) {
                    return i;
                }
            }
            return -1;
        }

        function update_calc(value=null)
        {
            if (value != null) {
                num_calc += ""+ value;
                $('#input-calc').html(num_calc);
            } else {
                num_calc = "";
                $('#input-calc').html('-');
            }
        }
    </script>
@endsection
