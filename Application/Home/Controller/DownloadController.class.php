<?php
// +----------------------------------------------------------------------
// | WerePort v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.we-report.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 226safe Team Poacher <QQ:177705712>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
class DownloadController extends Controller {

	public function download()
	{
		$pid = I('id','','intval');
		$report = M('Poject_list')->where("id = '$pid'")->find();
		if (!file_exists("./Application/Home/View/Temp/$report[temp]".'.html')) {
			$this->error("[$report[temp].html]模板文件不存在,请重新选择模板文件");
		}
		$report['poject_time'] 			= date('Y-m',$report['poject_start_time']);
		$report['poject_start_time'] 	= date('Y年m月d日',$report['poject_start_time']);
		$report['poject_end_time'] 		= date('Y年m月d日',$report['poject_end_time']);
 		$system_list 					= M('System_list')->where("pid = '$pid'")->select();
		$bug_type_level 				= array('低危','中危','高危','严重');
		$system_info 					= array();
		$system_cat 					= array();
		$system_level 					= 1;
		foreach ($system_list as $key => $val) {
			$system_list[$key]['result_list'] 	= M('Result_list')->where("pid = '$pid' AND sid = '$val[id]'")->select();
			$system_info[$key]['system_name'] 	= $val['system_name'];
			$system_info[$key]['system_url'] 	= $val['system_url'];
			$system_info[$key]['system_ip'] 	= $val['system_ip'];
			$system_cat[$key]['system_name'] 	= $val['system_name'];
			$system_cat[$key]['system_level'] 	= $system_level;
			$system_level++;
			$bug_level = 1;
			foreach ($system_list[$key]['result_list'] as $k => $v) {
				$system_list[$key]['result_list'][$k]['bug_img']	 	= explode(',',$v['bug_img']);
				$system_list[$key]['result_list'][$k]['bug_name'] 		= M('Bug_list')->where("id = '$v[bid]'")->getField('bug_name');
				$system_list[$key]['result_list'][$k]['bug_desc'] 		= M('Bug_list')->where("id = '$v[bid]'")->getField('bug_desc');
				$system_list[$key]['result_list'][$k]['bug_repair'] 	= M('Bug_list')->where("id = '$v[bid]'")->getField('bug_repair');
				$system_list[$key]['result_list'][$k]['bug_harm'] 		= $bug_type_level[$v['bug_level']];
				$system_list[$key]['result_list'][$k]['bug_param'] 		= stripslashes(htmlspecialchars($v['bug_param']));
				$system_cat[$key]['result_list'][$k]['bug_name'] 		= $system_list[$key]['result_list'][$k]['bug_name'];
				$system_cat[$key]['result_list'][$k]['bug_level'] 		= $bug_level;
				$bug_level++;
			}
		}
		$Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
    	$bug_count = $Model->query("select count(bid) as b_count from we_result_list where pid='$pid' group by bid");
    	$bug_type = M('Result_list')->field('bid as bug_name')->where("pid = '$pid'")->group('bid')->select();
    	foreach ($bug_type as $key => $val) {
    		$bug_type[$key] = $bug_count[$key];
    		$bug_type[$key]['bug_name'] = M('Bug_list')->where("id = '$val[bug_name]'")->getField('bug_name');
    		$bug_type[$key]['bug_id'] = $id++;

    	}
		$url = 'http://'.$_SERVER['HTTP_HOST'] . str_replace("index.php", "", $_SERVER['SCRIPT_NAME']) . "/Public/Home/image/report/";
		$this->assign('report',$report);
		$this->assign('bug_type',$bug_type);
		$this->assign('system_cat',$system_cat);
		$this->assign('system_info',$system_info);
		$this->assign('system_list',$system_list);
		header("Cache-Control: no-cache, must-revalidate"); 
		header("Pragma: no-cache"); 
		$fileContent = $this->WordMake($this->fetch("Temp/$report[temp]"),$url);
		if(strpos($fileContent,'not exist!') !== false){
			$this->error("生成失败,请检查报告内图片是否存在。");
 		}else{
			$fileName 	 = $report['poject_name'] . "报告"; 
			header("Content-Type: application/doc"); 
			header("Content-Disposition: attachment; filename=" . $fileName . ".doc");
			echo $fileContent;
 		}

	}

	function WordMake( $content , $absolutePath = "" , $isEraseLink = true )
	{
		import("ORG.Util.Wordmaker");
		$mht = new \Wordmaker();
		if ($isEraseLink) {
			$content = preg_replace('/<a\s*.*?\s*>(\s*.*?\s*)<\/a>/i' , '$1' , $content);
		}
		$images = array();
		$files = array();
		$matches = array();
		if (preg_match_all('/<img.*?src="(.*?)".*?>/is',$content ,$matches )) {
			$arrPath = $matches[1];		
			for ( $i=0;$i<count($arrPath);$i++) {
				$path = $arrPath[$i];
				$imgPath = trim( $path );				
				if ( $imgPath != "" ) {
					$files[] = $imgPath;
					if( substr($imgPath,0,7) == 'http://') {
					}
					else
					{
						$imgPath = $absolutePath.$imgPath;
					}
					$images[] = $imgPath;
				}
			}
		}
		$mht->AddContents("tmp.html",$mht->GetMimeType("tmp.html"),$content);
		for ( $i=0;$i<count($images);$i++) {
			$image = $images[$i];
			if ( @fopen($image , 'r') ) {
				$imgcontent = @file_get_contents( $image );

				if ( $content )
					$mht->AddContents($files[$i],$mht->GetMimeType($image),$imgcontent);
			} else {
				return "image:".$image." not exist!<br />";
			}
		}
		return $mht->GetFile();
	}

}