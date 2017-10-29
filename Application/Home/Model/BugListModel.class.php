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
class BugListModel extends Model{ 
	public function get_bug_list()
	{

		$data = array();
		$count      = M('Bug_list')->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show       = $Page->show();// 分页显示输出
		$bug_list 	= M('Bug_list')->field('id,bug_name,add_time')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach ($bug_list as $key => $val) {
			$bug_list[$key]['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
		}
		$data['bug_list'] 	= $bug_list;
		$data['count'] 		= $count;
		$data['show'] 		= $show;
		return $data;
	}
}