<?php
namespace App\Library;

use App\Point as PointModel;
use App\PointRedeem as PointRedeemModel;
use App\Usr_ as UsrModel;
use App\UsrPoint as UsrPointModel;
use App\ShortirPostLog as ShortirPostLogModel;
use App\Editor as EditorModel;
use App\Operator as OperatorModel;
use App\OperatorPrefix as OperatorPrefixModel;
use App\BalanceOption as BalanceOptionModel;
use MyConfig;
// use MyNumber;
use DB;

class Point {

	public static function getAllUserPoint($id, $order='desc', $limit) {
		$usr_point = UsrPointModel::where('id_usr', $id)
						->orderBy('id_usr_point', $order)
						->paginate($limit);

		return $usr_point;
	}

	public static function getPoint($id_point) {
		$point = PointModel::where('id_point', $id_point)->first();
		return $point->value;
	}

	public static function getUsrPoint($id_usr_point) {
		$usr_point = UsrPointModel::where('id_usr_point', $id_usr_point)->first();
		return $usr_point->value;
	}

	public static function getTotalPoint($id_usr, $status=[0,1]) {
		$cek = UsrModel::where('id', $id_usr);
    	if($cek->count() > 0) {
			// $cek = UsrPointModel::where('id_usr', $id_usr)->whereIn('status', $status);
			$debit = UsrPointModel::where('id_usr', $id_usr)->where('type', 1)->whereIn('status', $status)->sum('value');
			$credit = UsrPointModel::where('id_usr', $id_usr)->where('type', 2)->whereIn('status', $status)->sum('value');
			$sum = $debit - $credit;
			if($sum > 0) {
				return $sum;
			} else {
				return 0;
			}
		} else
			return false;
	}

	public static function getRedeemStatus($id_usr) {
		$status = PointRedeemModel::where([
			'id_usr' => $id_usr,
			'status' => 0,
		]);
		if($status->count() > 0)
			return false;
		else
			return true;
	}

	public static function addUsrPoint($id_point, $id_usr, $activity='', $status=1, $point='') {
		$cek = PointModel::where('id_point', $id_point);
		if($cek->count() > 0) {
			$get = $cek->first();

			if($point!='')
				$value = $point;
			else
				$value = $get->value;

			$getId = UsrPointModel::insertGetId([
	            'id_point' => $id_point,
	            'id_usr' => $id_usr,
	            'activity' => $activity,
	            'value' => $value,
	            'log_point' => date('Y-m-d H:i:s'),
	            'status' => $status
	        ]);
			return $getId;
		} else {
			return false;
		}
	}

	public static function deleteUsrPoint($id_usr_point) {
		$cek = UsrPointModel::where('id_usr_point', $id_usr_point);
		if($cek->count() > 0) {
			$cek->delete();
			return true;
		} else {
			return false;
		}
	}

	public static function redeemPoint($id_usr, $dt=[], $type=1) {
		$cek = UsrModel::where('id', $id_usr);
    	if($cek->count() > 0) {
    		if($type==1) {
    			// echo $dt["bank_name"]; exit;
    			$bank_name = $dt["bank_name"];
    			$bank_account_no = $dt["bank_account_no"];
    			$bank_account_name = $dt["bank_account_name"];

	    		$get = $cek->first();
	    		$totalPoint = self::getTotalPoint($id_usr);

				$limit_redeem = MyConfig::getConfig('limit.redeem');

	    		// cek previous redeem status
	    		$cekPR = PointRedeemModel::where([
	    			'id_usr' => $id_usr,
	    			'type' => 1,
	    			'status' => 0
				]);

				if($cekPR->count() == 0) {
					if($totalPoint >= $limit_redeem['value']) {
						$id_usr_point = UsrPointModel::insertGetId([
							'id_usr' => $id_usr,
							'id_point' => 5,
							'activity' => 'Redeem Point',
							'value' => $limit_redeem['value'],
							'log_point' => date('Y-m-d H:i:s'),
							'type' => 2,
							'status' => 0
						]);

			    		PointRedeemModel::insert([
							'id_usr' => $id_usr,
							'id_usr_point' => $id_usr_point,
							'bank_name' => $bank_name,
							'bank_account_no' => $bank_account_no,
							'bank_account_name' => $bank_account_name,
							'value' => $limit_redeem['value'],
							'log_point_redeem' => date('Y-m-d H:i:s'),
							'type' => 1,
						]);

						return true;
					} else {
						return false;
					}
				} else {
					return 'onprocess';
				}
			} else {
				$balance_token = $dt['balance_token'];
    			$phone_number = $dt['phone_number'];

    			// Check Balance Option
    			$balanceOption = BalanceOptionModel::where(['token'=>$balance_token]);
    			if($balanceOption->count() > 0) {
    				$getBalanceOption = $balanceOption->first();
    				$balance_value = $getBalanceOption->value;
    				$point_needed = $getBalanceOption->point_needed;

    				$get = $cek->first();	
		    		$totalPoint = self::getTotalPoint($id_usr);

		    		// cek previous redeem status
		    		$cekPR = PointRedeemModel::where([
		    			'id_usr' => $id_usr,
		    			'type' => 2,
		    			'status' => 0
					]);

					if($cekPR->count() == 0) {
						if($totalPoint > $point_needed) {
							$id_usr_point = UsrPointModel::insertGetId([
								'id_usr' => $id_usr,
								'id_point' => 5,
								'activity' => 'Redeem Point',
								'value' => $point_needed,
								'log_point' => date('Y-m-d H:i:s'),
								'type' => 2,
								'status' => 0
							]);

				    		PointRedeemModel::insert([
								'id_usr' => $id_usr,
								'id_usr_point' => $id_usr_point,
								'id_balance_option' => $getBalanceOption->id_balance_option,
								'balance_value' => $balance_value,
								'phone_number' => $phone_number,
								'value' => $point_needed,
								'log_point_redeem' => date('Y-m-d H:i:s'),
								'type' => 2,
							]);

							return true;
						} else {
							return false;
						}
					} else {
						return 'onprocess';
					}

    			}
			}
		}

		return false;
	}

