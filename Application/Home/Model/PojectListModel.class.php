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
class PojectListModel extends Model{ 
	public function get_poject_list()
	{

		$data = array();
		$count      = M('Poject_list')->count();// 查询满足要求的总记录数
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show       = $Page->show();// 分页显示输出
		$poject_list = M('Poject_list')->field('id,poject_name,poject_start_time,poject_end_time,add_time')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach ($poject_list as $key => $val) {
			$poject_list[$key]['poject_start_time'] = date('Y-m-d',$val['poject_start_time']);
			$poject_list[$key]['poject_end_time'] = date('Y-m-d',$val['poject_end_time']);
			$poject_list[$key]['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
		}
		$data['poject_list'] = $poject_list;
		$data['count'] = $count;
		$data['show'] = $show;
		return $data;
	}
}