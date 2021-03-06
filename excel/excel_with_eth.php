<?php
include_once("./Classes/PHPExcel.php");
include_once("./db_connect.php");

$objPHPExcel = new PHPExcel();

$sql = "select * from g5_member order by mb_no asc";

$result = mysqli_query($conn,$sql);

$arr = array();

for($i=0; $i < $row=mysqli_fetch_array($result); $i++){
  array_push($arr, array("id" => $row['mb_id'], "eth_address" => $row['first_name'], "mail" => $row['mb_email'], "recom"=> $row['mb_recommend'],
  						"mb_brecommend"=>$row['mb_brecommend'],
						"mb_balance"=>$row['mb_balance']));
}


$objPHPExcel -> setActiveSheetIndex(0)

-> setCellValue("A1", "NO.")

-> setCellValue("B1", "회원 아이디")

-> setCellValue("C1", "지갑 주소")

-> setCellValue("D1", "이메일")

-> setCellValue("E1", "추천인")

-> setCellValue("F1", "후원인")

-> setCellValue("G1", "BENEFIT( 총 발생수당 )");

$count = 1;

foreach($arr as $key => $val) {

	$num = 2 + $key;

	$objPHPExcel -> setActiveSheetIndex(0)


	

	-> setCellValue(sprintf("A%s", $num), $key+1)

	-> setCellValue(sprintf("B%s", $num), $val['id'])

	-> setCellValueExplicit(sprintf("C%s", $num), $val['eth_address'])

	-> setCellValue(sprintf("D%s", $num), $val['mail'])

	-> setCellValue(sprintf("E%s", $num), $val['recom'])
	  
	-> setCellValue(sprintf("F%s", $num), $val['mb_brecommend'])

	-> setCellValue(sprintf("G%s", $num), number_format($val['mb_balance']*1,2));

	$count++;

}



// 가로 넓이 조정

$objPHPExcel -> getActiveSheet() -> getColumnDimension("A") -> setWidth(6);

$objPHPExcel -> getActiveSheet() -> getColumnDimension("B") -> setWidth(20);

$objPHPExcel -> getActiveSheet() -> getColumnDimension("C") -> setWidth(55);

$objPHPExcel -> getActiveSheet() -> getColumnDimension("D") -> setWidth(35);

$objPHPExcel -> getActiveSheet() -> getColumnDimension("E") -> setWidth(25);

$objPHPExcel -> getActiveSheet() -> getColumnDimension("F") -> setWidth(25);

$objPHPExcel -> getActiveSheet() -> getColumnDimension("G") -> setWidth(30);



// 전체 세로 높이 조정

$objPHPExcel -> getActiveSheet() -> getDefaultRowDimension() -> setRowHeight(15);



// 전체 가운데 정렬

$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:G%s", $count)) -> getAlignment()

-> setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



// 전체 테두리 지정

$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A1:G%s", $count)) -> getBorders() -> getAllBorders()

-> setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);



// 타이틀 부분

$objPHPExcel -> getActiveSheet() -> getStyle("A1:G1") -> getFont() -> setBold(true);

$objPHPExcel -> getActiveSheet() -> getStyle("A1:G1") -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID)

-> getStartColor() -> setRGB("CECBCA");



// 내용 지정

$objPHPExcel -> getActiveSheet() -> getStyle(sprintf("A2:G%s", $count)) -> getFill()

-> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("F4F4F4");



// 시트 네임

$objPHPExcel -> getActiveSheet() -> setTitle("USER_INFOMATION");



// 첫번째 시트(Sheet)로 열리게 설정

$objPHPExcel -> setActiveSheetIndex(0);



// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.

$filename = iconv("UTF-8", "EUC-KR", "info");



// 브라우저로 엑셀파일을 리다이렉션

header("Content-Type:application/vnd.ms-excel");

header("Content-Disposition: attachment;filename=".$filename.".xls");

header("Cache-Control:max-age=0");



$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");

$objWriter -> save("php://output");

?>
