<?php
$sub_menu = "600600";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');




function clear_all_benefit_mem(){
	for ($i=0; $row=sql_fetch_array($rrr); $i++) {   
		$cond[$i]['price_cond']='';
		$cond[$i]['source']='';
		$cond[$i]['source_cond1']='';
		$cond[$i]['source_cond2']='';
		$cond[$i]['source_in1']='';
		$cond[$i]['source_in2']='';
		$cond[$i]['mb_level_cond1']='';
		$cond[$i]['mb_level_cond2']='';
		$cond[$i]['mb_level_in1']='';
		$cond[$i]['mb_level_in2']='';
		$cond[$i]['partner_cnt']='';
		$cond[$i]['partner_cont']='';
		$cond[$i]['history_cnt']='';
		$cond[$i]['history_cond1']='';
		$cond[$i]['history_cond2']='';
		$cond[$i]['history_in1']='';
		$cond[$i]['history_in2']='';
		$cond[$i]['base_source']='';
		$cond[$i]['per']='';
		$cond[$i]['allowance_name']='';
		$cond[$i]['bigsmall']='';
		$cond[$i]['level']='';
		$cond[$i]['andor']='';
		$cond[$i]['mat']='';
		$cond[$i]['history']='';
		$cond[$i]['benefit']=0;
	}
}


function clear_benefit_mem(){
	for ($i=0; $row=sql_fetch_array($rrr); $i++) {   

		$cond[$i]['bigsmall']='';
		$cond[$i]['level']='';
		$cond[$i]['mat']='';
		$cond[$i]['history']='';
	}
}



$cond = array(array('price_cond'=>'','source'=>'','source_cond1'=>'','source_cond2'=>'','source_in1'=>'','source_in2'=>'','mb_level_cond1'=>'','mb_level_cond2'=>'','mb_level_in1'=>'','mb_level_in2'=>'','partner_cnt'=>'','partner_cont'=>'','history_cnt'=>'','history_cond1'=>'','history_cond2'=>'','history_in1'=>'','history_in2'=>'','base_source'=>'','per'=>'','allowance_name'=>'','mat'=>'','level'=>'','bigsmall'=>'','history'=>'','benefit'=>''));

$benefit = "SELECT * from nodekorea_set where immediate=1 order by partner_cnt desc, no";
$rrr = sql_query($benefit);

for ($i=0; $row=sql_fetch_array($rrr); $i++) {   
	$cond[$i]['price_cond']=$row['price_cond'];
	$cond[$i]['source']=$row['source'];
	$cond[$i]['source_cond1']=$row['source_cond1'];
	$cond[$i]['source_cond2']=$row['source_cond2'];
	$cond[$i]['source_in1']=$row['source_in1'];
	$cond[$i]['source_in2']=$row['source_in2'];
	$cond[$i]['mb_level_cond1']=$row['mb_level_cond1'];
	$cond[$i]['mb_level_cond2']=$row['mb_level_cond2'];
	$cond[$i]['mb_level_in1']=$row['mb_level_in1'];
	$cond[$i]['mb_level_in2']=$row['mb_level_in2'];
	$cond[$i]['partner_cnt']=$row['partner_cnt'];
	$cond[$i]['partner_cont']=$row['partner_cont'];
	$cond[$i]['history_cnt']=$row['history_cnt'];
	$cond[$i]['history_cond1']=$row['history_cond1'];
	$cond[$i]['history_cond2']=$row['history_cond2'];
	$cond[$i]['history_in1']=$row['history_in1'];
	$cond[$i]['history_in2']=$row['history_in2'];
	$cond[$i]['base_source']=$row['base_source'];
	$cond[$i]['per']=$row['per'];
	$cond[$i]['andor']=$row['andor'];
	$cond[$i]['allowance_name']=$row['allowance_name'];

	if($row['partner_cnt']>0){$cond[$i]['mat']=1;} else {$cond[$i]['mat']=0;} // ????????????
	
	if(  ($row['source_in1']!=0) || ($row['source_in1']!=0) ){ $cond[$i]['bigsmall']=1;  }else {$cond[$i]['bigsmall']=0;} // ????????????

	if(  ($row['mb_level_in1']!=0) || ($row['mb_level_in2']!=0) ){ $cond[$i]['level']=1; }else {$cond[$i]['level']=0;} //????????????

	if(  ($row['history_cond1']!='') || ($row['history_cond2']!='') ){ $cond[$i]['history']=1;} else{  $cond[$i]['history']=0;} //????????????

}





