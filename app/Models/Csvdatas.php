<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Csvdatas extends Model
{
	protected $fillable = [
		'report_name',
		'report_date',
		'gross_sales',
		'discount',
		'free_of_charge',
		'net_sales',
		'tax_ten_percent_total',
		'no_of_receipt',
		'average_receipt',
		'total_sales',
		// 'sales_by_cash',
		// 'sales_by_cash_total',
		// 'sales_by_bca_debit',
		// 'sales_by_bca_debit_total',
		// 'sales_by_bca_credit',
		// 'sales_by_bca_credit_total',
		// 'sales_by_niaga_debit',
		// 'sales_by_niaga_debit_total',
		// 'sales_by_transfer',
		// 'sales_by_transfer_total',
		// 'total_sales_by_media'
	];
}