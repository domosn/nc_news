<?php

function int_div_q($a, $b) {
	if(function_exists("intdiv")) {
		return intdiv($a, $b);
	}
	return ($a - $a % $b) / $b;
}

function put_cached_data(&$page_cnt, &$apage_cnt, &$rpage_cnt, &$last_start, &$nid_map, &$nid_deny, &$nid_info, &$private_path, &$public_path, &$row_count) {
	$all_data = array();
	$all_data["page_cnt"]     = $page_cnt;
	$all_data["apage_cnt"]    = $apage_cnt;
	$all_data["rpage_cnt"]    = $rpage_cnt;
	$all_data["last_start"]   = $last_start;
	$all_data["nid_map"]      = $nid_map;
	$all_data["nid_deny"]     = $nid_deny;
	$all_data["nid_info"]     = $nid_info;
	$all_data["private_path"] = $private_path;
	$all_data["public_path"]  = $public_path;
	$all_data["row_count"]    = $row_count;
	$all_data_json = json_encode($all_data);
	file_put_contents("./content_cache.data", $all_data_json);
}

function get_cached_data(&$page_cnt, &$apage_cnt, &$rpage_cnt, &$last_start, &$nid_map, &$nid_deny, &$nid_info, &$private_path, &$public_path, &$row_count) {
	$all_data_json = file_get_contents("./content_cache.data");
	if($all_data_json === FALSE)
		return FALSE;
	$all_data = json_decode($all_data_json, TRUE);
	if($all_data === NULL)
		return FALSE;
	$page_cnt     = $all_data["page_cnt"];
	$apage_cnt    = $all_data["apage_cnt"];
	$rpage_cnt    = $all_data["rpage_cnt"];
	$last_start   = $all_data["last_start"];
	$nid_map      = $all_data["nid_map"];
	$nid_deny     = $all_data["nid_deny"];
	$nid_info     = $all_data["nid_info"];
	$private_path = $all_data["private_path"];
	$public_path  = $all_data["public_path"];
	$row_count    = $all_data["row_count"];	
	return TRUE;
}

function general_query($db, $db_node, $db_node_type, &$page_cnt, &$apage_cnt, &$rpage_cnt, &$last_start, &$nid_map, &$nid_deny, &$nid_info, &$private_path, &$public_path, &$row_count, $force_query, $lang) {
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
	if(!$force_query) {
		if(file_exists("./content_cache.data")) {
			$cur_time = time();
			$ftime = filemtime("./content_cache.data");
			if(($cur_time - $ftime) < 10800) { // 3 hours
				if(get_cached_data($page_cnt, $apage_cnt, $rpage_cnt, $last_start, $nid_map, $nid_deny, $nid_info, $private_path, $public_path, $row_count)) {
					return;
				}
			}
			
		}
	}
	$query = "select * from ".$db_node." where type='".$db_node_type."' AND status=1 AND language='".$lang."' ORDER BY created DESC";
	//echo $query."\n";	
	$res = $db->query($query);
	if($res) {
		$nid_all = $res->fetch_all();
		$row_count = count($nid_all);
		$query = "select nid from share_taxonomy_index where tid=3660";
		$res = $db->query($query);
		if($res) {
			while($row = $res->fetch_row()) {
				$nid_deny[] = $row[0]; // <--- 
			} 
		} 
		for($i=0; $i < $row_count; $i++) {
			if(array_search($nid_all[$i][0], $nid_deny) === FALSE) {
				$nid_map[] = $nid_all[$i]; // <---
				$nid_info[$nid_all[$i][0]] = array(); // <---
				$nid_info[$nid_all[$i][0]][0] = -1; //image fid
				$nid_info[$nid_all[$i][0]][1] = "./180x180.png"; //image file path
				$nid_info[$nid_all[$i][0]][2] = ""; // node body
				$nid_info[$nid_all[$i][0]][3] = array(); // [tid] ==> "name"
				$nid_info[$nid_all[$i][0]][4] = $nid_all[$i][4];  // title
				$nid_info[$nid_all[$i][0]][5] = $nid_all[$i][7];  // creatation time
			}
		}
		$row_count = count($nid_map); // <---
		if($row_count > 0) {
			$last_start = int_div_q($row_count-1, $page_cnt)*$page_cnt;
		}
		foreach($nid_info as $key => $value) {
			$query = "select body_value from share_field_data_body where entity_type='node' AND entity_id=".$key;
			$res = $db->query($query);
			if($res) {
				$row = $res->fetch_row();
				if($row) {
					$nid_info[$key][2] = $row[0];
				}
			}
			$query ="SELECT tid FROM `share_taxonomy_index` where nid=".$key." ORDER BY tid DESC"; // to get all tags (taxonomy)
			$res = $db->query($query);
			if($res) {
				while($row = $res->fetch_row()) {
					$query = "SELECT name FROM `share_taxonomy_term_data` where tid=".$row[0];
					$rtname = $db->query($query);
					if($rtname) {
						$tname = $rtname->fetch_row();
						if($tname && strlen($tname[0])) {
							$nid_info[$key][3][$row[0]] = $tname[0];
						}
					}
				}
			}
			$query ="SELECT field_image_fid FROM `share_field_data_field_image` where entity_type='node' AND entity_id=".$key; // to get image
			$res = $db->query($query);
			if($res) {
				$row = $res->fetch_row();
				if($row) {
					$nid_info[$key][0] = $row[0];
					//$query ="SELECT uri FROM `share_file_managed` where entity_type='node' AND fid=".$row[0]; // to get image uri
					$query ="SELECT uri FROM `share_file_managed` where entity_type='node' AND fid=".$row[0]; // to get image uri
					$res = $db->query($query);
					if($res) {
						$row = $res->fetch_row();
						if(strncmp("private://", $row[0], strlen("private://")) === 0) {
							$nid_info[$key][1] = $private_path.substr($row[0], strlen("private://")); 
						}
						else if(strncmp("public://", $row[0], strlen("public://")) === 0) {
							$nid_info[$key][1] = $public_path.substr($row[0], strlen("public://")); 
						} 
					}
				}
			}
		}
	}
	put_cached_data($page_cnt, $apage_cnt, $rpage_cnt, $last_start, $nid_map, $nid_deny, $nid_info, $private_path, $public_path, $row_count);
}