/*
function loopdeepod($recom, $partner_cnt,$partner_cont){

	$mat_deep=0;

	while( $recom!='admin' ){   

			 $res= sql_query("select mb_id, mat_cnt, from g5_member where mb_recommend='".$recom."' "); 

			 for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
				  $recom=$rrr['mb_id'];  	
				  if(   ($recom=='admin') || ($recom=='')  ) break;

				  if($partner_cnt<=$rrr['mat_cnt']){
					$mat_leg++;
				  } 

				  if($partner_cnt<=$mat_leg){
					$mat_cnt[$mat_deep]['cnt']=$mat_leg;
				  }
			  }
	
			$mat_deep++;

	}
	    return array($odhap,$cnt);    
}  
*/


function loopdeep2($recom){
	$cnt=0;
	$sql="select count(*) as hap from g5_member where mb_recommend='".$recom."' ";
	$res= sql_query($sql);
	for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
 			  $cnt=$rrr['hap'];
	} // for
	return $cnt;

}   


function loopdeepod($recom, $deep ,$partner_cnt,$partner_cont){
	$deep++;
    $res= sql_query("select * from g5_member where mb_recommend='".$recom."' "); 

    for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
 			  $recom=$rrr['mb_id'];  	
			  if($recom=='admin') break;
	} // for j	
	     
	return array($odhap,$evhap);    
}  
    



$leg_success=0;

//******** ????????? ???????????? ??????????????? ????????????... 
function loopdeep_one($recom, $deep ,$partner_cnt,$partner_cont, &$leg_success){
	
	//if(  ($leg_success==1) || ($leg_success==2)){ return $leg_succes;}
	$deep++;

	$sql= " delete from temp_mat_cnt"; 
	sql_query($sql);

    $res= sql_query("select * from g5_member where mb_recommend='".$recom."'  ");  //and mb_leave_date='' 
    for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
 			  $recom=$rrr['mb_id'];  
 			  	  if($recom=='admin') break;
 			  	   
						$rcnt=loopdeep2($recom);	
						//echo $recom.'==='.$deep.'==='.$rcnt.'<br/>';

						if(  ($rcnt<$partner_cnt)  ){	
							
							if($partner_cont>$deep) // ?????? deep????????? ??????(partner_cont)????????? ???????????? ?????? ???????????? ?????? ????????? ????????????(partner_cnt) ????????? ??? ????????? ????????? ????????? ??????
							{		
								/*echo $leg_success.'='.$recom.'==='.$deep.'==='.$rcnt.'??????<br/>';
								if($leg_success!=2){
									$leg_success=1;
								}
								return $leg_success;
								*/

								
							}else{

								$leg_success=2;

							}
						}else{

							if($partner_cont<=$deep) 
							{		
								//echo $leg_success.'='.$recom.'==='.$deep.'==='.$rcnt.'??????<br/>';
								$leg_success=2;
								return $leg_success;
							}	
						}

						if($partner_cont>=$deep){					
							$leg_success=loopdeep_one($recom, $deep ,$partner_cnt,$partner_cont,$leg_success);
						}else{
							
							return $leg_success;
						}	   		
	 } // for j	

	return $leg_success;
	   
}


