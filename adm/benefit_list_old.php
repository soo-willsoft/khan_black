<?php
$sub_menu = "200300";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');




$benefit = "SELECT allowance_name FROM soodang_pay WHERE (1) GROUP BY allowance_name ";
$rrr = sql_query($benefit);
//$html= "<br/>";
$allowcnt=0;
for ($i=0; $allowance_name=sql_fetch_array($rrr); $i++) {   
	$nnn="allowance_chk".$i;
	$html.= "<input type='checkbox' name='".$nnn."' id='".$nnn."'";
	
	if($$nnn!=''){
		$html.=" checked='true' ";
	}		

	$html.=" value='".$allowance_name['allowance_name']."'>".$allowance_name['allowance_name']."&nbsp;&nbsp;";


	if(${"allowance_chk".$i}!=''){
		if($allowcnt==0){
			$sql_search .= " and ( (allowance_name='".${"allowance_chk".$i}."')";
		}else{
			$sql_search .= "  or ( allowance_name='".${"allowance_chk".$i}."' )";
		}

		
			$qstr.='&'.$nnn.'='.$allowance_name['allowance_name'];
		
		$allowcnt++;

	}

}

if ($allowcnt>0) $sql_search .= ")";


$token = get_token();

$fr_date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $fr_date);
$to_date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $to_date);

$qstr.='&fr_date='.$fr_date.'&to_date='.$to_date.'&chkc='.$chkc.'&chkm='.$chkm.'&chkr='.$chkr.'&chkd='.$chkd.'&chke='.$chke.'&chki='.$chki;
$qstr.='&diviradio='.$diviradio.'&r='.$r;
$qstr.='&stx='.$stx.'&sfl='.$sfl;
$qstr.='&aaa='.$aaa;



$sql_common = " from soodang_pay where (1) ";


if(!$fr_date){
	$fr_date=date("Y-m-d");
	$to_date=$fr_date;
	
}








if(($allowance_name) ){
	$sql_search .= " and (";
		if($chkc){
		$sql_search .= " allowance_name='".$allowance_name."'";
		}
 $sql_search .= " )";
 
}/*else if($dv_gubun){
	 $sql_search .= " and dv_gubun='".$dv_gubun."'";
}
*/

if($_GET[start_dt]){
	$sql_search .= " and day >= '".$_GET[start_dt]."'";
	$qstr .= "&start_dt=".$_GET[start_dt];
}
if($_GET[end_dt]){
	$sql_search .= " and day <= '".$_GET[end_dt]."'";
	$qstr .= "&end_dt=".$_GET[end_dt];
}


if ($stx) {
    $sql_search .= " and ( ";
	if(($sfl=='mb_id') || ($sfl=='mb_id')){
            $sql_search .= " ({$sfl} = '{$stx}') ";
          
	}else if(($sfl=='day')){
            $sql_search .= " ({$sfl} = '{$stx}') ";
	}

	else{
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
          
    }
    $sql_search .= " ) ";
}

$sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search}
            {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // ?????? ????????? ??????
if ($page < 1) $page = 1; // ???????????? ????????? ??? ????????? (1 ?????????)
$from_record = ($page - 1) * $rows; // ?????? ?????? ??????



$sql_order='order by day desc';

$sql = " select *
            {$sql_common}
            {$sql_search}
            {$sql_order}
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);
echo $sql;

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">????????????</a>';

