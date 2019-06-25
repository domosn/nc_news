<!DOCTYPE html>
<!--[if lt IE 7]><html class="lt-ie9 lt-ie8 lt-ie7" lang="zh-hant" dir="ltr"><![endif]-->
<!--[if IE 7]><html class="lt-ie9 lt-ie8" lang="zh-hant" dir="ltr"><![endif]-->
<!--[if IE 8]><html class="lt-ie9" lang="zh-hant" dir="ltr"><![endif]-->
<!--[if gt IE 8]><!--><html lang="zh-hant" dir="ltr" prefix="fb: http://www.facebook.com/2008/fbml content:
http://purl.org/rss/1.0/modules/content/ dc: http://purl.org/dc/terms/ foaf: http://xmlns.com/foaf/0.1/ og:
http://ogp.me/ns# rdfs: http://www.w3.org/2000/01/rdf-schema# sioc: http://rdfs.org/sioc/ns# sioct:
http://rdfs.org/sioc/types# skos: http://www.w3.org/2004/02/skos/core# xsd:
http://www.w3.org/2001/XMLSchema#"><!--<![endif]-->
<head>
<meta charset="utf-8" />
<!--link href="default.css" rel="stylesheet" type="text/css"-->
<!-- meta name="viewport" content="width=device-width, initial-scale=1"-->
<?php

echo file_get_contents("nc_header.inc")."\n";
?>
<link rel="stylesheet" type="text/css" href="./css/w3.css">
<!--link rel="stylesheet" type="text/css" href="./css/fonts.css"-->
<style type="text/css">
html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td, article, 
aside, canvas, details, embed, figure, figcaption, footer, header, hgroup, menu, nav, output, ruby, section, summary, time, mark, audio, video {
    font-family: Helvetica, Arial, "Noto Sans TC", "微軟正黑體", "Microsoft JhengHei", serif, sans-serif;
}
.center_text {
     text-align:center;	
}
.no_line {
	text-decoration:none;
}
.no_padding {
	padding-top:0px;
	padding-bottom:0px;
	padding-left:0px;
	padding-right:0px;
}
.no_margin {
	margin-top:0px;
	margin-bottom:0px;
	margin-left:0px;
	margin-right:0px;
}
</style>
</head>
<!--body id="page" class="d-flex flex-column"--><!-- Google Tag Manager (noscript) -->
<!--noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KL4F9B4"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript-->
<body>
<?php

function int_div($a, $b) {
	if(function_exists("intdiv")) {
		return intdiv($a, $b);
	}
	return ($a - $a % $b) / $b;
}

//    font-family: Helvetica, Arial, "Noto Sans TC", "微軟正黑體", "Microsoft JhengHei", serif, sans-serif;

echo file_get_contents("nc_body_header.inc")."\n";
echo "<div class=\"maxW\">\n";
echo "<div class=\"maxWX\">\n";

include("db_settings.php");

include("general_query.php");

$page_cnt = 6;    
$apage_cnt = 20;
$rpage_cnt = 10;
$last_start = 0;
$nid_map = array();
$nid_deny = array();
$nid_info = array();
$private_path = "https://wiki.net-chinese.com.tw/sites/all/upload/";
$public_path = "https://wiki.net-chinese.com.tw/sites/wiki.net-chinese.taipei/files/";
$row_count = -1;

general_query($db, $db_node, $db_node_type, $page_cnt, $apage_cnt, $rpage_cnt, $last_start, $nid_map, $nid_deny, $nid_info, $private_path, $public_path, $row_count, FALSE);

