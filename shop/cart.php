<?php
include_once('./_common.php');

// 보관기간이 지난 상품 삭제
cart_item_clean();

// cart id 설정
set_cart_id($sw_direct);


$s_cart_id = get_session('ss_cart_id');

// 선택필드 초기화
$sql = " update {$g5['g5_shop_cart_table']} set ct_select = '0' where od_id = '$s_cart_id' ";
sql_query($sql);

$cart_action_url = G5_SHOP_URL.'/cartupdate.php';

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/cart.php');
    return;
}

// 테마에 cart.php 있으면 include
if(defined('G5_THEME_SHOP_PATH')) {
    $theme_cart_file = G5_THEME_SHOP_PATH.'/cart.php';
    if(is_file($theme_cart_file)) {
        include_once($theme_cart_file);
        return;
        unset($theme_cart_file);
    }
}

$g5['title'] = '장바구니';
include_once('./_head.php');

//echo $cart_action_url;
?>
<style>
    html, body {background-color: #fff !important;}
    .tbl_head01 thead th {background:#fff;border-top:none;border-bottom:1px solid #000;padding: 5px 0;}
    .td_numbig {color:#ef0000; font-weight:bold;}
    .tbl_head01 td {border:0px;}
    .tbl_head01 tbody {border-bottom:1px solid #000;}
    .btn_del {cursor:pointer;}
    .Grp {min-height: 600px;}
</style>

<!-- 장바구니 시작 { -->
<script src="<?php echo G5_JS_URL; ?>/shop.js"></script>

<div id="sod_bsk">

    <form name="frmcartlist" id="sod_bsk_list" method="post" action="<?php echo $cart_action_url; ?>">
    <div class="tbl_head01 tbl_wrap">
        <table>
            <colgroup>
                <col style="width:90px;">
                <col style="width:auto;">
                <col style="width:90px;">
                <col style="width:90px;">
                <col style="width:90px;">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"> </th>
                    <th scope="col" style="text-align:left;">Product Description</th>
                    <th scope="col"> </th>
                    <th scope="col">Price</th>
                    <th scope="col">Quantity</th>
                    <!-- <th scope="col">Selling Price</th> -->
                    
                    <!-- <th scope="col">BTC</th> -->

                    <!-- <th scope="col">
                        <label for="ct_all" class="sound_only">상품 전체</label>
                        <input type="checkbox" name="ct_all" value="1" id="ct_all" checked="checked">
                    </th> -->
                </tr>
            </thead>
            <tbody>
        <?php
        $tot_point = 0;
        $tot_sell_price = 0;

        // $s_cart_id 로 현재 장바구니 자료 쿼리
        $sql = " select a.ct_id,
                        a.it_id,
                        a.it_name,
                        b.it_basic,
                        a.ct_price,
                        a.ct_point,
                        a.ct_qty,
                        a.ct_status,
                        a.ct_send_cost,
                        a.it_sc_type,
                        b.ca_id,
                        b.ca_id2,
                        b.ca_id3
                   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_item_table']} b on ( a.it_id = b.it_id )
                  where a.od_id = '$s_cart_id' ";
        $sql .= " group by a.it_id ";
        $sql .= " order by a.it_id desc";
        $result = sql_query($sql,false);

        $it_send_cost = 0;

        $sum_qty = 0;

        for ($i=0; $row=sql_fetch_array($result); $i++)
        {
            // 합계금액 계산
            $sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
                            SUM(ct_point * ct_qty) as point,
                            SUM(ct_qty) as qty
                        from {$g5['g5_shop_cart_table']}
                        where it_id = '{$row['it_id']}'
                          and od_id = '$s_cart_id' ";
            $sum = sql_fetch($sql);

            if ($i==0) { // 계속쇼핑
                $continue_ca_id = $row['ca_id'];
            }

            // $a1 = '<a href="./item.php?it_id='.$row['it_id'].'"><b>';
            // $a2 = '</b></a>';
            $a1 = '<strong style="color:#1f4e79;">';
            $a2 = '</strong>';
            $image = get_it_image($row['it_id'], 70, 70);

            $it_name = $a1 . stripslashes($row['it_name']) . $a2;
            $it_options = print_item_options($row['it_id'], $s_cart_id);
            if($it_options) {
                // $mod_options = '<div class="sod_option_btn"><button type="button" class="mod_options">to Modify selecting </button></div>';
                // $it_name .= '<div class="sod_opt">'.$it_options.'</div>';
            }

            // 배송비
            switch($row['ct_send_cost'])
            {
                case 1:
                    $ct_send_cost = '착불';
                    break;
                case 2:
                    $ct_send_cost = '무료';
                    break;
                default:
                    $ct_send_cost = '선불';
                    break;
            }

            // 조건부무료
            if($row['it_sc_type'] == 2) {
                $sendcost = get_item_sendcost($row['it_id'], $sum['price'],c, $s_cart_id);

                if($sendcost == 0)
                    $ct_send_cost = '무료';
            }

            $sum_qty    += $sum['qty'];

            $point      = $sum['point'];
            $sell_price = $sum['price'];

            $sql_coin = "select * from coin_cost";
	        $row_coin = sql_fetch($sql_coin);

        ?>

        <tr>
            <td class="sod_img"><?php echo $image; ?></td>
            <td>
                <input type="hidden" name="ct_chk[<?php echo $i; ?>]" value="1" id="ct_chk_<?php echo $i; ?>" >
                <input type="hidden" name="it_id[<?php echo $i; ?>]"    value="<?php echo $row['it_id']; ?>">
                <input type="hidden" name="it_name[<?php echo $i; ?>]"  value="<?php echo get_text($row['it_name']); ?>">
                <?php echo $it_name.$mod_options; ?><br>
                <?php echo $row['it_basic']?>
            </td>
            <td><div class="btn_del" idx="<?php echo $i; ?>" >delete</div></td>
            <!-- <td class="td_numbig"><?php echo number_format($row['ct_price']); ?></td> -->
            <td class="td_numbig">$ <span id="sell_price_<?php echo $i; ?>"><?php echo number_format($sell_price); ?></span></td>
            <td class="td_num"><?php echo number_format($sum['qty']); ?></td>
        </tr>

        <?php
            $tot_point      += round($sell_price/ $row_coin['btc_cost'],8);
            $tot_sell_price += $sell_price;
        } // for 끝

        if ($i == 0) {
            echo '<tr><td colspan="8" class="empty_table">There are no goods in the shopping cart.</td></tr>';
        } else {
            // 배송비 계산
            $send_cost = get_sendcost($s_cart_id, 0);
        }
        ?>
        </tbody>
        </table>
    </div>

    <?php
    $tot_price = $tot_sell_price + $send_cost; // 총계 = 주문상품금액합계 + 배송비
    if ($tot_price > 0 || $send_cost > 0) {
    ?>
    <div style="text-align:right;">
        <strong>
            Subtotal ( <?php echo $sum_qty;?> items) 
            <span style="color:#ef0000;">$ <?php echo number_format($tot_price); ?></span>
        </strong>
    </div>
    <?php } ?>

    <div id="sod_bsk_act">
        <?php if ($i == 0) { ?>
        <!--a href="<?php echo G5_SHOP_URL; ?>/" class="btn01">쇼핑 계속하기</a>-->
		        <a href="/new/buy_full.php" class="btn01">Continue</a>
        <?php } else { ?>
        <input type="hidden" name="url" value="./orderform.php">
        <input type="hidden" name="records" value="<?php echo $i; ?>">
        <input type="hidden" name="act" value="">
        <!--a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=<?php echo $continue_ca_id; ?>" class="btn01">쇼핑 계속하기</a>-->
		<a href="/new/buy_full.php" class="btn01">Continue</a>
        <button type="button" onclick="return form_check('buy');" class="btn_submit">To Order</button>
        <!-- <button type="button" onclick="return form_check('seldelete');" class="btn01">Sel Delete</button> -->
        <!-- <button type="button" onclick="return form_check('alldelete');" class="btn01">To Empty</button> -->
        <?php } ?>
    </div>

    </form>

</div>

<script>
$(function() {
    var close_btn_idx;

    // 선택사항수정
    $(".mod_options").click(function() {
        var it_id = $(this).closest("tr").find("input[name^=it_id]").val();
        var $this = $(this);
        close_btn_idx = $(".mod_options").index($(this));

        $.post(
            "./cartoption.php",
            { it_id: it_id },
            function(data) {
                $("#mod_option_frm").remove();
                $this.after("<div id=\"mod_option_frm\"></div>");
                $("#mod_option_frm").html(data);
                price_calculate();
            }
        );
    });

    // 모두선택
    $("input[name=ct_all]").click(function() {
        if($(this).is(":checked"))
            $("input[name^=ct_chk]").attr("checked", true);
        else
            $("input[name^=ct_chk]").attr("checked", false);
    });

    // 옵션수정 닫기
    $(document).on("click", "#mod_option_close", function() {
        $("#mod_option_frm").remove();
        $(".mod_options").eq(close_btn_idx).focus();
    });
    $("#win_mask").click(function () {
        $("#mod_option_frm").remove();
        $(".mod_options").eq(close_btn_idx).focus();
    });

    $(".btn_del").on('click',function (e) {
        $("input[name^=ct_chk]").val('0');
        $("input[name^=ct_chk]").eq($(this).attr('idx')).val(1);
        
        var f = document.frmcartlist;
        var cnt = f.records.value;

        f.act.value = "seldelete";
        f.submit();
    });

});

function form_check(act) {
    var f = document.frmcartlist;
    var cnt = f.records.value;

    if (act == "buy")
    {
        // if($("input[name^=ct_chk]:checked").size() < 1) {
        //     alert("주문하실 상품을 하나이상 선택해 주십시오.");
        //     return false;
        // }

        f.act.value = act;
        f.submit();
    }
    else if (act == "alldelete")
    {
        f.act.value = act;
        f.submit();
    }
    else if (act == "seldelete")
    {
        // if($("input[name^=ct_chk]:checked").size() < 1) {
        //     alert("삭제하실 상품을 하나이상 선택해 주십시오.");
        //     return false;
        // }

        f.act.value = act;
        f.submit();
    }

    return true;
}
</script>
<!-- } 장바구니 끝 -->

<?php
include_once('./_tail.php');
?>