$g5['title'] = '???????????? ??? ???????????????';
include_once ('./admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$colspan = 16;


 ?>   
 


<div class="local_ov01 local_ov">

	<?
	/*
    if (isset($mb['mb_id']) && $mb['mb_id']) {
    	  $row2 = sql_fetch(" select sum(dv_money) as sum_dividend_money ,  sum(dv_tax) as sum_dividend_tax,  sum(dv_money-dv_tax) as sum_dividend from dividend where dv_paid<>'R' and dv_paid<>'S' and mb_id='{$mb['mb_id']}' {$sql_search} ");
         echo '&nbsp;?????? ??????:  ??????('.number_format($row2['sum_dividend_money']).'???)  / ??????('.number_format($row2['sum_dividend_tax']).'???)  / ??? ?????????('.number_format($row2['sum_dividend']).'???)';
    } else {
        $row2 = sql_fetch(" select sum(dv_money) as sum_dividend_money,  sum(dv_tax) as sum_dividend_tax,  sum(dv_money-dv_tax) as sum_dividend from dividend where dv_paid<>'R' and dv_paid<>'S' {$sql_search}");
        
        echo '&nbsp;?????? ??????:  ??????('.number_format($row2['sum_dividend_money']).'???)  / ??????('.number_format($row2['sum_dividend_tax']).'???)  / ??? ?????????('.number_format($row2['sum_dividend']).'???)';
    }*/



	$ym=date('Y-01-01');

    ?>

	<!--?????? ????????????
		<input type="text" id="noo_start_day"  name="noo_start_day" value="<?=$ym?>" class="frm_input" size="12" maxlength="10"> &nbsp;|&nbsp;
	-->
		???????????? ????????????
       <!-- <input type="text" name="fr_date" value="<?php if($fr_date){echo $fr_date; }else{echo date("Ymd");} ?>" id="fr_date" required class="required frm_input" size="13" maxlength="10">
        ~-->
        <label for="to_date" class="sound_only">?????? ?????????</label>
        <input type="text" name="to_date" value="<?php if($to_date){echo $to_date; }else{echo date("Ymd");} ?>" id="to_date" required class="required frm_input" size="13" maxlength="10"> 
		&nbsp;
			<!--<input type="checkbox" name="save_noo_mon" id="save_noo_mon" value="1">????????????+????????????--> 
			( PV<input type="radio" name="price" id="pv" value='pv' checked='true'><!--&nbsp;|&nbsp; BV<input type="radio" name="price" id="bv" value='bv' >&nbsp;|&nbsp;?????????<input type="radio" name="price" id="receipt" value='receipt'>&nbsp; -->)&nbsp;&nbsp;
            <!--<input type="checkbox" name="save_noo_mon" id="save_benefit" value="1">????????? ????????? ???????????????&nbsp;&nbsp;&nbsp; -->



	<input type="submit" name="act_button" value="?????? ?????? ??????"  class="frm_input" onclick="go_calc(0);">
	<input type="submit" name="act_button" value="????????? ??????"  class="frm_input" onclick="go_calc(1);">
	<input type="submit" name="act_button" value="?????? ??????(?????? ?????????)"  class="frm_input" onclick="go_calc(2);">

	<br>??????????????????- ??????????????? ?????? ????????? ?????? ???????????? ????????? ????????? ?????????.-->
	&nbsp;<!--<input type="checkbox" name="benefit_yn_chk" id="sales_yn_chk" >?????????????????? ??????(?????????????????? ??????) --> &nbsp;&nbsp;&nbsp; &nbsp;<!--<input type="checkbox" name="benefit_yn_chk" id="benefit_yn_chk"  >???????????? ??????(????????? ????????? ??????)  &nbsp;-->
	&nbsp;<!--<input type="checkbox" name="iwol_yn_chk" id="iwol_yn_chk"  >???????????? ??????(????????????????????? ??????)-->  &nbsp;

</div>




<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">????????????</label>
<select name="sfl" id="sfl">
    <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>???????????????</option>>
    <option value="mb_name"<?php echo get_selected($_GET['sfl'], "mb_name"); ?>>????????????</option>
</select>

<label for="stx" class="sound_only">?????????<strong class="sound_only"> ??????</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">


 ?????? ?????? : <input type="text" name="start_dt" id="start_dt" placeholder="From" class="frm_input" value="<?=$_GET[start_dt]?>" /> ~ <input type="text" name="end_dt" id="end_dt" placeholder="To" class="frm_input" value="<?=$_GET[end_dt]?>" />


<?

echo $html;

?>
      

<input type="submit" class="btn_submit" value="??????">
<input type="button" class="btn_submit" value="??????" onclick="document.location.href='benefit_list_excel_out.php?<?echo $qstr?>'" />	
<br/>

</form>



<form name="benefitlist" id="benefitlist">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">
<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    ?????? <?php echo number_format($total_count) ?> ??? 
</div>
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> ??????</caption>
    <thead>
    <tr>
		<th scope="col">????????????</th>
        <th scope="col">????????????</th>
		<th scope="col">??????</a></th>
        <th scope="col">??????</th>
		<th scope="col">???????????????</a></th>
        <th scope="col">?????????</th>     
		
		
        <!--th scope="col">??????????????????</a></th>
        <th scope="col">??????????????????</a></th>
		<th scope="col">30????????????</a></th>
        <th scope="col">???????????????</a></th-->
        <th scope="col">????????????(USD)</a></th>
        <th scope="col">????????????(BTC)</a></th>		
        <th scope="col">????????????</a></th>				
		<th scope="col">??????</a></th>		
		<th scope="col">????????????</a></th>		
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {

        /*
				$sql2 = " select * from g5_member where mb_id='{$row['mb_id']}' ";
				$row2 = sql_fetch($sql2);

        $mb_nick = get_sideview($row['mb_id'], $row2['mb_nick'], $row2['mb_email'], $row2['mb_homepage']);

        $link1 = $link2 = '';

				
        $expr = '';
		*/


        $bg = 'bg'.($i%2);

		
			$soodang = $row['benefit'];
			$doodang2 = $row['benefit_usd'];

if($row['mb_level']==10){
	$member_lvString="Super Star";
}
else if($row['mb_level']==9){
	$member_lvString="Manager";
}
else if($row['mb_level']==8){
	$member_lvString="6 Star";
}
else if($row['mb_level']==7){
	$member_lvString="5 Star";
}
else if($row['mb_level']==6){
	$member_lvString="4 Star";
}
else if($row['mb_level']==5){
	$member_lvString="3 Star";
}
else if($row['mb_level']==4){
	$member_lvString="2 Star";
}
else if($row['mb_level']==3){
	$member_lvString="1 Star";
}
else if($row['mb_level']==2){
	$member_lvString="0 Star";
}
else if($row['mb_level']==1){
	$member_lvString="Miner";
}

    ?>

    <tr class="<?php echo $bg; ?>">

		<td width='100'><? echo $row['day'];?></td>
		<td width='100'><?php echo get_text($row['allowance_name']); ?></td>
		<td width='100'><?php echo $member_lvString?></td>
		<td width="150"><?php echo get_text($row['mb_name']); ?></td>
		<td class="td_num"><?php echo get_text($row['mb_id']); ?></td>
        <td width="150" ><?php echo get_text($row['mb_recommend']); ?></td>
        <!--td class="td_num td_pt"><?php echo $row['accu_my_sales'] ?></td>
        <td class="td_num"><?php echo $row['accu_habu_sum'] ?></td>
		<td class="td_num"><?php echo $row['mon_habu_sum'] ?></td>
        <td class="td_num td_pt"><?php echo $row['today_sales1'] ?></td-->
      
        <td width="150" align='center'><?php echo '$'.$doodang2 ?></td>
		<td width="150" align='center'><?php echo $soodang  ?></td>
        <td width="500"><?php echo ($row['rec_adm']) ?></td>
		<td width="100" align='center'><?php echo $row['exchange_rate']?></td>
		<td width="100"><?php if($row['benefit_limit1']==1){  echo "????????????"; }else { echo "??????"; }?></td>		
    </tr>

    <?php
    }

    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">????????? ????????????.</td></tr>';
    ?>
    </tbody>
    </table>
</div>


</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>



    <div class="btn_confirm01 btn_confirm">
    	<? if($what=='u') { ?>  <input type="submit" id="submit" value="??????" class="btn_submit"> <? } else{  ?> <input type="submit" id="submit" value="??????" class="btn_submit">   <? } ?>
    </div>

    </form>

</section>


<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/common.js"></script>


<script>
	
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
	$("#start_dt, #end_dt").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});

var str='';
function go_calc(n)
{



	switch(n){
		case 0: 
			location.href='total_soodang_run.php';
			break;
		case 1: 
			location.href='paid_mining_progress.php';
			break;
		case 2: 
			location.href='paid_bonus_progress.php';
			break;


	}
	

}
  
  
   



		
</script>

<?php
include_once ('./admin.tail.php');
?>