if(is_null($_GET['n']) && $row_count >= 0) {
	$start = 0;
	if($_GET['i']) 
		$start = $_GET['i'];
	echo "<div class=\"w3-cell-row\">\n";
	echo "<div class=\"w3-cell\">\n";
	echo "<h1 class=\"center_text\">最新消息</h1>\n";
        for($i=$start; $i < $row_count && $i < ($start+ $page_cnt); $i++) {
		$node_id = $nid_map[$i][0];
      		echo "<div class=\"w3-cell-row\">\n";
                echo "<div class=\"w3-cell w3-padding\" style=\"width:25%;align-content:center;\"><img alt=\"pic\" style=\"width:100%;max-width:180px\" src=\"".$nid_info[$node_id][1]."\"></div>\n";
                echo "<div class=\"w3-cell w3-cell-middle\" style=\"width:75%;\">\n";
      		echo "<div><p style=\"font-size:18px;color:#003C9D;\">".$nid_map[$i][4]."</p></div>\n";
      		echo "<div><p style=\"font-size:14px;\">".date("Y-m-d", $nid_map[$i][7])." ";
		$j = 0;
		foreach($nid_info[$node_id][3] as $tid => $tname) {
			echo "<a class=\"w3-button\" href='".$_SERVER['PHP_SELF']."?n=".$node_id."&t=".$tid."'>".$tname."</a>\n";
			$j += 1;
			if($j >= 3) break;
		} 
		echo "</p></div>\n";
      		echo "<div ><p style=\"font-size:14px;\">".mb_substr(strip_tags($nid_info[$node_id][2]), 0, 50)."...</p>\n";
		echo "<p style=\"text-align:right;font-weight: bold;color:#ff6600;\"><a href=\"".$_SERVER['PHP_SELF']."?n=".$nid_map[$i][0]."\">閱讀更多...</a></p>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
        }
	echo "<br/><p class=\"center_text\" style=\"font-size:18px;color:#005da2;\"><a href=\"".$_SERVER['PHP_SELF']."?n=0&a=1\">閱讀更多新聞</a></p><br/>\n";
	$total_pages = int_div($row_count, $page_cnt) + ($row_count%$page_cnt ? 1 : 0);
	if($total_pages <= 1) {
		echo "<div class=\"w3-cell-row\">\n";
			echo "<div class=\"w3-cell center_text w3-cell-middle\" >第 1 頁，共 1 頁/總筆數".$row_count."筆</div>\n";
		echo "</div>\n";
	}
	else if($total_pages == 2) {
		echo "<div class=\"w3-cell-row\">\n";
			echo "<div class=\"w3-cell center_text w3-cell-middle\" >第 ".(int_div($start,$page_cnt)+1)."頁，共 2 頁/總筆數".$row_count."筆</div>\n";
		echo "</div>\n";
		echo "<div class=\"w3-center\"><div class=\"w3-bar w3-border\">\n";
			echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=0'>最前頁</a></div>\n";
			echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=0'>&laquo;</a></div>\n";
			echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=0'>1</a></div>\n";
			echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=".$page_cnt."'>2</a></div>\n";
			echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=".$page_cnt."'>&raquo;</a></div>\n";
			echo "<div class=\"w3-button w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=".$last_start."'>最末頁</a></div>\n";
		echo "</div>\n"; 
	}
	else {
		echo "<div class=\"w3-cell-row\">\n";
			echo "<div class=\"w3-cell center_text w3-cell-middle\" >第 ".(int_div($start,$page_cnt)+1)."頁，共".$total_pages."頁/總筆數".$row_count."筆</div>\n";
			$prev = (($start - $page_cnt >= 0) ? ($start - $page_cnt) : 0);
			$cur  = (($prev < $last_start) ? ($prev + $page_cnt) : $last_start);
			$next = (($cur < $last_start) ? ($cur + $page_cnt) : $last_start);
			$cur  = (($next - $page_cnt >= 0) ? ($next - $page_cnt) : 0);
			$prev  = (($cur - $page_cnt >= 0) ? ($cur - $page_cnt) : 0);
		echo "</div>\n";
		echo "<div class=\"w3-center\"><div class=\"w3-bar w3-border\">\n";
			echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=0'>最前頁</a></div>\n";
			echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=".(($start - $page_cnt >= 0) ? ($start - $page_cnt) : 0)."'>&laquo;</a></div>\n";
			echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=".$prev."'>".(int_div($prev,$page_cnt)+1)."</a></div>\n";
			echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=".$cur."'>".(int_div($cur,$page_cnt)+1)."</a></div>\n";
			echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=".$next."'>".(int_div($next,$page_cnt)+1)."</a></div>\n";
			echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=".(($start < $last_start) ? ($start + $page_cnt) : $last_start)."'>&raquo;</a></div>\n";
			echo "<div class=\"w3-button w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?i=".$last_start."'>最末頁</a></div>\n";
		echo "</div>\n"; 
	}
	echo "</div>\n";
	echo "</div>\n";
	echo "</div>\n";
}
else if(!is_null($_GET['n']) && $row_count >= 0) {
	if(!is_null($_GET['a'])) {
		$start = 0;
		if($_GET['n']) 
			$start = $_GET['n'];
		if($row_count > 0) {
			$last_start = int_div($row_count-1, $apage_cnt)*$apage_cnt;
		}
		else {
			$last_start = 0;
		}
		echo "<div class=\"w3-cell-row\">\n";
		echo "<div class=\"w3-cell\">\n";
		echo "<h1 class=\"center_text\">所有新聞</h1>\n";
      		echo "<div class=\"w3-cell-row\" style=\"font-size:12px;font-weight:bold;\">\n";
	        echo "<div class=\"w3-cell w3-padding w3-cell-middle center_text\" style=\"width:14%;align-content:center;\">日期</div>\n";
	        echo "<div class=\"w3-cell w3-cell-middle center_text\" style=\"width:50%;font-size:12px;font-weight:bold;\">新聞標題</div>\n";
	        echo "<div class=\"w3-cell w3-cell-middle center_text\" style=\"width:36%;font-size:12px;font-weight:bold;\">新聞類別</div>\n";
		echo "</div>\n";
		for($i=$start; $i < $row_count && $i < ($start+ $apage_cnt); $i++) {
			$node_id = $nid_map[$i][0];
	      		echo "<div class=\"w3-cell-row\">\n";
		        echo "<div class=\"w3-cell w3-padding w3-cell-middle\" style=\"width:14%;align-content:center;\">".date("Y-m-d", $nid_map[$i][7])."</div>\n";
		        echo "<div class=\"w3-cell w3-cell-middle\" style=\"width:50%;\">\n";
	      		//echo "<div class=\"w3-border w3-round\"><a href=\"".$_SERVER['PHP_SELF']."?n=".$nid_map[$i][0]."\" style=\"color:#003C9D;\">".$nid_map[$i][4]."</a></div>\n";
	      		echo "<div><a href=\"".$_SERVER['PHP_SELF']."?n=".$nid_map[$i][0]."\" style=\"color:#003C9D;\">".$nid_map[$i][4]."</a></div>\n";
		        echo "</div>\n";
			$j = 0;
			foreach($nid_info[$node_id][3] as $tid => $tname) {
				echo "<div class=\"w3-cell w3-cell-middle\" style=\"width:12%;\">\n";
		      		echo "<div><p style=\"font-size:14px;\">";
				echo "<a class=\"w3-button\" href='".$_SERVER['PHP_SELF']."?n=".$node_id."&t=".$tid."'>".$tname."</a>\n";
				echo "</p></div>\n";
				echo "</div>\n";
				$j += 1;
				if($j >= 3) break;
			} 
			if($j < 3) {
				for(; $j<3; $j++) {
					echo "<div class=\"w3-cell w3-cell-middle\" style=\"width:12%;\">\n";
			      		echo "<div><p style=\"font-size:14px;display:none;\">";
					echo "empty\n";
					echo "</p></div>\n";
					echo "</div>\n";
				}
			} 
			echo "</div>\n";
		}
		$total_pages = int_div($row_count, $apage_cnt) + ($row_count%$apage_cnt ? 1 : 0);
		if($total_pages <= 1) {
			echo "<div class=\"w3-cell-row\">\n";
				echo "<div class=\"w3-cell center_text w3-cell-middle\" >第 1 頁，共 1 頁/總筆數".$row_count."筆</div>\n";
			echo "</div>\n";
		}
		else if($total_pages == 2 ) {
			echo "<div class=\"w3-cell-row\">\n";
				echo "<div class=\"w3-cell center_text w3-cell-middle\" >第 ".(int_div($start,$apage_cnt)+1)."頁，共 2 頁/總筆數".$row_count."筆</div>\n";
			echo "</div>\n";
			echo "<div class=\"w3-center\"><div class=\"w3-bar w3-border\">\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=0&a=1'>最前頁</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=0&a=1'>&laquo;</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=0&a=1'>1</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$apage_cnt."&a=1'>2</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$apage_cnt."&a=1'>&raquo;</a></div>\n";
				echo "<div class=\"w3-button w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$last_start."&a=1'>最末頁</a></div>\n";
			echo "</div>\n";
		}
		else {
			echo "<div class=\"w3-cell-row\">\n";
				echo "<div class=\"w3-cell center_text w3-cell-middle\" >第 ".(int_div($start,$apage_cnt)+1)."頁，共".$total_pages."頁/總筆數".$row_count."筆</div>\n";
				$prev = (($start - $apage_cnt >= 0) ? ($start - $apage_cnt) : 0);
				$cur  = (($prev < $last_start) ? ($prev + $apage_cnt) : $last_start);
				$next = (($cur < $last_start) ? ($cur + $apage_cnt) : $last_start);
				$cur  = (($next - $apage_cnt >= 0) ? ($next - $apage_cnt) : 0);
				$prev  = (($cur - $apage_cnt >= 0) ? ($cur - $apage_cnt) : 0);
			echo "</div>\n";
			echo "<div class=\"w3-center\"><div class=\"w3-bar w3-border\">\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=0&a=1'>最前頁</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".(($start - $apage_cnt >= 0) ? ($start - $apage_cnt) : 0)."&a=1'>&laquo;</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$prev."&a=1'>".(int_div($prev,$apage_cnt)+1)."</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$cur."&a=1'>".(int_div($cur,$apage_cnt)+1)."</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$next."&a=1'>".(int_div($next,$apage_cnt)+1)."</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".(($start < $last_start) ? ($start + $apage_cnt) : $last_start)."&a=1'>&raquo;</a></div>\n";
				echo "<div class=\"w3-button w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$last_start."&a=1'>最末頁</a></div>\n";
			echo "</div>\n";
		}
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
	}
	else if(is_null($_GET['t'])) {
		$start = 0;
		$node_id = is_null($_GET['n']) ? $nid_map[0][0] : $_GET['n'];
		if($_GET['n']) {
			$start = $_GET['n'];
		}
		$last_start = int_div($row_count-1, $rpage_cnt)*$rpage_cnt;
		echo "<div class=\"w3-cell-row\"><div class=\"w3-cell\">\n";
		echo "<span style=\"font-size:18px;color:#003c9d;float:left;\">".$nid_info[$node_id][4]."</span>\n";
		echo "<span style=\"font-size:16px;color:#005da2;float:right;\"><a href='".$_SERVER['PHP_SELF']."'>回到最新消息</a></span>\n";
		echo "</div></div>\n";
		echo "<div class=\"w3-cell-row\"><div class=\"w3-cell\"><div class=\"w3-card\">\n";
		echo $nid_info[$node_id][2]."\n";
		echo "</div></div></div>\n";
		echo "<div class=\"w3-cell-row\"><div class=\"w3-cell\">\n";
		echo "<span style=\"float:left;\"><p style=\"font-size:14px;\">".date("Y-m-d", $nid_info[$node_id][5])." ";
		$j = 0;
		foreach($nid_info[$node_id][3] as $tid => $tname) {
			echo "<a class=\"w3-button\" href='".$_SERVER['PHP_SELF']."?n=".$node_id."&t=".$tid."'>".$tname."</a>\n";
			$j += 1;
			if($j >= 3) break;
		} 
		echo "</p></span>";
		echo "<span style=\"font-size:16px;color:#005da2;float:right;\"><a href='".$_SERVER['PHP_SELF']."'>回到最新消息</a></span></div></div>\n";
		echo "<div class=\"w3-cell-row\">\n";
		echo "<div class=\"w3-cell\">\n";
		echo "<p class=\"center_text\">你可能還有興趣...</p>\n";
		for($i=0; $i < $row_count && $i < $rpage_cnt; $i++) {
			$node_id = $nid_map[$i][0];
	      		echo "<div class=\"w3-cell-row\">\n";
		        echo "<div class=\"w3-cell w3-padding w3-cell-middle\" style=\"width:20%;align-content:center;\">".date("Y-m-d", $nid_map[$i][7])."</div>\n";
		        echo "<div class=\"w3-cell w3-cell-middle\" style=\"width:80%;\">\n";
	      		echo "<div><a href=\"".$_SERVER['PHP_SELF']."?n=".$nid_map[$i][0]."\" style=\"color:#003C9D;\">".$nid_map[$i][4]."</a></div>\n";
		        echo "</div>\n";
			echo "</div>\n";
		}
		echo "</div>\n";
		echo "</div>\n";
	}
	else if(!is_null($_GET['t'])){
		$start = 0;
		if($_GET['i']) $start = $_GET['i'];
		$tag_id = $_GET['t'];
		$nid_count = 0;
		$nid_last = 0;
		$rnid = array();
		$ndata = array();
		$tag_name = "...";

		//tag_query($db, $tag_id, $nid_count, $nid_last, $rnid, $ndata, $tag_name, $nid_deny, $apage_cnt, FALSE);
		$query = "SELECT * FROM `share_taxonomy_index` where tid=".$tag_id." ORDER BY nid DESC";
		$res = $db->query($query);
		if($res) {
			$rnid = $res->fetch_all();
			$nid_count = count($rnid);
			for($i = 0; $i < $nid_count; $i++) {
				$query = "SELECT nid, type, title, created FROM `share_node` where nid=".$rnid[$i][0]." AND type='dn_news'";
				$res = $db->query($query);
				if($res) {
					$row = $res->fetch_row();
					if($row && count($row) > 0 && array_search($row[0], $nid_deny) === FALSE) {
						$ndata[] = $row;
					}
				}
			}
			$nid_count = count($ndata);
			if($nid_count > 0) {
				$nid_last  = int_div($nid_count-1,$apage_cnt)*$apage_cnt;
			}
		}
		$query = "SELECT name FROM `share_taxonomy_term_data` where tid=".$tag_id;
		$res = $db->query($query);

		if($res) {
			$row = $res->fetch_row();
			$tag_name = $row[0];
		} 
		if($nid_count > 0) {
			echo "<div class=\"w3-cell-row\">\n";
			echo "<div class=\"w3-cell\">\n";
			echo "<h1 class=\"center_text\">".$tag_name."</h1>\n";
	      		echo "<div class=\"w3-cell-row\" style=\"font-size:12px;font-weight:bold;\">\n";
			echo "<div class=\"w3-cell w3-padding w3-cell-middle center_text\" style=\"width:14%;align-content:center;\">日期</div>\n";
			echo "<div class=\"w3-cell w3-cell-middle center_text\" style=\"width:50%;font-size:12px;font-weight:bold;\">新聞標題</div>\n";
			echo "<div class=\"w3-cell w3-cell-middle center_text\" style=\"width:36%;font-size:12px;font-weight:bold;\">新聞類別</div>\n";
			echo "</div>\n";
			for($i=$start; $i < $nid_count && $i < ($start+ $apage_cnt); $i++) {
				$node_id = $ndata[$i][0];
		      		echo "<div class=\"w3-cell-row\">\n";
				echo "<div class=\"w3-cell w3-padding w3-cell-middle\" style=\"width:14%;align-content:center;\">".date("Y-m-d", $ndata[$i][3])."</div>\n";
				echo "<div class=\"w3-cell w3-cell-middle\" style=\"width:50%;\">\n";
		      		echo "<div><a href=\"".$_SERVER['PHP_SELF']."?n=".$ndata[$i][0]."\" style=\"color:#003C9D;\">".$ndata[$i][2]."</a></div>\n";
				echo "</div>\n";
				$j = 0;
				foreach($nid_info[$node_id][3] as $tid => $tname) {
					echo "<div class=\"w3-cell w3-cell-middle\" style=\"width:12%;\">\n";
			      		echo "<div><p style=\"font-size:14px;\">";
					echo "<a class=\"w3-button\" href='".$_SERVER['PHP_SELF']."?n=".$node_id."&t=".$tid."'>".$tname."</a>\n";
					echo "</p></div>\n";
					echo "</div>\n";
					$j += 1;
					if($j >= 3) break;
				}
				if($j < 3) {
					for(; $j<=3; $j++) {
						echo "<div class=\"w3-cell w3-cell-middle\" style=\"width:12%;\">\n";
				      		echo "<div><p style=\"font-size:14px;display:none;\">";
						echo "empty\n";
						echo "</p></div>\n";
						echo "</div>\n";
					}
				} 
				echo "</div>\n";
			}
			$total_pages = int_div($nid_count, $apage_cnt) + ($nid_count%$apage_cnt ? 1 : 0);
			if($total_pages <= 1) {
				echo "<div class=\"w3-cell-row\">\n";
					echo "<div class=\"w3-cell center_text w3-cell-middle\" >第 1 頁，共 1 頁/總筆數".$nid_count."筆</div>\n";
				echo "</div>\n";
			}
			else if($total_pages == 2) {
				echo "<div class=\"w3-cell-row\">\n";
				echo "<div class=\"w3-cell center_text w3-cell-middle\" >第 ".(int_div($start,$apage_cnt)+1)."頁，共 2 頁/總筆數".$nid_count."筆</div>\n";
				echo "</div>\n";
				echo "<div class=\"w3-center\"><div class=\"w3-bar w3-border\">\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=0'>最前頁</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=0'>&laquo;</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=0'>1</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=".$apage_cnt."'>2</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=".$apage_cnt."'>&raquo;</a></div>\n";
				echo "<div class=\"w3-button w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=".$apage_cnt."'>最末頁</a></div>\n";
				echo "</div>\n";
			}
			else {
				echo "<div class=\"w3-cell-row\">\n";
				echo "<div class=\"w3-cell center_text w3-cell-middle\" >第 ".(int_div($start,$apage_cnt)+1)."頁，共".$total_pages."頁/總筆數".$nid_count."筆</div>\n";
				$prev = (($start - $apage_cnt >= 0) ? ($start - $apage_cnt) : 0);
				$cur = (($prev < $nid_last) ? ($prev + $apage_cnt) : $nid_last);
				$next = (($cur < $nid_last) ? ($cur + $apage_cnt) : $nid_last);
				$cur = (($next - $apage_cnt >= 0) ? ($next - $apage_cnt) : 0);
				$prev = (($cur - $apage_cnt >= 0) ? ($cur - $apage_cnt) : 0);
				echo "</div>\n";
				echo "<div class=\"w3-center\"><div class=\"w3-bar w3-border\">\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=0'>最前頁</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=".(($start - $apage_cnt >= 0) ? ($start - $apage_cnt) : 0)."'>&laquo;</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=".$prev."'>".(int_div($prev,$apage_cnt)+1)."</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=".$cur."'>".(int_div($cur,$apage_cnt)+1)."</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=".$next."'>".(int_div($next,$apage_cnt)+1)."</a></div>\n";
				echo "<div class=\"w3-button w3-border-right w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=".(($start < $nid_last) ? ($start + $apage_cnt) : $nid_last)."'>&raquo;</a></div>\n";
				echo "<div class=\"w3-button w3-white no_margin\" ><a class=\"no_line\" href='".$_SERVER['PHP_SELF']."?n=".$_GET['n']."&t=".$_GET['t']."&i=".$nid_last."'>最末頁</a></div>\n";
				echo "</div>\n";
			}			
			echo "</div>\n";
			echo "</div>\n";
			echo "</div>\n";
			echo "</div>\n";
		}

	}
}
echo "</div>\n";
echo "</div>\n";

echo file_get_contents("nc_body_footer.inc")."\n";
?>
</body>