function put_cached_tag($tag_id, &$nid_count, &$nid_last, &$rnid, &$ndata, &$tag_name) {
	$tag_data = array();
	if(file_exists("./tag_cache.data")) {
		$tag_data_json = file_get_contents("./tag_cache.data");
		if($tag_data_json !== FALSE) {
			$tag_data = json_decode($tag_data_json, TRUE);
			if($tag_data === NULL)
				$tag_data = array();
		}
	}
	$tag_data[$tag_id]["nid_count"] = $nid_count;
	$tag_data[$tag_id]["nid_last"]  = $nid_last;
	$tag_data[$tag_id]["rnid"] = $rnid;
	$tag_data[$tag_id]["ndata"]     = $ndata;
	$tag_data[$tag_id]["tag_name"]  = $tag_name;
	$tag_data_json = json_encode($tag_data);
	file_put_contents("./tag_cache.data", $tag_data_json);
}

function get_cached_tag($tag_id, &$nid_count, &$nid_last, &$rnid, &$ndata, &$tag_name) {
	if(file_exists("./tag_cache.data")) {
		$tag_data_json = file_get_contents("./tag_cache.data");
		if($tag_data_json === FALSE)
			return FALSE;
		$tag_data = json_decode($tag_data_json, TRUE);
		if($tag_data === NULL)
			return FALSE;
		if(!array_key_exists($tag_id, $tag_data))
			return FALSE;
	}
	$nid_count = $tag_data[$tag_id]["nid_count"];
	$nid_last  = $tag_data[$tag_id]["nid_last"];
	$rnid      = $tag_data[$tag_id]["rnid"];
	$ndata     = $tag_data[$tag_id]["ndata"];
	$tag_name  = $tag_data[$tag_id]["tag_name"];
	return TRUE;
}

function tag_query($db, $tag_id, &$nid_count, &$nid_last, &$rnid, &$ndata, &$tag_name, &$nid_deny, $apage_cnt, $force_query, $lang) {
	if(!$force_query) {
		if(file_exists("./tag_cache.data")) {
			$cur_time = time();
			$ftime = filemtime("./tag_cache.data");
			if(($cur_time - $ftime) < 10800) { // 3 hours
				if(get_cached_tag($tag_id, $nid_count, $nid_last, $rnid, $ndata, $tag_name)) {
					return;
				}
			}
			
		}
	}

	$query = "SELECT * FROM `share_taxonomy_index` where tid=".$tag_id." ORDER BY nid DESC";
	$res = $db->query($query);
	if($res) {
		$rnid = $res->fetch_all();
		$nid_count = count($rnid);
		for($i = 0; $i < $nid_count; $i++) {
			$query = "SELECT nid, type, title, created FROM `share_node` where nid=".$rnid[$i][0]." AND type='dn_news' AND language='".$lang."' ";
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
			$nid_last  = int_div_q($nid_count-1,$apage_cnt)*$apage_cnt;
		}
	}
	$query = "SELECT name FROM `share_taxonomy_term_data` where tid=".$tag_id;
	$res = $db->query($query);

	if($res) {
		$row = $res->fetch_row();
		$tag_name = $row[0];
	}
	put_cached_tag($tag_id, $nid_count, $nid_last, $rnid, $ndata, $tag_name); 
}


?>
