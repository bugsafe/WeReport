<?php
// +----------------------------------------------------------------------
// | WerePort v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.we-report.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 226safe Team Poacher <QQ:177705712>
// +----------------------------------------------------------------------
namespace Home\Model;
use Think\Model;
class ResultListModel extends Model{ 
	public function get_bug_list()
	{

		$where['pid']	= I('pid','','intval');
		$where['sid'] 	= I('sid','','intval');
		$data 			= array();
		$count      	= M('Result_list')->where($where)->count();// 查询满足要求的总记录数
		$Page       	= new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show       	= $Page->show();// 分页显示输出
		$bug_list 		= M('Result_list')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		$bug_level 		= array('低危','中危','高危','严重');
		foreach ($bug_list as $key => $val) {
			$bug_list[$key]['bid'] 			= M('Bug_list')->where("id = '$val[bid]'")->getField('bug_name');
			$bug_list[$key]['bug_level'] 	= $bug_level[$val['bug_level']];
			$bug_list[$key]['bug_param'] 	= htmlspecialchars($val['bug_param']);
			$bug_list[$key]['add_time'] 	= date('Y-m-d H:i:s',$val['add_time']);
		}
		$data['bug_list'] 	= $bug_list;
		$data['count'] 		= $count;
		$data['show'] 		= $show;

		return $data;

	}


	public function get_bug_img_list()
	{

		$where['id'] 	= I('id','','intval');
		$bug_list 		= M('Result_list')->field('id,bug_img')->where($where)->find();
		$result_id 		= $bug_list['id'];		
		$bug_list 		= explode(',',$bug_list['bug_img']);
		$url = 'http://'.$_SERVER['HTTP_HOST'] . str_replace("index.php", "", $_SERVER['SCRIPT_NAME']) . "Public/Home/image/report/";
		foreach ($bug_list as $key => $val) {
			$bug_img_list['bug_img'][$key] = $url . $val;

		}
	
		$bug_img_list['result_id'] = $result_id;		
		return $bug_img_list;

	}
}