	public static function contributorPoint($id_editor, $mode=null, $periode=null) {
		$cek = EditorModel::where('id_editor', $id_editor);
		if($cek->count() > 0) {
			$cek_point = ShortirPostLogModel::where('id_editor', $id_editor)
							->groupBy('id_post')
							->orderBy('id_post_log', 'asc');

			if($mode!=null) {
				if($mode=='periodic') { // periodic
					if($periode=='d')
						$cek_point->select([
										DB::raw('COUNT("*") as count'),
										DB::raw('DATE(published_date) as periode')
									])
									->groupBy(DB::raw('DATE(published_date)'));
					else if($periode=='w')
						$cek_point->select([
										DB::raw('COUNT("*") as count'),
										DB::raw('WEEK(published_date) as periode')
									])
									->groupBy(DB::raw('WEEK(published_date)'));
					else if($periode=='m')
						$cek_point->select([
										DB::raw('COUNT("*") as count'),
										DB::raw('MONTH(published_date) as periode')
									])
									->groupBy(DB::raw('MONTH(published_date)'));
					return $cek_point->orderBy('id_post_log', 'desc');
				} else if($periode=='c') { // current
					if($periode=='d')
						$cek_point->where(DB::raw('DATE(published_date)'));
					else if($periode=='w')
						$cek_point->where(DB::raw('WEEK(published_date)'));
					else if($periode=='m')
						$cek_point->where(DB::raw('MONTH(published_date)'));
				}
			}
			return $cek_point->count();
		}
		return 0;
	}

	public static function getRedeemBalanceOption($id_usr, $phone_number) {
		$cek = UsrModel::where('id', $id_usr);
    	if($cek->count() > 0) {
    		$get = $cek->first();
    		$totalPoint = self::getTotalPoint($id_usr);

    		$prefix_1st = substr($phone_number, 0, 1);
			if($prefix_1st == "0")
				$prefix = substr($phone_number, 0, 4);
			else if($prefix_1st == "+") {
				$prefix_2nd = substr($phone_number, 1, 1);
				if($prefix_2nd == "6")
					$prefix = 0 . substr($phone_number, 3, 3); // +62 856 -> 0 856
				else
					$prefix = 0 . substr($phone_number, 1, 3); // + 856 -> 0 856
			} else {
				$prefix_2nd = substr($phone_number, 0, 1);
				if($prefix_2nd == "6")
					$prefix = 0 . substr($phone_number, 2, 3); // +62 856 -> 0 856
				else
					$prefix = 0 . substr($phone_number, 0, 3); // + 856 -> 0 856
			}

			// Check Prefix
			$operatorPrefix = OperatorPrefixModel::where(['prefix'=>$prefix]);
			if($operatorPrefix->count() > 0) {
				$getPrefix = $operatorPrefix->first();
				if($getPrefix->operator!=null) {

					// Check Balance Option
					$balanceOption = BalanceOptionModel::where([
						['id_operator', '=', $getPrefix->operator->id_operator],
						['point_needed', '<', $totalPoint]
					]);

					if($balanceOption->count() > 0) {
						$getBalanceOption = $balanceOption->orderBy('value', 'asc')->get();

						$balanceOps = array();
						foreach ($getBalanceOption as $bo) {
							$balanceOps[] = ['id_balance_option' => $bo->id_balance_option, 'token' => $bo->token, 'value' => MyNumber::toReadableAngka($bo->value, FALSE), 'point_needed' => $bo->point_needed, 'status' => true];
						}

						return [
			              	'phone_number' => $phone_number,
			              	'full_name' => $get->full_name,
			              	'cell_provider_name' => $getPrefix->operator->name,
			              	'cell_provider_logo' => "http://xadminx.shortir.com/logoProvider/". $getPrefix->operator->logo,
			              	'balance_option' => $balanceOps,
			              	'status' => true
			          	];
					} else {
						return [
				          	'message' => "Wah kacau, point anda tidak mencukupi!",
				          	'status' => false
				      	];
					}
		        }
			} else {
				return [
		          	'message' => "Wah kacau, operator belum didukung!",
		          	'status' => false
		      	];
			}
		}

		return [
          	'message' => "Wah kacau, terjadi error!",
          	'status' => false
      	];
	}
}