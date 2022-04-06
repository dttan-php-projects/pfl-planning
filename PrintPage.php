<?php
	require("./Module/Database.php");
	include('code128.class.php');
	// @tandoan: check FOD đối với đơn hàng làm lần đầu tiên của code vật tư mới. Thi.LeBich yêu cầu 20201119
		function checkFOD($JobJacket,$ItemCode) {
			$check = false;
			$result = MiQuery("SELECT JobJacket FROM pfl_item_master_fod WHERE Item_Code ='$ItemCode';",_conn1() );
			
			if (!empty($result) ) {

				$JobJacketCheck = trim($result[0]['JobJacket']);
				// Trường hợp đã in rồi, kiêm tra xem Job có giống nhau không, nếu giống thì hiển thị FOD
				if (!empty($JobJacketCheck) ) {
					if ($JobJacketCheck == $JobJacket ) {
						$check = true;
					}
				} else {
					// Trường hợp chưa in (chưa có số Job trong bảng này)
						$check = true;
					// update JobJacket
					$conn1 = _conn1();
					mysqli_query( $conn1, "UPDATE pfl_item_master_fod SET JobJacket='$JobJacket' WHERE Item_Code ='$ItemCode';");	
					mysqli_close($conn1);
				}

			}

			return $check;
			
		}

	// @TanDoan - 20210204: Xử lý FOD cho các Item được cập nhật trong tool item FOD
		function checkNewItemFOD($JobJacket,$ItemCode) 
		{
			$check = false;
			$conn1 = _conn1();
			$table = "pfl_new_item_fod";
			$result = MiQuery("SELECT JobJacket FROM $table WHERE Item_Code ='$ItemCode';",$conn1);
			
			if (!empty($result) ) {

				$JobJacketCheck = trim($result[0]['JobJacket']);
				// Trường hợp đã in rồi, kiêm tra xem Job có giống nhau không, nếu giống thì hiển thị FOD
				if (!empty($JobJacketCheck) ) {
					if ($JobJacketCheck == $JobJacket ) {
						$check = true;
					}
				} else {
					// Trường hợp chưa in (chưa có số Job trong bảng này)
						$check = true;
					// update JobJacket
					$conn1 = _conn1();
					mysqli_query( $conn1, "UPDATE $table SET JobJacket='$JobJacket' WHERE Item_Code ='$ItemCode';");	
					mysqli_close($conn1);
				}

			}

			

			return $check;
			
		}

	// @TanDoan - 20201224: Xử lý - 1 ngày. Chổ in này xử lý tạm thời cho đến khi hết đơn đã in
		function subDate($date, $number)
		{
			$date = ( !empty($date) && ($date != '1970-01-01') ) ? $date : '1970-01-01';
			if ($date == '1970-01-01' ) {
				return "1970-01-01";
			}

			$date = date("Y-m-d", strtotime($date . "$number days"));
			if(date("l", strtotime($date)) == "Sunday") $date = date("Y-m-d", strtotime($date . "-1 days"));
			
			return $date;

		}
	// Trường hợp đơn hàng vượt 24h, 48h thì không -1 ngày
		function checkOrder24h48h($ORDER_TYPE) 
		{
			$result = false;
			if (!empty($ORDER_TYPE)) {
				$ORDER_TYPE = strtoupper($ORDER_TYPE);
				if (strpos($ORDER_TYPE, 'VN QR 24H') !== false || strpos($ORDER_TYPE, 'VN QR 48H') !== false ) {
					$result = true;
				}
			}

			return $result;
		}
	
	// @TanDoan - 20201231: Remark theo Item. mail: ON-149232 / Check công đoạn packing
	// @TanDoan - 20211026: mail: Re: Add thêm vào ghi chú đơn SpanX có đường bế
		function specialItemRemark($ItemCode) 
		{
			$result = '';

			// danh sách Item cần remark
				$array[] = array( 'item' => 'CB377957A', 'remark' => '<br> Bỏ bịch 500pcs' );
				$array[] = array( 'item' => 'P569959A', 'remark' => '<br> Bỏ bịch 500pcs' );
				$array[] = array( 'item' => 'P580934A', 'remark' => '<br> Bỏ bịch 500pcs' );
				$array[] = array( 'item' => 'CB597811A', 'remark' => "<br> Chú ý có đường bế" );
				// $array[] = array( 'item' => 'CB553611A', 'remark' => '<br/> Bó cọc và Bỏ bịch 100 pcs' );

			// check
				foreach ($array as $value ) {
					$item = $value['item'];
					if ($ItemCode == $item ) {
						$result = $value['remark'];
						break;
					}
				}

				// 	$array = array( 'CB377957A', 'P569959A', 'P580934A' );
				// // check
				// 	foreach ($array as $item ) {
				// 		if ($ItemCode == $item ) {
				// 			$result = "<br/> Bỏ bịch 500pcs";
				// 			break;
				// 		}
				// 	}

				
			// result
				return $result;
		}

	// @TanDoan - 20210128: Lấy thông số Print Speed
		function getPrintSpeed($ItemCode)
		{
			$Printing_Speed = '';
			$conn1 = _conn1();
			$result = MiQuery("SELECT `Printing_Speed` FROM pfl_item_master WHERE Item_Code ='$ItemCode';",$conn1);
			if (!empty($result) ) {
				$Printing_Speed = trim($result[0]['Printing_Speed']);
			}

			return $Printing_Speed;
		}
		
	// @TanDoan - 20210310: mail [SMARTWOOL] F21 BULK _ AD VN _ PACKING ISSUE FOR ITEM L-20
	// @TanDoan - 20211108: mail: REMARK FOR LEADING START: xử lý lại lấy thêm bill to number
	// @TanDoan - 20211214: mail: Re: VS/ Sakurai - Request for detailed PKL 
	// @TanDoan - 20220214: mail: THÔNG TIN YÊU CẦU ĐẶC BIỆT CỦA KHÁCH HÀNG - PFL ROTARY
		function remarkShiptoRBO($RBO, $ShipToCustomer, $BillToNumber=null )
		{
			$remark = '';

			if (!empty($RBO) && !empty($ShipToCustomer) ) {
				
				$RBO = strtoupper($RBO);
				$ShipToCustomer = strtoupper($ShipToCustomer);

				$array[] = array(
					'RBO' => 'SMARTWOOL',
					'ShipToCustomer' => 'Youngone Nam Dinh',
					'remark' => '<br/> ĐÓNG GÓI 1000pcs/bịch'
				);

				$array[] = array(
					'RBO' => 'QUIKSILVER',
					'ShipToCustomer' => 'MAY MẶC QTNP',
					'remark' => '<br/> Pack bằng Polybag - bỏ bịch 500Pcs'
				);

				// updated 2021-05-27 12:55, mail: Re: NHÃN CẶP ĐƠN SHIP TO DAQIAN
				$array[] = array(
					'RBO' => 'EUROPE ADIDAS',
					'ShipToCustomer' => 'DAQIAN TEXTILE',
					'remark' => '<br/> Nhãn cặp, bỏ bịch 500Pcs'
				);


				// updated 2021-07-09 08:55, mail: REMARK FOR LEADING START
				// updated 2021-11-08 10:00, mail: REMARK FOR LEADING START
				$array[] = array(
					'RBO' => 'WALMART',
					'ShipToCustomer' => 'MAY MAC LEADING STAR', 
					'BillToNumber' => '777115', 
					'remark' => '<br/> Tach rieng size/bich polybag-Đóng gói 500Pcs / hộp - Làm trim card 2 mặt của nhãn'
				);

				// $array[] = array(
				// 	'RBO' => '100011',
				// 	'ShipToCustomer' => 'SAKURAI VIETNAM COMPANY LIMITED',
				// 	'remark' => '<br/> Nhập kho theo size'
				// );

				// 2022-02-14: email: THÔNG TIN YÊU CẦU ĐẶC BIỆT CỦA KHÁCH HÀNG - PFL ROTARY
				// số thứ tự 1
				$array[] = array(
					'RBO' => 'ADIDAS',
					'ShipToCustomer' => 'QUANG VIỆT',
					'remark' => '<br> Hộp chẵn 1000 <br> Bó cọc 200pcs/bó, mỗi hộp 5 bó. Không kim loại'
				);

				// số thứ tự 3
				$array[] = array(
					'RBO' => '100011',
					'ShipToCustomer' => 'DAYLEEN INTIMATES INC',
					'remark' => '<br/> 	Lấy mỗi size 1 pcs, đóng kèm thùng hàng'
				);

				// số thứ tự 4
				$array[] = array(
					'RBO' => '100011',
					'ShipToCustomer' => 'CHUTEX INTERNATIONAL (LONG AN) COMPANY., LTD',
					'remark' => '<br> Bỏ hộp 500 pcs'
				);

				// số thứ tự 5
				$array[] = array(
					'RBO' => '100011',
					'ShipToCustomer' => 'CHUTEX INTERNATIONAL CO,LTD',
					'remark' => '<br> Bỏ hộp 500 pcs'
				);

				// số thứ tự 5
				$array[] = array(
					'RBO' => '100011',
					'ShipToCustomer' => 'FASHION GARMENTS 2',
					'remark' => '<br> Hộp chẵn 500. Bó cọc 50 pcs/bó (kích thước dưới 35 bỏ bịch)'
				);

				// số thứ tự 4
				// $array[] = array(
				// 	'RBO' => '100011',
				// 	'ShipToCustomer' => 'DAYLEEN INTIMATES INC',
				// 	'remark' => '<br/> 	Lấy mỗi size 1 pcs, đóng kèm thùng hàng'
				// );

				// số thứ tự 13
				$array[] = array(
					'RBO' => 'QUICK SILVER',
					'ShipToCustomer' => 'MAY MẶT QTNP',
					'remark' => '<br> Bỏ bịch 500 pcs/bịch'
				);

				// số thứ tự 14
				$array[] = array(
					'RBO' => 'REEBOK',
					'ShipToCustomer' => 'QUANG VIỆT',
					'remark' => '<br> Hộp chẵn 1000. Bó cọc 200 pcs/bó, mỗi hộp 5 bó. Không kim loại'
				);

				// số thứ tự 15
				$array[] = array(
					'RBO' => 'SMARTWOOL',
					'ShipToCustomer' => 'YOUNGONE NAM ĐỊNH',
					'remark' => '<br> 	Bỏ bịch 1000 pcs/bịch'
				);
				
				// check 
				foreach ($array as $data ) {

					$RBOCheck = strtoupper($data['RBO']);
					$ShipCheck = strtoupper($data['ShipToCustomer']);
					$BillToNumberCheck = isset($data['BillToNumber']) ? $data['BillToNumber'] : '100011';
					
					if (strpos($RBO, $RBOCheck) !== false ) {
						// Đơn Ship to giống LEADING STAR là 1 trường hợp đặc biệt
						if (strpos($ShipToCustomer, 'LEADING STAR') !== false ) {
							if ( (strpos($ShipToCustomer, $ShipCheck) !== false) || (strpos($BillToNumber, $BillToNumberCheck) !== false) ) {
								$remark = $data['remark'];
								break; // trường hợp này đã gán remark là remark cần trả về
							}	
						} else {
							if ( (strpos($ShipToCustomer, $ShipCheck) !== false) ) {
								$remark = $data['remark'];
								break; // trường hợp này đã gán remark là remark cần trả về
							}
						}
						
						
					} else if ($RBOCheck == '100011' ) { // trường hợp này k cần RBO
						if ( (strpos($ShipToCustomer, $ShipCheck) !== false) ) {
							$remark = $data['remark'];
							break; // trường hợp này đã gán remark là remark cần trả về
						}
					}

				}
			}

			return $remark;
			
		}

	// @TanDoan - 20220214, mail: "THÔNG TIN YÊU CẦU ĐẶC BIỆT CỦA KHÁCH HÀNG - PFL ROTARY"
		function remarkRBO($RBO, $item ) 
		{
			$remark = '';

			$rboArr[] = array( 'rbo' => 'CARTER', 'remark' => 'Đóng mộc "VID # 50014" lên thùng hàng'); // 7
			$rboArr[] = array( 'rbo' => 'CASUAL MALE', 'remark' => 'Kích thước 90mm - hộp chẳn 200 pcs'); // 8
			$rboArr[] = array( 'rbo' => 'H&M', 'remark' => 'Hộp chẵn 1000. Bó cọc 200 pcs/bó (ngoại trừ kích thước 29 bỏ bịch). Đóng thùng mỗi size 1 bịch riêng, đóng mộc H&M'); // 9
			$rboArr[] = array( 'rbo' => 'LTAPPAREL', 'remark' => 'Kích thước 182 mm - hộp chẳn 400 pcs'); // 10
			$rboArr[] = array( 'rbo' => 'MUJI', 'remark' => 'Bỏ bịch 1200 pcs/ bịch, đóng nhãn cặp'); // 11
			$rboArr[] = array( 'rbo' => 'PXVN', 'remark' => 'Kích thước 25mm bỏ bịch 1000 pcs/bịch'); // 12
			$rboArr[] = array( 'rbo' => 'VICTORIAS', 'remark' => 'Hộp chẳn 400 pcs (Kích thước 32 bỏ bịch 500)'); // 16
			$rboArr[] = array( 'rbo' => 'ADIDAS', 'remark' => 'Đóng gói kèm tờ Scan mã code' ); // 2

			if (!empty($RBO ) ) {
				foreach ($rboArr as $value ) {

					$rboCheck = $value['rbo'];
					if (strpos(strtoupper($RBO), $rboCheck ) !== false ) {
						
						// Trường hợp đặc biệt, RBO = ADIDAS
						if (strpos(strtoupper($RBO), 'ADIDAS' ) !== false ) {
							$itemADIDASArr = array( 'CB573755A','CB573768A','CB573770A','CB573772A','CB575853A','CB579177A','CB579166A','CB574766A','CB576227A','CB580589A','CB580593A','CB573747A','CB573748A','CB619392A','CB619395A','CB620057A','CB620058A' );
							foreach ($itemADIDASArr as $itemCheck ) {
								if ($item == $itemCheck ) {
									$remark = $value['remark'];
									break;		
								}
							}
						} else {
							$remark = $value['remark'];
							break;
						}
						
					}
				}
			}

			return $remark;
		}

	//Chưa sử dụng @TanDoan - 20220216: Remark Nhập kho theo size. 
		function nhapKhoTheoSize($RBO, $BillToCustomer, $ShipToCustomer, $item )
		{
			$data[] = array(
				'rbo' => '11111111',
				'bill_to' => '11111111',
				'ship_to' => 'SAKURAI VIETNAM COMPANY LIMITED',
				'item' => '11111111'
			);

			$data[] = array(
				'rbo' => '11111111',
				'bill_to' => '11111111',
				'ship_to' => 'SON HA CO. LTD',
				'item' => '11111111'
			);

			$data[] = array( 'rbo' => 'H&M', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');
			$data[] = array( 'rbo' => 'H &M', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');
			$data[] = array( 'rbo' => 'H & M', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');
			$data[] = array( 'rbo' => 'H& M', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');
			$data[] = array( 'rbo' => 'HENNES', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');

			$data[] = array( 'rbo' => 'THE WILLIAM CARTER', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');
			$data[] = array( 'rbo' => 'OSHKOSH', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');
			$data[] = array( 'rbo' => 'PRIMARK', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');
			$data[] = array( 'rbo' => 'GEORGE', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');
			$data[] = array( 'rbo' => 'FASHION GARMENT', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');

			$data[] = array( 'rbo' => 'CARMEL', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');
			$data[] = array( 'rbo' => 'VICTORIAS SECRET', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');
			$data[] = array( 'rbo' => 'TARGET', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');
			$data[] = array( 'rbo' => 'MUJI', 'bill_to' => '11111111', 'ship_to' => '11111111', 'item' => '11111111');

			$data[] = array(
				'rbo' => '11111111',
				'bill_to' => 'SAE-A TRADING',
				'ship_to' => '11111111',
				'item' => '11111111'
			);

			$data[] = array(
				'rbo' => '11111111',
				'bill_to' => 'KANMAX ENTERPRISES LIMITED',
				'ship_to' => 'I APPAREL INT’L GROUP PTE LTD',
				'item' => '11111111'
			);

			$data[] = array(
				'rbo' => '11111111',
				'bill_to' => 'KANMAX ENTERPRISES LIMITED',
				'ship_to' => 'IK APPAREL CO. LTD',
				'item' => '11111111'
			);

			$data[] = array(
				'rbo' => '11111111',
				'bill_to' => 'EPOCH LAY ENTERPRISE., LTD',
				'ship_to' => 'EPOCH LAY ENTERPRISE., LTD',
				'item' => '11111111'
			);

			$data[] = array(
				'rbo' => '11111111',
				'bill_to' => '11111111',
				'ship_to' => 'CHUTEX INTERNATIONAL (LONG AN) COMPANY., LTD',
				'item' => '11111111'
			);
			
			


			
		}




	$TurnPrint = true;
	if(!isset($_GET["JJ"])) return;
	if(isset($_GET["P"])) $TurnPrint = false;
	$JobJacket = $_GET["JJ"];
	$SOLine = "";
	$ItemCode = "";
	$ReceivingDate = "";
	$Dueday = "";
	$Promise = "";
	$Request = "";
	$Qty = "";
	$NumSize = "";
	$QtyScrap = "";
	$Line = "";
	$Teeth = "";
	$RateScrap = "";
	$PersonPrint = "";
	$QtyNeed = "";
	$QtyExport = "";
	$PersonPIC = "";
	$RemarkTop = "";
	$RemarkBot = "";
	$PONumber = "";
	$LengthLabel = "";
	$WidthLabel = "";
	$MaterialCode = "";
	$InkNum = "";
	$InkCode = "";
	$PrintMethod = "";
	$CutMethod = "";
	$FoldMethod = "";
	$SizeFinish = "";
	$Drying = "";
	$Temp = "";
	$RBO = "";
	$DataRaw = MiNonQuery( "UPDATE `pfl_order_list` SET PrintDate = NOW() WHERE JobJacket = '$JobJacket';", _conn1());
	$DataRaw = MiQuery( "SELECT `pfl_order_list`.`ID`,
									`pfl_order_list`.`JobJacket`,
									`pfl_order_list`.`SOLine`,
									`pfl_order_list`.`ItemCode`,
									`pfl_order_list`.`ReceivingDate`,
									`pfl_order_list`.`Dueday`,
									`pfl_order_list`.`RequestDate`,
									`pfl_order_list`.`PromiseDate`,
									`pfl_order_list`.`Qty`,
									`pfl_order_list`.`NumSize`,
									`pfl_order_list`.`QtyScrap`,
									`pfl_order_list`.`Line`,
									`pfl_order_list`.`Teeth`,
									`pfl_order_list`.`RateScrap`,
									`pfl_order_list`.`PersonPrint`,
									`pfl_order_list`.`QtyNeed`,
									`pfl_order_list`.`QtyExport`,
									`pfl_order_list`.`PersonPIC`,
									`pfl_order_list`.`RemarkTop`,
									`pfl_order_list`.`RemarkBot`,
									`pfl_order_list`.`PONumber`,
									`pfl_order_list`.`LengthLabel`,
									`pfl_order_list`.`WidthLabel`,
									`pfl_order_list`.`MaterialCode`,
									`pfl_order_list`.`InkNum`,
									`pfl_order_list`.`InkCode`,
									`pfl_order_list`.`PrintMethod`,
									`pfl_order_list`.`CutMethod`,
									`pfl_order_list`.`FoldMethod`,
									`pfl_order_list`.`SizeFinish`,
									`pfl_order_list`.`Drying`,
									`pfl_order_list`.`Temp`,
									`pfl_order_list`.`RBO`,
									`pfl_order_list`.`UEE`
								FROM `pfl_order_list` WHERE JobJacket = '$JobJacket';", _conn1());
	foreach($DataRaw as $row) {
		$JobJacket = $row["JobJacket"];
		$SOLine = $row["SOLine"];
		$ItemCode = $row["ItemCode"];
		$ReceivingDate = $row["ReceivingDate"];
		$Dueday = $row["Dueday"];
		$Request = $row["RequestDate"];
		$Promise = $row["PromiseDate"];
		$Qty = $row["Qty"];
		$NumSize = $row["NumSize"];
		$QtyScrap = $row["QtyScrap"];
		$Line = $row["Line"];
		$Teeth = $row["Teeth"];
		$RateScrap = $row["RateScrap"];
		$PersonPrint = $row["PersonPrint"];
		$QtyNeed = $row["QtyNeed"];
		$QtyExport = $row["QtyExport"];
		$PersonPIC = $row["PersonPIC"];
		$RemarkTop = str_replace("<br/>","\n",$row["RemarkTop"]);
		$RemarkBot = $row["RemarkBot"];
		$PONumber = $row["PONumber"];
		$LengthLabel = $row["LengthLabel"];
		$WidthLabel = $row["WidthLabel"];
		$MaterialCode = $row["MaterialCode"];
		$InkNum = $row["InkNum"];
		$InkCode = $row["InkCode"];
		$PrintMethod = $row["PrintMethod"];
		$CutMethod = $row["CutMethod"];
		$FoldMethod = $row["FoldMethod"];
		$SizeFinish = $row["SizeFinish"];
		$Drying = $row["Drying"];
		$Temp = $row["Temp"];
		$RBO = $row["RBO"];
		$UEE = $row["UEE"];
	}

	$ArrayItemSpecial = ["P519068","P519069","P519072","P519076","P519650","P519801D","P519802A","P519810A","P519816A","P519836A"];
	$RemarkNew = "";
	if(in_array($ItemCode, $ArrayItemSpecial)) $RemarkNew = "CHỈ IN, KHÔNG CẮT, GIAO DẠNG ROLL THEO TỪNG SIZE <br>";

	// Note (@TanDoan): Đây là trường hợp (đã xử lý trước đó) lấy thông tin CRD. Nên tại đây -1 ngày cho đúng yêu cầu.
	// @TanDoan - 20210923: xử lý -2 ngày. Nếu trừ xong và rơi vào chủ nhật thì trừ - 1 ngày // Trường hợp ORDER_TYPE_NAME là VN QR 24H, VN QR 48H khong tru
	$Multi = 0;
	$CRD = 0;
	$DataRaw = MiQuery( "SELECT ORDER_NUMBER, LINE_NUMBER, REQUEST_DATE, ORDER_TYPE_NAME FROM au_avery.vnso_total WHERE ORDER_NUMBER = '" . explode("-",$SOLine)[0] . "' AND LINE_NUMBER = '" . explode("-",$SOLine)[1] . "';",_conn1());
	foreach($DataRaw as $R) {
		$CRD = $R["REQUEST_DATE"];
		if (!checkOrder24h48h($R["ORDER_TYPE_NAME"]) ) {
			$CRD = date('Y-m-d', strtotime($CRD));
			$CRD = subDate($CRD, -2);
		}
		
		$Multi = $Multi + 1;
	}

	$PDS = "";
	
	// $DataRaw = MiQuery( "SELECT Order_Number,Line_Number,PromiseDate,ETA,Comments FROM au_avery.cs_confirmpromise WHERE StatusActive = 1 AND Order_Number = '" . explode("-",$SOLine)[0] . "';", $connM2 );
	// foreach($DataRaw as $R) {
	// 	$Promise = $row["PromiseDate"];
	// }

	switch(strtoupper($PrintMethod))
	{
		case "TWO SIDE": $PrintMethod = "In Hai Mặt"; break;
		case "FRONT SIDE": $PrintMethod = "In Một Mặt";break;
		case "NONE": $PrintMethod = "Không In";break;
		default:break;
	}

	$CutMethod = strtoupper($CutMethod);
	$FoldMethod = strtoupper($FoldMethod);

	if($CutMethod == "HOT CUT") {	$CutMethod = "Nóng"; }
	else if($CutMethod == "SONIC CUT" && $FoldMethod != "CUT SINGLE") {	$CutMethod = "Cao Tầng";  }
	else if($CutMethod == "SONIC CUT" && $FoldMethod == "CUT SINGLE") {	$CutMethod = "Thẳng Cao Tầng";  }
	else if($CutMethod == "SINGLE LASER CUT") {	$CutMethod = "Thẳng Cao Tầng"; }
	else if($CutMethod == "COLD CUT") {	$CutMethod = "Nguội"; }
	else if($CutMethod == "ROLLS") {	$CutMethod = "Thẳng Nóng"; }
	else if($CutMethod == "LAZER CUT") {	$CutMethod = "Lazer"; }
	else if($CutMethod == "COLD HOT CUT") {	$CutMethod = "Nóng Nguội"; }
	else if($CutMethod == "DIE CUT") {	$CutMethod = "Thẳng Nguội"; }
	else if($CutMethod == "LAZER HOT CUT") {	$CutMethod = "Lzer + Hot Cut"; }
	else if($CutMethod == "FUSE CUT") {	$CutMethod = "Cắt Nhiệt"; }


	if($FoldMethod == "CENTER FOLD") {	$FoldMethod = "Gấp Giữa"; }
	else if($FoldMethod == "UNEVEN BOOKLET FOLD") {	$FoldMethod = "Gấp Sách Lệch"; }
	else if($FoldMethod == "ROLL") {	$FoldMethod = "Giao Dạng Cuộn"; }
	else if($FoldMethod == "UNVEN END FOLD") {	$FoldMethod = "Gấp Lệch"; }
	else if($FoldMethod == "BOOKLET FOLD") {	$FoldMethod = "Gấp Sách"; }
	else if($FoldMethod == "CENTER END FOLD") {	$FoldMethod = "Gấp Giữa + Hai Đầu"; }
	else if($FoldMethod == "TOP AND BOTTOM FOLD") {	$FoldMethod = "Gấp Hai Bên"; }
	else if($FoldMethod == "MANHATTAN FOLD") {	$FoldMethod = "Gấp Hai Manhata"; }
	else if($FoldMethod == "MITRE FOLD") {	$FoldMethod = "Gấp chéo góc"; }
	else if($FoldMethod == "END LEFT FOLD") {	$FoldMethod = "Gấp một đầu"; }



	$Print_Machine = "";
	$Cut_Machine = "";
	$Plan_YMD = "";
	$STT = "";
	$Plan_Cut = "";
	$STT_Cut = "";

	$DataRaw = MiQuery( "SELECT PRINT_MACHINE, CUT_MACHINE, PLAN_YMD, MATERIAL, STT, PLAN_CUT, STT_CUT FROM pfl_machine_mark WHERE ID IN (SELECT MAX(ID) FROM pfl_machine_mark WHERE JOBJACKET = '" . $JobJacket . "');",_conn1());
	foreach($DataRaw as $R) {
		$Print_Machine = $R["PRINT_MACHINE"];
		$Cut_Machine = $R["CUT_MACHINE"];
		$Plan_YMD = $R["PLAN_YMD"];
		if($R["MATERIAL"] != "") $MaterialCode = $R["MATERIAL"];
		$STT = $R["STT"];

		$Plan_Cut = $R["PLAN_CUT"];
		$STT_Cut = $R["STT_CUT"];
	}

	$Weight = "0";

	$DataRaw = MiQuery( "SELECT MaterialDescription, Weight FROM `pfl_material_description` WHERE MaterialCode LIKE '%$MaterialCode%' LIMIT 1;",_conn1());
	foreach($DataRaw as $R) {
		$MaterialCode = $MaterialCode . " - " . $R["MaterialDescription"];
		$Weight = $R["Weight"];
	}

	if(strlen($SOLine) < 20) {
		$BarcodeX = $SOLine;
		$NumCare = 1;
	} else {
		$BarcodeX = $JobJacket;
		$NumCare = 0;
	}

	// // if($Promise == "" || $Promise == "01-01-1970")
	// // {
	// // 	$Promise = MiQueryScalar( "SELECT PromiseDate FROM au_avery.cs_revisepromisedate WHERE Order_Number = '" . explode("-",$SOLine)[0] . "' ORDER BY ID DESC LIMIT 1;", dbMiConnect246() );
	// // }

	$barcode = new phpCode128(str_replace("-B","",$BarcodeX), 100, 'c:\windows\fonts\verdana.ttf', 18);
	$barcode->setBorderWidth(0);
	$barcode->setBorderSpacing(10);
	$barcode->setPixelWidth(4);
	$barcode->setEanStyle(false);
	$barcode->setShowText(false);
	$barcode->setAutoAdjustFontSize(true);
	$barcode->setTextSpacing(5);
	$barcode->saveBarcode('Images//'.$JobJacket.'.png');
	$TotalSOLine = strlen($SOLine) - strlen(str_replace("-","",$SOLine));
	
	$SizeData = MiQuery("SELECT `so_line`, `size` as SIZE, `qty` as QTY FROM `pfl_size_save` WHERE `no_number` = '$JobJacket' ORDER BY length(so_line), so_line ASC", _conn1() );

	/*
		Lưu ý số size: (Nhung.LePham)
		1. Một số trường hợp Planning sẽ nhập số Size vào hệ thống ==> tương ứng với số size để làm layout
		2. Trường hợp số size lấy từ Automail là số size sử dụng cho CUT
	*/ 
	// Dành cho Layout hiển thị ở giữa lệnh sx
	// là $NumSize
	// Dành cho CUT (hiển thị dưới phần chi tiết Size)
	$cutNumberSize = count($SizeData);
	
	if(count($SizeData) > 0) {

		// Hiển thị 15 size, vượt quá thì không hiển thị
		if ($cutNumberSize > 20 ) {
			/* SHOW SIZE PRINTING ==================================================*/
			// Trường hợp này không cần lấy size data
			$TableSize1 = '<table style="width:100%; text-align:center" border=1>
							<tr>
								<td style="width:50px">STT</td>
								<td>Size (*)</td>
								<td style="width:150px">'.$SOLine.'</td>
								<td style="width:150px">Σ Số Lượng</td>
								<td style="width:100px">Mét</td>
								<td style="width:100px">%Scrap</td>
							</tr>
							<tr>
								<td style="width:50px">1</td>
								<td>'.$cutNumberSize.' size (Vượt quá layout hiển thị)</td>
								<td style="width:150px">'.$Qty.'</td>
								<td style="width:150px">'.$Qty.'</td>
								<td style="width:100px"></td>
								<td style="width:100px"></td>
							</tr>
							<tr>
								<td></td>
								<td>Total</td>
								<td></td>
								<td>'.$Qty.'</td>
								<td></td>
								<td></td>
							</tr></table>';
		} else {
			
			if($cutNumberSize != 1 ) {

				/* GET SIZE DATA ==================================================*/
				$ArrSize = array();
				if ($TotalSOLine == 1 ) {
					
					foreach($SizeData as $K=>$R) {
						if(!isset($ArrSize[$R["SIZE"]])) {
							$ArrSize[$R["SIZE"]] = 0;
						} else {
							$SIZE = $R["SIZE"] . " ($K)";
							$ArrSize[$SIZE] = 0;
						}
					} 
				} else {
					foreach($SizeData as $K=>$R) if(!isset($ArrSize[$R["SIZE"]])) $ArrSize[$R["SIZE"]] = 0;
				}
				
				
				// Nếu đơn có 1 Size và Size = NON ==> Đơn 1 Size
				$DataSize = array();
				if ($TotalSOLine == 1 ) {
	
					// get data
					foreach($SizeData as $K=>$R) {
						$SOLINE = $R["so_line"];
						$SIZE = $R["SIZE"];
	
						if (isset($DataSize[$SOLINE][$SIZE]) ) {
							if (in_array($DataSize[$SOLINE][$SIZE], $DataSize[$SOLINE]) ) {
								$SIZE = $SIZE . " ($K)";
								$DataSize[$SOLINE][$SIZE] = (int)$R["QTY"];
							}
						} else {
							$DataSize[$SOLINE][$SIZE] = (int)$R["QTY"];
						}
						
					}
					
				} else {
					
					// get data
					foreach($SizeData as $K=>$R) {
						$SOLINE = $R["so_line"];
						$SIZE = $R["SIZE"];
						if(!isset($DataSize[$SOLINE][$SIZE]) ) {
							$DataSize[$SOLINE][$SIZE] = (int)$R["QTY"];
						} else {
							$DataSize[$SOLINE][$SIZE] += (int)$R["QTY"];
						}
						
					}	
				}
		
				/* SHOW SIZE PRINTING ==================================================*/
				$TableSize1 = '<table style="width:100%; text-align:center;font-weight:normal" border=1>
									<tr style="background:yellow;font-weight:bold">
										<td style="width:100px">SOLINE</td>';
				foreach($ArrSize as $K=>$R) $TableSize1 = $TableSize1 . "<td>$K</td>";
				$TableSize1 . "</tr>";
				foreach($DataSize as $C=>$r) {
					$TableSize1 .= "<tr><td>" . $C . "</td>";
					foreach($ArrSize as $K=>$R) {
						$TableSize1 = $TableSize1 . "<td>" . $r[$K] . "</td>";
						$ArrSize[$K] += $r[$K];
					}
					$TableSize1 . "</tr>";
				}
		
				$TableSize1 .= "<tr style='background:yellow;font-style: italic;text-decoration: underline;'><td>Total</td>";
				foreach($ArrSize as $K=>$R) $TableSize1 = $TableSize1 . "<td>$R</td>";
				$TableSize1 . "</tr>";
				
				$TableSize1 .= "<tr style='background:yellow;font-weight:bold'><td>Yard</td>";
				foreach($ArrSize as $K=>$R) $TableSize1 = $TableSize1 . "<td>" . round($R*$QtyNeed/$Qty,2) . "</td>";
				$TableSize1 . "</tr>";
		
				
				$TableSize1 .= "<tr style='font-weight:bold;height:50px'><td>Thiếu</td>";
				foreach($ArrSize as $K=>$R) $TableSize1 = $TableSize1 . "<td></td>";
				$TableSize1 . "</tr>";
		
				$TableSize1 .= "</table>";
			} else {
				/* SHOW SIZE PRINTING ==================================================*/
				// Trường hợp này không cần lấy size data
				$TableSize1 = '<table style="width:100%; text-align:center" border=1>
								<tr>
									<td style="width:50px">STT</td>
									<td>Size (*)</td>
									<td style="width:150px">'.$SOLine.'</td>
									<td style="width:150px">Σ Số Lượng</td>
									<td style="width:100px">Mét</td>
									<td style="width:100px">%Scrap</td>
								</tr>
								<tr>
									<td style="width:50px">1</td>
									<td>1 Size</td>
									<td style="width:150px">'.$Qty.'</td>
									<td style="width:150px">'.$Qty.'</td>
									<td style="width:100px"></td>
									<td style="width:100px"></td>
								</tr>
								<tr>
									<td></td>
									<td>Total</td>
									<td></td>
									<td>'.$Qty.'</td>
									<td></td>
									<td></td>
								</tr></table>';
			}
		}

		

		

	} else {

		if($NumSize != 1 ) {
			// Lấy theo logic cũ. Đến giai đoạn Planning ngừng in đơn cũ thì bỏ
			$SizeData = MiQuery("SELECT ORDER_NUMBER, LINE_NUMBER, SIZE, QTY FROM au_avery.vnso_size WHERE CONCAT(ORDER_NUMBER,'-',LINE_NUMBER) IN (SELECT SOLINE FROM pfl_order_line WHERE IDOrder = '$JobJacket') ORDER BY CAST(LINE_NUMBER AS UNSIGNED) DESC",_conn1()); 	
			if(count($SizeData) > 0) {
				foreach($SizeData as $K=>$R) if($R["SIZE"] == "") $SizeData[$K]["SIZE"] = ($K + 1);
				$ArrSize = array();
				foreach($SizeData as $K=>$R) if(!isset($ArrSize[$R["SIZE"]])) $ArrSize[$R["SIZE"]] = 0;
				$DataSize = array();
				foreach($SizeData as $K=>$R) {
					$SOLINE = $R["ORDER_NUMBER"] . "-" . $R["LINE_NUMBER"];
					if(!isset($DataSize[$SOLINE])) $DataSize[$SOLINE] = $ArrSize;
					$DataSize[$SOLINE][$R["SIZE"]] += $R["QTY"];
				}

				$TableSize1 = '<table style="width:100%; text-align:center;font-weight:normal" border=1>
									<tr style="background:yellow;font-weight:bold">
										<td style="width:100px">SOLINE (*)</td>';
				foreach($ArrSize as $K=>$R) $TableSize1 = $TableSize1 . "<td>$K</td>";
				$TableSize1 . "</tr>";
				foreach($DataSize as $C=>$r) {
					$TableSize1 .= "<tr><td>" . $C . "</td>";
					foreach($ArrSize as $K=>$R) {
						$TableSize1 = $TableSize1 . "<td>" . $r[$K] . "</td>";
						$ArrSize[$K] += $r[$K];
					}
					$TableSize1 . "</tr>";
				}

				$TableSize1 .= "<tr style='background:yellow;font-style: italic;text-decoration: underline;'><td>Total</td>";
				foreach($ArrSize as $K=>$R) $TableSize1 = $TableSize1 . "<td>$R</td>";
				$TableSize1 . "</tr>";
				
				$TableSize1 .= "<tr style='background:yellow;font-weight:bold'><td>Yard</td>";
				foreach($ArrSize as $K=>$R) $TableSize1 = $TableSize1 . "<td>" . round($R*$QtyNeed/$Qty,2) . "</td>";
				$TableSize1 . "</tr>";

				
				$TableSize1 .= "<tr style='font-weight:bold;height:50px'><td>Thiếu</td>";
				foreach($ArrSize as $K=>$R) $TableSize1 = $TableSize1 . "<td></td>";
				$TableSize1 . "</tr>";

				$TableSize1 .= "</table>";
				$TableSize1 .= '<br><span style="color:red;">Đơn lấy SIZE theo cách cũ</span>';
			}
		} else {
			$TableSize1 = '<table style="width:100%; text-align:center" border=1>
							<tr>
								<td style="width:50px">STT</td>
								<td>Size (*)</td>
								<td style="width:150px">'.$SOLine.'</td>
								<td style="width:150px">Σ Số Lượng</td>
								<td style="width:100px">Mét</td>
								<td style="width:100px">%Scrap</td>
							</tr>
							<tr>
								<td style="width:50px">1</td>
								<td>1 Size</td>
								<td style="width:150px">'.$Qty.'</td>
								<td style="width:150px">'.$Qty.'</td>
								<td style="width:100px"></td>
								<td style="width:100px"></td>
							</tr>
							<tr>
								<td></td>
								<td>Total</td>
								<td></td>
								<td>'.$Qty.'</td>
								<td></td>
								<td></td>
							</tr></table>';
			$TableSize1 .= '<br><span style="color:red;">Đơn lấy SIZE theo cách cũ</span>';
		}
		
		
	}

	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>Item Maintain</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<script src="./Module/JS/jquery-1.10.1.min.js"></script>
	</head>
	<script>
		window.print();                                             // Print preview
		setTimeout(function(){
			window.close();
		},800);
	</script>
	<style>
		@page {
			size: A4;
			margin: 10mm 5mm 10mm 5mm;
		}
			@media print {
			html, body {
				width: 210mm;
				height: 280mm;
				font-family: "Source Sans Pro","Helvetica Neue",Helvetica;
			}
		/* ... the rest of the rules ... */
		}

		html, body {
				width: 840px;
				height: 1100px;
				font-family: "Source Sans Pro","Helvetica Neue",Helvetica;
			}
		table{
			border-collapse: collapse;
		}

		table, th, td {
			/*border: 1px solid #de943e;*/
		}
		table {
			table-layout: fixed;
			white-space: normal!important;
		}
		td {
			word-wrap: break-word;
		}

		.BorderAll{
			border: 1px #de943e solid;
		}
		div.page
		{
			page-break-after: always;
			page-break-inside: avoid;
		}

		.kh-in-cut{
			width:50%;
			font-weight:bold;
			color:red;
			padding:5px;
			font-size:18px;
			word-spacing:-2px;
			border:1px solid #de943e;
			margin:0 !important;
			background: yellow;
			text-align: center;
		}
	</style>
	<?php 

	$ShipToCustomer = "";
	$Creation_D = "";
	$Order_Type = "";
	$PACKING_INSTRUCTIONS = "";
	$CustomerItem = "";
	$BillToCustomer = "";
	$SOxLine = explode("-",explode(",",$SOLine)[0]);
	$SOxxLine = $SOxLine[0] . "-" . $SOxLine[1];
	$DataRaw = MiQuery( "SELECT SHIP_TO_CUSTOMER, CREATION_DATE, ORDER_TYPE_NAME, PACKING_INSTRUCTIONS, CUSTOMER_ITEM, BILL_TO_CUSTOMER, BILL_TO_NUMBER
								FROM au_avery.vnso_total WHERE ORDER_NUMBER = '" . $SOxLine[0] . "'  AND LINE_NUMBER = '" . $SOxLine[1] . "' LIMIT 1;",_conn1());
	foreach($DataRaw as $R) {
		$ShipToCustomer = $R["SHIP_TO_CUSTOMER"];
		$BillToCustomer = $R["BILL_TO_CUSTOMER"];
		$Creation_D = $R["CREATION_DATE"];
		$Order_Type = $R["ORDER_TYPE_NAME"];
		$PACKING_INSTRUCTIONS = $R["PACKING_INSTRUCTIONS"];
		$CustomerItem = $R["CUSTOMER_ITEM"];
		$BILL_TO_NUMBER = $R["BILL_TO_NUMBER"];
	}
	$RBO = strtoupper($RBO);
	$MLAArr = array();
	$MLAArr []= "ADIDAS";
	$MLAArr []= "REEBOK";
	$MLAArr []= "AMAZON";
	$MLAArr []= "FAST RETAILING";
	$MLAArr []= "G.U";
	$MLAArr []= "H&M";
	$MLAArr []= "H M HENNES";
	$MLAArr []= "NIKE";
	$MLAArr []= "PRIMARK";
	$MLAArr []= "PUMA";
	$MLAArr []= "UNDER ARMOUR";
	$MLAArr []= "DECATHLON";
	$MLAArr []= "INDITEX";
	$MLAArr []= "TARGET";
	$MLAArr []= "COLUMBIA";
	$MLAArr []= "WALMART";
	$MLAArr []= "VICTORIAS SECRET";
	$GLOBALS["MLA"] = "";

	foreach($MLAArr as $R) {
		if(strpos($RBO,$R) !== false) $GLOBALS["MLA"] = "MLA/ƯU TIÊN 1";
	}
	$RBO = str_replace("&AMP;","&",$RBO);
	$RBO = str_replace("AMP;","&",$RBO);

	$GLOBALS["PersonPIC"] = $PersonPIC;
	$GLOBALS["Plan_YMD"] = $Plan_YMD;
	$GLOBALS["RBO"] = $RBO;
	$GLOBALS["JobJacket"] = $JobJacket;
	$GLOBALS["ReceivingDate"] = $ReceivingDate;
	$GLOBALS["ShipToCustomer"] = $ShipToCustomer;
	$GLOBALS["Teeth"] = $Teeth;
	$GLOBALS["LengthLabel"] = $LengthLabel;
	$GLOBALS["WidthLabel"] = $WidthLabel;
	$GLOBALS["Print_Machine"] = $Print_Machine;
	$GLOBALS["Cut_Machine"] = $Cut_Machine;
	$GLOBALS["STT"] = $STT;
	$GLOBALS["RemarkTop"] = $RemarkTop;
	$GLOBALS["RemarkBot"] = $RemarkBot;
	$GLOBALS["QtyNeed"] = $QtyNeed;
	$GLOBALS["MaterialCode"] = $MaterialCode;
	$GLOBALS["InkNum"] = $InkNum;
	$GLOBALS["InkCode"] = $InkCode;
	$GLOBALS["JobJacket"] = $JobJacket;
	$GLOBALS["ItemCode"] = $ItemCode;
	$GLOBALS["RemarkNew"] = $RemarkNew;

	$GLOBALS["UEE"] = $UEE;

	$GLOBALS["Plan_Cut"] = $Plan_Cut;
	$GLOBALS["STT_Cut"] = $STT_Cut;
	

	$SOLINES = explode(",",$SOLine);
	/*
	$SOLINES = trim($SOLINES);
	$SOLINES = trim($SOLINES,",");
	*/
	$s = '';
	foreach($SOLINES as $key => $value){
		if($key%2!=0){
			$s.=$value."<br/>";
		}else{
			$s.=$value.", ";
			
		}
	}
	$s = trim($s,", ");
	if($Weight == "") $Weight = 0;
	$LengthLabel = trim($LengthLabel);
	$GLOBALS["SOLine"] = $s;
	$GLOBALS["CustomerItem"] = $CustomerItem;
	$GLOBALS["Weight"] = (int)$Weight * (int)$QtyNeed;
	$GLOBALS["ScrapRate"] = ($Qty * $LengthLabel)/(1000 * 0.914);
	$GLOBALS["ScrapRate"] = ($QtyNeed - $GLOBALS["ScrapRate"]) * 100/$GLOBALS["ScrapRate"];
	$GLOBALS["ScrapRate"] = $GLOBALS["ScrapRate"] == 0 ? "" : number_format($GLOBALS["ScrapRate"],1,",",".");
	$GLOBALS["Weight"] = $GLOBALS["Weight"] == 0 ? "" : number_format($GLOBALS["Weight"],3,",",".") . " KG";
	$GLOBALS["Creation_D"] = $Creation_D;

	// @TanDoan - 20201224: Tạm thời xử lý hiển thị trên tờ lệnh CRD - 1 và PD - 1.
		// $current = date('Y-m-d');
		// if ($current <= '2020-12-24' ) {
		// 	$CRD = date('Y-m-d', strtotime($CRD));
		// 	$CRD = subDate($CRD, -1);
		// 	$Promise = date('Y-m-d', strtotime($Promise));
		// 	$Promise = subDate($Promise, -1);
		// }
	
	$GLOBALS["Request"] = $CRD;//$Request;

	$GLOBALS["Qty"] = $Qty;
	$GLOBALS["Promise"] = $Promise;
	$GLOBALS["NumSize"] = $NumSize;
	$GLOBALS["PrintMethod"] = $PrintMethod;
	$GLOBALS["Drying"] = $Drying;
	$GLOBALS["Temp"] = $Temp;
	$GLOBALS["Order_Type"] = $Order_Type;
	$GLOBALS["TotalLine"] = $TotalSOLine;
	$GLOBALS["TableSize1"] = $TableSize1;
	$GLOBALS["PACKING_INSTRUCTIONS"] = $PACKING_INSTRUCTIONS;
	$GLOBALS["BILL_TO_NUMBER"] = $BILL_TO_NUMBER;
	
	$GLOBALS["RemarkWH"] = "";
	$GLOBALS["NOTENKXS"] = false;
	$GLOBALS["NOTENKXS2"] = false;

	/* 
		@TanDoan - 20220216: email: THÔNG TIN YÊU CẦU ĐẶC BIỆT CỦA KHÁCH HÀNG - PFL ROTARY
		Bỏ các điều kiện cũ
	*/

		// $ItemPass = ["CB475005A","CB475806B","CB475007B","CB476337B"];
		// $RBOPass = ["H amp;M HENNES  amp; MAURITZ GBC AB","H &M HENNES & MAURITZ GBC AB","NORTH FACE","LT APPAREL"];
		$ItemPass = [""];
		$RBOPass = [""];

	/* =========================================== */

	$GLOBALS["RemarkQC"] = "";
	$GLOBALS["remarkSKU"] = '';
	$checkFOD = checkFOD($JobJacket ,$ItemCode);
	
	// @TanDoan: ngày mai 20210205 áp dụng
		if ($checkFOD == false ) {
			$checkFOD = checkNewItemFOD($JobJacket ,$ItemCode);
		}

	// Lấy thông số Printing Speed
		$GLOBALS["Printing_Speed"] = getPrintSpeed($ItemCode);
	
	$GLOBALS["FOD"] = ($checkFOD==true) ? "<div style='color:red;font-size:30px;font-weight:bold;border:2px solid blue;heigth:45px;padding:15px 0px 15px 0; text-align:center;'>FOD</div>" : "";

	$CRDCheck = abs(strtotime($GLOBALS['Request'] ) - strtotime($GLOBALS['Plan_YMD']) );  
	$YCheck = floor($CRDCheck / (365*60*60*24));  
	$MCheck = floor(($CRDCheck - $YCheck * 365*60*60*24) / (30*60*60*24));  
	$dayCheck = floor(($CRDCheck - $YCheck * 365*60*60*24 - $MCheck*30*60*60*24)/ (60*60*24));
	$GLOBALS["CRD0"] = ($dayCheck == 0 ) ? "<div style='color:red;font-size:30px;font-weight:bold;border:2px solid blue;heigth:45px;padding:15px 0px 15px 0; text-align:center;'>CRD 0</div>" : '';

	if(in_array($ItemCode,$ItemPass)) $GLOBALS["RemarkQC"] = "<div style='position: absolute;border:5px solid red; padding:10px 0 10px 0; bottom: 0; font-size: 12pt; font-weight:bold;text-align:center;width:90%'>QC Kiểm Tra 30%</div>";
	if(strtoupper($ShipToCustomer) == "YSS GARMENT COMPANY LIMITED") $GLOBALS["RemarkQC"] = "<div style='position: absolute;border:5px solid red; padding:10px 0 10px 0; bottom: 0; font-size: 12pt; font-weight:bold;text-align:center;width:90%'>QC Kiểm Tra 10%</div>";
	else if(strpos(strtoupper($ShipToCustomer), "SAKURAI VIETNAM COMPANY LIMITED") !== false ) {
		
		// @TanDoan - 20220214 - Điều chỉnh remark NHẬP KHO THEO SIZE vào vị trí RemarkWH.
		$GLOBALS["RemarkWH"] = "<div style='background:black;color:white;font-size:20pt;width:100%;height:100%;text-align:center;padding:5px 0px 5px 0px'>NHẬP KHO THEO SIZE</div>";	

	} else if(in_array($RBO,$RBOPass)) $GLOBALS["RemarkQC"] = "<div style='position: absolute;border:5px solid red; padding:10px 0 10px 0; bottom: 0; font-size: 12pt; font-weight:bold;text-align:center;width:90%'>QC Kiểm Tra 10%</div>";
	else if(strpos($RBO, "MAURITZ GBC AB") !== false) $GLOBALS["RemarkQC"] = "<div style='position: absolute;border:5px solid red; padding:10px 0 10px 0; bottom: 0; font-size: 12pt; font-weight:bold;text-align:center;width:90%'>QC Kiểm Tra 10%</div>";
	


	if(strpos($RBO,"H&M") !== false || strpos($RBO,"H &M") !== false || strpos($RBO,"H & M") !== false || strpos($RBO,"H& M") !== false )
	{
		$GLOBALS["RemarkWH"] = "<div style='background:black;color:white;font-size:20pt;width:100%;height:100%;text-align:center;padding:5px 0px 5px 0px'>NHẬP KHO THEO SIZE</div>";	
		// @tandoan 20200903: Thêm điều kiện RBO là H&M thì remark: MỖI SKU MỘT BỊCH
		$GLOBALS["remarkSKU"] = '<br /> MỖI SKU MỘT BỊCH';
	} else if(strpos($RBO,"HENNES") !== false || strpos($RBO,"THE WILLIAM CARTER") !== false || strpos($RBO,"OSHKOSH") !== false || strpos($RBO,"PRIMARK") !== false || strpos($RBO,"GEORGE") !== false)
	{
		$GLOBALS["RemarkWH"] = "<div style='background:black;color:white;font-size:20pt;width:100%;height:100%;text-align:center;padding:5px 0px 5px 0px'>NHẬP KHO THEO SIZE</div>";
	} else if(strpos(strtoupper($RBO),"FASHION GARMENT") !== false || strpos(strtoupper($RBO),"CARMEL") !== false)
	{
		$GLOBALS["RemarkWH"] = "<div style='background:black;color:white;font-size:20pt;width:100%;height:100%;text-align:center;padding:5px 0px 5px 0px'>NHẬP KHO THEO SIZE</div>";
	}  else if(strpos(strtoupper($RBO),"VICTORIAS SECRET") !== false ) {
		// @Tandoan - 20200915: RBO = VICTORIAS SECRET && SHIP TO CRYSTAL MARTIN
		// @TanDoan - 20220214: VICTORIAS áp dụng cho tất cả ship to (Yêu cầu từ Nhung.LePham). Lấy theo email: "THÔNG TIN YÊU CẦU ĐẶC BIỆT CỦA KHÁCH HÀNG - PFL ROTARY"
		$GLOBALS["RemarkWH"] = "<div style='background:black;color:white;font-size:20pt;width:100%;height:100%;text-align:center;padding:5px 0px 5px 0px'>NHẬP KHO THEO SIZE</div>";
		
	} else if (strpos($RBO,"TARGET") !== false ) {
		$GLOBALS["RemarkWH"] = "<div style='background:black;color:white;font-size:20pt;width:100%;height:100%;text-align:center;padding:5px 0px 5px 0px'>NHẬP KHO THEO SIZE</div>";
	} else if (strpos($RBO,"MUJI") !== false ) {
		// @TanDoan - 20220214: Lấy theo email: "THÔNG TIN YÊU CẦU ĐẶC BIỆT CỦA KHÁCH HÀNG - PFL ROTARY"
		$GLOBALS["RemarkWH"] = "<div style='background:black;color:white;font-size:20pt;width:100%;height:100%;text-align:center;padding:5px 0px 5px 0px'>NHẬP KHO THEO SIZE</div>";
	}
	

	if(strpos(strtoupper($ShipToCustomer),"SON HA CO. LTD") !== false )
	{
		$GLOBALS["RemarkWH"] = "<div style='background:black;color:white;font-size:20pt;width:100%;height:100%;text-align:center;padding:5px 0px 5px 0px'>NHẬP KHO THEO SIZE</div>";
		$GLOBALS["RemarkNew"] .= "Bó cọc hoặc bỏ bịch 50 pcs <br>";
	} else if(strpos(strtoupper($ShipToCustomer),"NIEN HSING (NINH BINH) GARMENT CO.,LTD") !== false)
	{
		$GLOBALS["RemarkNew"] .= "ĐƠN HÀNG YÊU CẦU BÓ/ CỘT SỐ LƯỢNG LẺ <br>";
		$GLOBALS["RemarkNew"] .=  ($ItemCode == 'P580934A' ) ? " - Xếp 500pcs/bịch" : "";
		
	} else if(strpos(strtoupper($ShipToCustomer),"MINH ANH - DO LUONG GARMENT JOINT STOCK COMPANY") !== false)
	{
		$GLOBALS["RemarkNew"] .= "Chú ý đơn hàng không đóng FOC <br>";

	} else if(strpos(strtoupper($ShipToCustomer),"FASHION GARMENT 2") !== false ) 
	{
		// @TanDoan - 20220214: Lấy theo email: "THÔNG TIN YÊU CẦU ĐẶC BIỆT CỦA KHÁCH HÀNG - PFL ROTARY"
		$GLOBALS["RemarkNew"] .= "Hộp chẵn 500 <br> Bó cọc 50 pcs/bó (kích thước dưới 35 bỏ bịch) <br>";
	}
	//@TanDoan: add "KIM" to print: Aug-21, 2019
	//  <td style="background: #de943e; color: white;font-weight: bold; font-size:16pt; text-align:center"></td>
	// 	<td></td>


	if(strpos($BillToCustomer,"SAE-A TRADING") !== false )
	{
		$GLOBALS["RemarkWH"] = "<div style='background:black;color:white;font-size:20pt;width:100%;height:100%;text-align:center;padding:5px 0px 5px 0px'>NHẬP KHO THEO SIZE</div>";
	}

	if( (strpos($BillToCustomer, 'HANSOLL') !== false) || (strpos($BillToCustomer, 'SAE-A') !== false) || (strpos($BillToCustomer, 'HANSAE') !== false) || 
	(strpos($BillToCustomer, 'YAKJIN') !== false)  || (strpos($BillToCustomer, 'POONG IN') !== false) || (strpos($BillToCustomer, 'NOBLAND') !== false)  )
	{
		$GLOBALS["BillToCustomer_KIM"] = "KIM/ƯU TIÊN 2";
	}
	else $GLOBALS["BillToCustomer_KIM"] = "";

	//@tandoan: 20200831 - Remark: MỖI SKU MỘT BỊCH.  điều kiện là: Ship to =  CONG TY TNHH MAY MAC LEADING STAR VIET NAM VÀ  ITEM là 1 trong 3 item:   CB478481E, CB550377A, CB550381A
	$itemShiptoCheck = array('CB478481E', 'CB550377A', 'CB550381A' );

	if (strpos($ShipToCustomer, 'CONG TY TNHH MAY MAC LEADING STAR VIET NAM') !==false ) {
		foreach ($itemShiptoCheck as $itemCheck ) {
			if (strpos(strtoupper($ItemCode), $itemCheck) !==false ) {
				$GLOBALS["remarkSKU"] = '<br> MỖI SKU MỘT BỊCH';
			}
		}
	}

	if( (strpos($BillToCustomer, 'KANMAX ENTERPRISES LIMITED') !== false &&	strpos($ShipToCustomer, 'I Apparel Int’l Group Pte Ltd') !== false) ||
		(strpos($BillToCustomer, 'KANMAX ENTERPRISES LIMITED') !== false &&	strpos($ShipToCustomer, 'IK APPAREL CO. LTD') !== false) ||
		(strpos($BillToCustomer, 'EPOCH LAY ENTERPRISE., LTD') !== false && strpos($ShipToCustomer, 'EPOCH LAY ENTERPRISE., LTD') !== false)
	) {
		$GLOBALS["RemarkWH"] = "<div style='background:black;color:white;font-size:20pt;width:100%;height:100%;text-align:center;padding:5px 0px 5px 0px'>NHẬP KHO THEO SIZE</div>";
		$GLOBALS["RemarkNew"] .= 'ĐÓNG GÓI 200 PCS/ BỊCH HOẶC GÓI GIẤY 200 PCS/ BÓ <br>';
		$GLOBALS["RemarkNew"] .= ($GLOBALS["ItemCode"] == 'P534490A') ? 'ĐÓNG GÓI 500PCS/ HỘP' : '';
	}

	// @TanDoan - 20220216: email: THÔNG TIN YÊU CẦU ĐẶC BIỆT CỦA KHÁCH HÀNG - PFL ROTARY
	// Nhóm chat 20220216: Nhung.LePham, Khoa.D.Pham yêu cầu làm theo điều kiện mới bỏ cái cũ

	if ( (strpos($ShipToCustomer, 'CHUTEX INTERNATIONAL (LONG AN) COMPANY., LTD') !== false) || (strpos($ShipToCustomer, 'CHUTEX INTERNATIONAL CO,LTD') !== false ) ) {
		$GLOBALS["RemarkWH"] = "<div style='background:black;color:white;font-size:20pt;width:100%;height:100%;text-align:center;padding:5px 0px 5px 0px'>NHẬP KHO THEO SIZE</div>";
	}



	function InsertPage()
	{
		echo '<div class="page">
			<table style="width: 100%;border:1px #de943e solid;font-size:11pt">
			<tr>
					<td style="width:17%"></td>
					<td style="width:10%"></td>
					<td style="width:12%"></td>
					<td style="width:11%"></td>
					<td style="width:14%"></td>
					<td style="width:12%"></td>
					<td style="width:12%"></td>
					<td style="width:12%"></td>
				</tr>
				<tr style="height:30px">
					<td colspan=1 class="BorderAll" style="background: yellow; text-align:center">' . $GLOBALS["PersonPIC"] . '</td>
					<td style="font-weight: bold; font-size:20pt; text-align:center; word-spacing:-2px;" colspan=4>ĐƠN HÀNG VÀ CHI TIẾT MẪU</td>
					<td colspan=3 style="position:relative;text-align:right;">
						<div style="min-width:295px;">
							<a class="kh-in-cut">KH IN: ' . $GLOBALS["Plan_YMD"] . '</a>
							<a class="kh-in-cut">KH Cắt: ' . $GLOBALS["Plan_Cut"] . '</a>
						</div>
					</td>
				</tr>
				<tr style="height:45px">
					<td style="background:#B8CCE4; font-weight: bold; font-size:16pt; text-align:center" colspan=2>' .$GLOBALS["RBO"]. '</td>
					<td colspan=4><img style="width:70%;height:100%" src=\'' .'Images//'.$GLOBALS["JobJacket"]. '.png\'></td>
					<td colspan=1>'.$GLOBALS["CRD0"].'</td>
					<td colspan=1>'.$GLOBALS["FOD"].'</td>
				</tr>
				<tr style="height:35px">
					<td colspan=2> Ngày in lệnh: ' .date("d-m-Y",strtotime($GLOBALS["ReceivingDate"])). '</td>
									
					<td class="BorderAll" style="font-size:14pt;text-align:center;color:red;background:yellow">TEETH</td>
					<td class="BorderAll" style="font-size:14pt;text-align:center;color:red;background:yellow">Độ Dài In</td>
					<td class="BorderAll" style="font-size:14pt;text-align:center;color:red;background:yellow">Khổ vật tư</td>
					<td colspan=3 style="text-align:center; font-size: 15pt; background:yellow; border: 1px #de943e solid">Ship To: ' .$GLOBALS["ShipToCustomer"] . '</td>

				</tr>
				<tr style="height:35px">
				<td colspan=2 style="background: yellow; color: red;font-weight: bold; font-size:20pt; text-align:center">' . $GLOBALS["BillToCustomer_KIM"] .' ' . $GLOBALS["MLA"] . '</td>	
					
					<td class="BorderAll" style="font-size:16pt;text-align:center;color:red;">' .$GLOBALS["Teeth"] . '</td>
					<td class="BorderAll" style="font-size:16pt;text-align:center;color:red;">' .$GLOBALS["LengthLabel"] . '</td>
					<td class="BorderAll" style="font-size:16pt;text-align:center;color:red;">' .$GLOBALS["WidthLabel"] . '</td>
					<td class="BorderAll" colspan=1 style="font-size:14pt;text-align:left;">Máy In: </td>
					<td class="BorderAll" colspan=1 style="font-size:20pt;text-align:center;">' .$GLOBALS["Print_Machine"] . '</td>
					<td class="BorderAll" rowspan=1 style="font-size:14pt;text-align:center;vertical-align: top; word-spacing:-2px;">STT IN: '. $GLOBALS["STT"] .'</td>
				</tr>
				<tr style="height:20px">
					<td class="BorderAll" rowspan=4 style="background: gray;text-align:center">Trace Ability</td>
					<td class="BorderAll" style="background: gray;"></td>
					<td class="BorderAll" style="background: #D8D8D8">Màu</td>
					<td class="BorderAll" style="background: #D8D8D8">Lot #</td>
					<td class="BorderAll" style="background: #D8D8D8">Ghi Chú</td>
					<td class="BorderAll" colspan=1 style="font-size:14pt;text-align:left;">Máy Cắt:</td>
					<td class="BorderAll" colspan=1 style="font-size:20pt;text-align:center;">' .$GLOBALS["Cut_Machine"] . '</td>
					<td class="BorderAll" rowspan=1 style="font-size:14pt;text-align:center;vertical-align: top; word-spacing:-5px;">STT Cắt: ' .$GLOBALS["STT_Cut"] . '</td>
				</tr>
				<tr style="height:20px">
					<td class="BorderAll" style="background: #D8D8D8">Vải</td>
					<td class="BorderAll"></td>
					<td class="BorderAll"></td>
					<td class="BorderAll"></td>
					<td class="BorderAll" colspan=3 rowspan=6 style="font-size:14pt;text-align:left;vertical-align:top;padding: 2px">
						GHI CHÚ: <br/>- ' .$GLOBALS["RemarkTop"] . "<br/> - " . $GLOBALS["RemarkBot"] . "<br/>" . '</td>
				</tr>
				<tr style="height:20px">
					<td class="BorderAll" style="background: #D8D8D8">Giấy</td>
					<td class="BorderAll"></td>
					<td class="BorderAll"></td>
					<td class="BorderAll"></td>
				</tr>
				<tr style="height:20px">
					<td class="BorderAll" style="background: #D8D8D8">Mực</td>
					<td class="BorderAll"></td>
					<td class="BorderAll"></td>
					<td class="BorderAll"></td>
				</tr>
				<tr style="height:30px">
					<td class="BorderAll" colspan=5 style="font-weight:bold">Người kiểm vật tư:</td>
				</tr>
				<tr style="height:30px">
					<td class="BorderAll" colspan=5 style="font-weight:bold">Người kiểm mực:</td>
				</tr>
				<tr style="height:30px">
					<td colspan=2 style="font-weight:bold">Chiều dài Yard :
					<a style="font-weight:bold;font-size:16pt;">' .number_format($GLOBALS["QtyNeed"],0,",",".") . '</a> <a style="font-size: 12pt">Yards</a></td>
					<td style="font-weight:bold; font-size:13pt; " class="">(' .$GLOBALS["Weight"] . ')</td>
					<td style="font-weight:bold;background:#B8CCE4;text-align:center" colspan=2 rowspan=2>' .$GLOBALS["MaterialCode"] . '</td>
				</tr>
				<tr style="height:20px">
					<td colspan=2 style="font-weight:bold" class="BorderAll">
					<a style="font-weight:bold;font-size:12pt;">Tỷ lệ Scrap: ' .$GLOBALS["ScrapRate"] . '%</a></td>
					<td style="font-weight:bold; font-size:10pt; background:yellow; text-align: center;background:#B8CCE4" class="BorderAll">Chất Liệu Vải</td>
				</tr>
				<tr class="BorderAll" style="height:30px">
					<td style="font-weight:bold; text-align:center;" class="BorderAll">UEE(m): ' . $GLOBALS["UEE"] . '(m)</td>
					<td style="font-weight:bold">Mực: ' .$GLOBALS["InkNum"] . '</td>
					<td colspan=3 style="font-weight:bold;background:#B8CCE4">' .$GLOBALS["InkCode"] . '</td>
				</tr>
				<tr style="height:40px">
					<td class="BorderAll" style="padding-left: 5px">Lệnh Sản Xuất:</td>
					<td class="BorderAll" colspan=4 style="font-size:18pt;background: lime; text-align:center; padding:5px; font-weight: bold">' .$GLOBALS["JobJacket"] . '</td>
					<td class="BorderAll" style="padding-left: 5px">ITEM:</td>
					<td class="BorderAll" colspan=2 style="text-align:center;font-weight:bold;font-size:15pt">' .$GLOBALS["ItemCode"] . '</td>
				</tr>
				<tr style="height:40px">
					<td class="BorderAll" style="padding-left: 5px">SO#:</td>';
						if($GLOBALS["NumCare"] == 1)
						{
							echo '<td class="BorderAll" colspan=4 style="font-size:20pt;background: yellow; text-align:left; padding:5px; font-weight: bold">' .$GLOBALS["SOLine"] .'</td>';
						} else
						{
							echo '<td class="BorderAll" colspan=4 style="font-size:15pt;background: yellow; text-align:left; padding:5px; font-weight: bold">' .$GLOBALS["SOLine"] .'</td>';
						}
				echo 	'<td class="BorderAll" style="padding-left: 5px">Customer Item:</td>
					<td class="BorderAll" colspan=2 style="text-align:center;font-weight:bold;font-size:10pt">' .$GLOBALS["CustomerItem"] . '</td>
				</tr>
				<tr style="height:35px">
					<td style="padding-left: 5px">Ngày Order:</td>
					<td colspan=3 style="font-weight: bold; font-size: 12pt; text-align: center; border: 1px black solid">' .date("d-m-Y",strtotime($GLOBALS["Creation_D"])) . '</td>';
						if($GLOBALS["Multi"] > 4) 
						{
							echo '<td colspan=2 style="font-size:14pt; border: 1px black solid; text-align: center">Nhiều Line</td>';
						} else
						{
							echo '<td colspan=2></td>';
						}
					
				echo '	<td colspan=2 style="font-size:15pt">Chiều dài (m): ' . number_format($GLOBALS["QtyNeed"]*0.914,0,",",".") . 'M</td>
				</tr>
				<tr style="height:35px">
					<td style="padding-left: 5px">Ngày Request:</td>
					<td colspan=3 style="background: #de943e; color: black; font-weight: bold; font-size: 20pt; text-align: center">' .date("d-m-Y",strtotime($GLOBALS["Request"])) . '</td>
					<td></td>
					<td style="padding-left: 5px">Số lượng:</td>
					<td colspan=2 style="background: #de943e; color: black; font-weight: bold; font-size: 20pt; text-align: center">' .number_format($GLOBALS["Qty"],0,",",".") . ' pcs</td>
				</tr>
				<tr style="height:35px">
					<td style="padding-left: 5px">Ngày Promise:</td>
					<td colspan=3 style="font-weight: bold; font-size: 16pt; text-align: center;">' .date("d-m-Y",strtotime($GLOBALS["Promise"])) . '</td>
					<td></td>
					<td style="padding-left: 5px">Số Size:</td>
					<td colspan=2 style="font-weight: bold; font-size: 16pt; text-align: center; background:Orange">' .$GLOBALS["NumSize"] . '</td>
				</tr>
				<tr style="height:30px">
					<td class="BorderAll">Phương Pháp In:</td>
					<td class="BorderAll" colspan=3 style="background: yellow; font-weight: bold; font-size: 16pt; text-align: center; padding: 2px">' .$GLOBALS["PrintMethod"] . '</td>';
					if($GLOBALS["Drying"] != "" && $GLOBALS["Drying"] != "0")
						{
							echo '<td  style="padding-left:10px; border-top: 1px #de943e solid">Sấy:</td><td style="border-top: 1px #de943e solid">' . $GLOBALS["Drying"] . ' Phút</td>';
						} else
						{
							echo '<td></td><td style="border-top: 1px #de943e solid"></td>';
						}
					
				echo	'<td class="BorderAll" colspan=2 rowspan=5 style="font-size:16;text-align:left; vertical-align:top; position: relative;">MỘC PASS QC: ' . $GLOBALS["RemarkQC"]  . '</td>
				</tr>
				<tr style="height:30px">
					<td class="BorderAll">Phương Pháp cắt:</td>
					<td class="BorderAll" colspan=3 style="background: yellow; font-weight: bold; font-size: 16pt; text-align: center; padding: 2px">' .$GLOBALS["CutMethod"] . '</td>';
					if($GLOBALS["Temp"] != "" && $GLOBALS["Temp"] != "0")
					{
						echo '<td style="padding:10px">Độ nóng</td><td>'. $GLOBALS["Temp"] .' Độ C</td>';
					} else
					{
						echo '<td></td><td></td>';
					}
				
				echo '</tr>
				<tr style="height:30px">
					<td class="BorderAll">Phương Pháp gấp:</td>
					<td class="BorderAll" colspan=3 style="background: yellow; font-weight: bold; font-size: 16pt; text-align: center; padding: 2px">' .$GLOBALS["FoldMethod"] . '</td>';
					if($GLOBALS["Printing_Speed"] != "" && $GLOBALS["Printing_Speed"] != "0")
					{
						echo '<td style="padding:10px;font-size:15px;">Tốc độ in</td><td>'. $GLOBALS["Printing_Speed"] .' m/p</td>';
					} else
					{
						echo '<td></td><td></td>';
					}
					
				echo '</tr>
				<tr style="height:35px">
					<td colspan=6>IN: <a style="font-size:8pt">___________________________________________________ </a> QC IN:<a style="font-size:8pt">___________________________________________</a></td>
				</tr>
				<tr style="height:35px; border-bottom: 1px black solid">
					<td colspan=6>CẮT: <a style="font-size:8pt">_________________________________________________ </a> QC CẮT:<a style="font-size:8pt">________________________________________</a></td>
				</tr>
				<tr style="height:100px">
					<td colspan=6 style="vertical-align: top;">Ghi Chú <a style=\'font-size:16pt;font-weight:bold;background:yellow;\'>' . $GLOBALS["RemarkNew"] . "<br>" . $GLOBALS["remarkSKU"] . specialItemRemark($GLOBALS["ItemCode"]) . remarkShiptoRBO($GLOBALS["RBO"], $GLOBALS["ShipToCustomer"], $GLOBALS["BILL_TO_NUMBER"] ) . remarkRBO($GLOBALS["RBO"], $GLOBALS["ItemCode"]) .  '</a></td>
					<td colspan=2 style="vertical-align: top;">' . $GLOBALS["RemarkWH"] . '</td>
				</tr>
				
				<tr style="height:60px">';
						if(strpos($GLOBALS["Order_Type"],"SAM") !== false)
						{
							echo '  <td colspan=6 style="font-size:12;text-align:left; vertical-align:top; border-top: 1px #de943e solid; border-bottom: 1px #de943e solid">Packing Instruction: ' . $GLOBALS["PACKING_INSTRUCTIONS"] . '</td>
									<td colspan=2 style="background:#de943e; color:white; font-weight:bold; font-size:16pt; text-align: center">Đơn Mẫu</td>';
						} else
						{
							echo '  <td colspan=8 style="font-size:12;text-align:left; vertical-align:top; border-top: 1px #de943e solid; border-bottom: 1px #de943e solid">Packing Instruction: ' . $GLOBALS["PACKING_INSTRUCTIONS"] . '</td>
									';
						}
					
				echo '</tr>
				<tr>
					<td colspan=8 style="width:100%;height:inherit">
						<div style="width:100%; height:100%;">
							<div style="width:100%;height:100%;">';
								if($GLOBALS["TotalLine"] < 7) 
										echo $GLOBALS["TableSize1"]; 
										else echo "Số size nhiều quá nên in trang sau nhé.";
								
							echo '</div>
							
						</div>
					</td>
				</tr>
			</table>
		</div>';
		if($GLOBALS["TotalLine"] > 6)
		{
			echo '<div class="page" style="width:100%;height:100%;">' . $GLOBALS["TableSize1"] . '</div>';
		}  
	}


	?>
		<body style="font-weight:bold">
		<?php 
			echo InsertPage();
		?>
		</body>
	</html>