//******** 2?????? ?????? ???????????? ??????????????? ????????????... 
function loopdeep($recom, $deep ,$partner_cnt,$partner_cont, &$leg_success){
	
	//if(  ($leg_success==1) || ($leg_success==2)){ return $leg_succes;}
	$deep++;

	$sql= " delete from temp_mat_cnt"; 
	sql_query($sql);

    $res= sql_query("select * from g5_member where mb_recommend='".$recom."'  ");  //and mb_leave_date='' 
    for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
 			  $recom=$rrr['mb_id'];  
 			  	  if($recom=='admin') break;
 			  	   
						$rcnt=loopdeep2($rrr['mb_id']);	

						echo $leg_success.'='.$recom.'==='.$deep.'==='.$rcnt.'<br/>';
						
						if(  ($rcnt<$partner_cnt)  ){				
							

							
							if($partner_cont>=$deep)
							{		
								echo $leg_success.'='.$recom.'==='.$deep.'==='.$rcnt.'??????<br/>';

								$leg_success=1;

								return $leg_success;
								
							}
						}

						if($partner_cont>=$deep){					
							//if($leg_success!=1){

							$leg_success=loopdeep($recom, $deep ,$partner_cnt,$partner_cont,$leg_success);

							//}
						}else{
							//$leg_success=2;
							return $leg_success;
						}	   		
	 } // for j	

	return $leg_success;
	   
}




//$save_noo_mon ????????? ?????? ??? ????????? ????????? nodekorea_pay ??? ?????????
//$save_benefit ??? ????????? ??? ??????????????? nodekorea_pay??? ?????????  


// ???????????? ????????? ????????? ?????? ????????? ????????? ??? ?????? ????????? ?????? ?????? ?????? ???????????? ?????????
if($save_noo_mon){ 
	$sql= " delete from nodekorea_pay"; 
	sql_query($sql);
}





if(   ($cond[$i]['price_cond'])=='PV') {
// PV??????
$price_cond="SUM(pv) AS hap";

} else if(   ($cond[$i]['price_cond'])=='BV') {
//BV?????? ??????
$price_cond="SUM(bv) AS hap";

}else{
// ???????????? 
$price_cond="SUM(od_receipt_price +od_receipt_localcard) AS hap";
}








//$fr_date="2015-05-01";
//$to_date="2015-05-31";

if(!$fr_date){ 
	$fr_date=date('Y-m-01');
}
if(!$to_date){ 
	$to_date=date('Y-m-31');
}

	$to_date=date('Y-m-31');
	$day=$to_date;
	$ym=substr($fr_date,0,7);




$sql_common = " FROM g5_shop_order AS o, g5_member AS m ";

$sql_search=" WHERE o.mb_id=m.mb_id AND o.mb_id=m.mb_id";
$sql_member=" AND m.mb_id='".$mb_id."'";

$searchdate=" AND DATE_FORMAT(o.od_receipt_time,'%Y-%m')='".$ym."' GROUP BY DATE_FORMAT(o.od_receipt_time,'%Y-%m') ";
$sql_mgroup='GROUP BY m.mb_id';
$sql_orderby=' order by od_receipt_time asc';


$sql = " SELECT mb_id, mb_name, mb_level, mb_recommend FROM g5_member";
$result = sql_query($sql);



//for ($i=0; $row=sql_fetch_array($result); $i++) {   
		
		$leg_success=0;
		$comp=$row['mb_id'];
		$name=$row['mb_name'];
		$comp='0010000195';
		$noohap=$row['hap'];
		$history_cnt=0;
		
		$recommend=0;


		//break;


		//******* ???????????? ??????
		$sql_member=" AND m.mb_id='".$comp."'";
		$sql = "SELECT $price_cond
					{$sql_common}
					{$sql_search}
					{$sql_member}	
					{$searchdate}
					
					{$having} ";
		$mysqles = sql_fetch($sql);

		$my_month_sales=$mysqles['hap'];

		$rec='';
	
				$partner_cnt=2;
				$partner_cont=2;

			$mycnt=loopdeep2($comp);
			
			
			//echo $mycnt.'>='.$partner_cnt.'---'.$comp.'<br/>';

					if(($mycnt>=$partner_cnt) && ($mycnt!=0) ){ 
							
							$leg_success= loopdeep($comp, $deep ,$partner_cnt, $partner_cont,&$leg_success);    // partner_cont??? -1 ????????? ???. ???, 4??? 
							
								if($leg_success==1){
									
									$temp_cond_mat=1;
								}else{

									if($comp!=''){
										echo  $comp.': ??????<br/>';
									}
								}
					}

		//****************

		

		$rec='';
		$my_month_sales=0;
		$noohap=0;

//}	

//alert('??????????????? ?????????????????????');
    ?>

