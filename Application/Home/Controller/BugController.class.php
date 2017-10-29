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
class BugController extends Controller {

	public function index()
	{
		$bug_type_list = D('Bug_list')->get_bug_list();
		$this->assign('bug_type_list',$bug_type_list['bug_list']);
		$this->assign('count',$bug_type_list['count']);
    	$this->assign('page',$bug_type_list['show']);
		$this->display();

	}

	public function add_bug_type()
	{
		if (IS_POST) {
			$data['bug_name'] 	= I('bug_name','addslashes,htmlspecialchars');
			$data['bug_desc'] 	= I('bug_desc','addslashes,htmlspecialchars');
			$data['bug_repair'] = I('bug_repair','addslashes,htmlspecialchars');
			$data['add_time'] 	= time();
			$add_bug_type 		= M('Bug_list')->add($data);
			if ($add_bug_type) {
				$this->success("添加成功",U('Bug/index'));
			} else {
				$this->error('添加失败');
			}
		} else {
			$this->display();
		}
	}

	public function edit_bug_type()
	{
		if (IS_POST) {
			$where['id']		= I('id','','intval');
			$data['bug_name'] 	= I('bug_name','addslashes,htmlspecialchars');
			$data['bug_desc'] 	= I('bug_desc','addslashes,htmlspecialchars');
			$data['bug_repair'] = I('bug_repair','addslashes,htmlspecialchars');

			$update_bug_type 		= M('Bug_list')->where($where)->save($data);
			if ($update_bug_type) {
				$this->success("修改成功",U('Bug/index'));
			} else {
				$this->error('修改失败');
			}
		} else {
			$where['id'] = I('id','','intval');
			$bug_type_info = M('Bug_list')->field('id,bug_name,bug_desc,bug_repair')->where($where)->find();
			$this->assign('bug_type_info',$bug_type_info);
			$this->display();
		}
	}

	public function del_bug_type()
	{
		$where['id'] = I('id','','intval');
		$delete = M('Bug_list')->where($where)->delete();
		if ($delete) {
			echo 1;exit;
		} else {
			echo 0;exit;
		}
	}
}