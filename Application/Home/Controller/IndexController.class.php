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
class IndexController extends Controller {

    public function index() 
    {
    	$report_list = D('Poject_list')->get_poject_list();
    	$this->assign('report_list',$report_list['poject_list']);
    	$this->assign('count',$report_list['count']);
    	$this->assign('page',$report_list['show']);
    	$this->display();
	}

	public function add_report() 
	{
		if (IS_POST) {
			if (!empty($_FILES['logo']['name'])) {
			    $upload 			= new \Think\Upload();// 实例化上传类
			    $upload->maxSize   	= 3145728 ;// 设置附件上传大小
			    $upload->exts      	= array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			    $upload->rootPath 	= './Public';
			    $upload->savePath  	= '/Home/image/report/'; // 设置附件上传根目录
			    $upload->saveExt  	= 'jpg'; // 设置附件上传根目录
				$upload->saveName 	= md5(mt_rand() . uniqid() . time());
			    $upload->autoSub 	= false;
			    // 上传文件 
			    $info   			=   $upload->upload();
			    if(!$info) {// 上传错误提示错误信息
			        $this->error($upload->getError());
			    }else{// 上传成功
			    	$data['poject_logo'] = $info['logo']['savename'];
			    }
			} else {
				$data['poject_logo'] = '';
			}
		    $data['temp'] 				= str_replace(".html","",I('report_temp','','htmlspecialchars,addslashes'));
		    $data['poject_com'] 		= I('poject_com','','htmlspecialchars,addslashes');
		    $data['poject_name'] 		= I('report_name','','htmlspecialchars,addslashes');
		    $data['poject_start_time'] 	= I('start_time','','strtotime');
		    $data['poject_end_time'] 	= I('end_time','','strtotime');
		    $data['poject_person'] 		= I('person','','htmlspecialchars,addslashes');
		    $data['add_time'] 			= time();


		    $create_report = M('poject_list')->add($data);

		    if ($create_report) {
		    	$this->success('创建报告成功',U('Index/index'));
		    } else {
		    	$this->error('创建报告失败');
		    }

		} else {
			$this->assign('report_temp',$this->get_temp());
			$this->display();
		}
	}

	public function del_report()
	{
		$report_id = I('id','','intval');

		$sys_res_list 	= M('Poject_list as poj')->field('poj.poject_logo,res.bug_img')->join("we_result_list AS res ON poj.id = res.pid")->where("poj.id = '$report_id'")->select();

		$poject_logo = M('Poject_list')->where("id = '$report_id'")->getField('poject_logo');
		$res_img_list = M('Result_list')->field('bug_img')->where("pid = '$report_id'")->select();

		foreach ($res_img_list as $key => $val) {
			if ($val['bug_img']) {
				$bug_img 	= explode(",",$val['bug_img']);
				foreach ($bug_img as $key => $val) {
					if(file_exists('./Public/Home/image/bug/'.$val)) {
						@unlink('./Public/Home/image/bug/'.$val);
					}
					if (file_exists('./Public/Home/image/logo/'.$poject_logo)) {
						@unlink('./Public/Home/image/logo/'.$poject_logo);
					}
				}
			}
		}

		$res_delete = M('Result_list')->where("pid = '$report_id'")->delete();
		$sys_delete = M('System_list')->where("pid = '$report_id'")->delete();
		$sys_delete = M('Poject_list')->where("id = '$report_id'")->delete();

		if ($sys_delete) {
			echo 1;exit;
		} else {
			echo 0;exit;
		}


	}

	public function edit_report_info()
	{
		if (IS_POST) {

			$where['id'] 				= I('id','','intval');
		    $data['poject_com'] 		= I('poject_com','','htmlspecialchars,addslashes');
		    $data['temp'] 				= str_replace(".html","",I('report_temp','','htmlspecialchars,addslashes'));
		    $data['poject_name'] 		= I('report_name','','htmlspecialchars,addslashes');
		    $data['poject_start_time'] 	= I('start_time','','strtotime');
		    $data['poject_end_time'] 	= I('end_time','','strtotime');
		    $data['poject_person'] 		= I('person','','htmlspecialchars,addslashes');

			if ($_FILES['logo']['name']) {
			    $upload 			= new \Think\Upload();// 实例化上传类
			    $upload->maxSize   	= 3145728 ;// 设置附件上传大小
			    $upload->exts      	= array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			    $upload->rootPath 	= './Public';
			    $upload->savePath  	= '/Home/image/report/'; // 设置附件上传根目录
			    $upload->saveExt  	= 'jpg'; // 设置附件上传根目录
				$upload->saveName 	= md5(mt_rand() . uniqid() . time());
			    $upload->autoSub 	= false;
			    // 上传文件 
			    $info   			=   $upload->upload();
			    if(!$info) {// 上传错误提示错误信息
			        $this->error($upload->getError());
			    }else{// 上传成功
			    	$data['poject_logo'] = $info['logo']['savename'];
					$poject_logo = M('Poject_list')->where($where)->getField('poject_logo');
					if(file_exists('./Public/Home/image/logo/'.$poject_logo)) {
						@unlink('./Public/Home/image/logo/'.$poject_logo);
					}
			    }
			}
			$update = M('Poject_list')->where($where)->save($data);
			if ($update) {
				$this->success('修改成功',U('Index/index'));
			} else {
				$this->error('修改失败');
			}

		} else {
			$where['id']	= I('id','','intval');
			$report_info 	= M('Poject_list')->field('id,temp,poject_name,poject_com,poject_logo,poject_start_time,poject_end_time,poject_person')->where($where)->find();
			$report_info['poject_start_time'] 	= date('Y-m-d',$report_info['poject_start_time']);
			$report_info['poject_end_time'] 	= date('Y-m-d',$report_info['poject_end_time']);
			$report_info['poject_logo']		 	= __ROOT__.'/Public/Home/image/report/'.$report_info['poject_logo'];
			$report_info['temp'] 				= $report_info['temp'] . '.html';
			$this->assign('report_temp',$this->get_temp());
			$this->assign('report_info',$report_info);
			$this->display();
		}
	}


	public function system_list() 
	{
    	$system_list = D('System_list')->get_system_list();
    	$this->assign('system_list',$system_list['system_list']);
    	$this->assign('count',$system_list['count']);
    	$this->assign('page',$system_list['show']);
    	$this->display();
	}



	public function add_system()
	{
		if (IS_POST) {
		    $data['pid'] 			= I('pid','','intval');
		    $data['system_name'] 	= I('system_name','','htmlspecialchars,addslashes');
		    $data['system_url'] 	= I('system_url','','htmlspecialchars,addslashes');
		    $data['system_ip'] 		= I('system_ip','','htmlspecialchars,addslashes');
		    $data['add_time'] 		= time();
		    $add_system 			= M('system_list')->add($data);
		    if ($add_system) {
		    	$this->success('添加目标系统成功',U('Index/system_list',array('id'=>$data['pid'])));
		    } else {
		    	$this->error('添加目标系统失败');
		    }
		} else {
			$this->display();
		}
	}

	public function edit_system_info()
	{
		if (IS_POST) {
			$where['id'] 			= I('id','','intval');
		    $pid 					= I('pid','','intval');
		    $data['system_name'] 	= I('system_name','','htmlspecialchars,addslashes');
		    $data['system_url'] 	= I('system_url','','htmlspecialchars,addslashes');
		    $data['system_ip'] 		= I('system_ip','','htmlspecialchars,addslashes');

		    $update = M('System_list')->where($where)->save($data);
		    if ($update) {
		    	$this->success('修改成功',U('Index/system_list',array('id'=>$pid)));
		    } else {
		    	$this->error('修改失败');
		    }

		} else {
			$where['id'] 	= I('id','','intval');
	    	$system_info = M('System_list')->field('id,pid,system_name,system_url,system_ip')->where($where)->find();
	    	$this->assign('system_info',$system_info);
	    	$this->display();
		}
	}

	public function del_system()
	{
		$id 			= I('id','','intval');
		$sys_res_list 	= M('System_list as sys')->field("sys.id,res.bug_img")->join("we_result_list AS res ON sys.id = res.sid")->where("sys.id = '$id'")->select();
		foreach ($sys_res_list as $key => $val) {
			if ($val['bug_img']) {
				$bug_img 	= explode(",",$val['bug_img']);
				foreach ($bug_img as $key => $val) {
					if(file_exists('./Public/Home/image/bug/'.$val)) {
						@unlink('./Public/Home/image/bug/'.$val);
					}
				}
			}
		}

		$res_delete = M('Result_list')->where("sid = '$id'")->delete();
		$delete = M('System_list')->where("id = '$id'")->delete();

		if ($delete) {
			echo 1;exit;
		} else {
			echo 0;exit;
		}
	}

	public function bug_list()
	{
    	$bug_list = D('Result_list')->get_bug_list();
    	$this->assign('bug_list',$bug_list['bug_list']);
    	$this->assign('count',$bug_list['count']);
    	$this->assign('page',$bug_list['show']);
    	$this->display();
	}

	public function add_bug()
	{
		if (IS_POST) {
		    $upload 			= new \Think\Upload();// 实例化上传类
		    $upload->maxSize   	= 3145728 ;// 设置附件上传大小
		    $upload->exts      	= array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		    $upload->rootPath 	= './Public';
		    $upload->savePath  	= '/Home/image/report/'; // 设置附件上传根目录
		    $upload->saveExt  	= 'jpg'; // 设置附件上传根目录
			// $upload->saveName ='time';
		    $upload->autoSub 	= false;
		    // 上传文件 
		    $info   			= $upload->upload($_FILES);
		    if(!$info) {// 上传错误提示错误信息
		        $this->error($upload->getError());
		    }else{// 上传成功
		    	$data['bug_img'] = '';
		    	foreach ($info as $key => $val) {
		    		$data['bug_img'] .= $val['savename'] . ',';
		    	}
		    	$data['bug_img'] = rtrim($data['bug_img'],',');
		    }
		    $data['bid'] 		= I('bid','','intval');
		    $data['pid'] 		= I('pid','','intval');
		    $data['sid'] 		= I('sid','','intval');
		    $data['bug_level'] 	= I('bug_level','','intval');
		    $data['bug_url'] 	= I('bug_url','','addslashes');
		    $data['bug_param']	= I('bug_param','','addslashes');
		    $data['add_time'] 	= time();
		    $add_system 		= M('result_list')->add($data);

		    if ($add_system) {
		    	$this->success('添加漏洞成功',U('Index/bug_list',array("pid"=>$data['pid'],"sid"=>$data['sid'])));
		    } else {
		    	$this->error('添加漏洞失败');
		    }
		} else {
			$bug_type_list = M('Bug_list')->field('id,bug_name')->select();
			$this->assign('bug_type_list',$bug_type_list);
			$this->display();
		}
	}

	public function bug_img_list()
	{
		$bug_img_list = D('Result_list')->get_bug_img_list();
		$this->assign('bug_img_list',$bug_img_list['bug_img']);
		$this->assign('result_id',$bug_img_list['result_id']);
		$this->display();
	}

	public function add_bug_img()
	{
		$where['id']		= I('id','','intval');
	    $upload 			= new \Think\Upload();// 实例化上传类
	    $upload->maxSize   	= 3145728 ;// 设置附件上传大小
	    $upload->exts      	= array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    $upload->rootPath 	= './Public';
	    $upload->savePath  	= '/Home/image/report/'; // 设置附件上传根目录
	    $upload->saveExt  	= 'jpg'; // 设置附件上传根目录
	    $upload->autoSub 	= false;
	    $info   			= $upload->upload($_FILES);
	    if(!$info) {// 上传错误提示错误信息
	        $this->error($upload->getError());
	    }else{// 上传成功
	    	$data['bug_img'] = '';
	    	foreach ($info as $key => $val) {
	    		$data['bug_img'] .= $val['savename'] . ',';
	    	}
	    	$data['bug_img'] = rtrim($data['bug_img'],',');
	    }
	    $image_list	= M('Result_list')->field('bug_img')->where($where)->find();

	    if ($image_list['bug_img']) {
	    	$data['bug_img'] = $data['bug_img'] .','. $image_list['bug_img'];
	    	$save 	= M('Result_list')->where($where)->save($data);
	    } else {
	    	$save 	= M('Result_list')->where($where)->save($data);
	    }
    	if ($save) {
    		$this->success("添加成功",U('Index/bug_img_list',array('id'=>$where['id'])));
    	} else {
    		$this->error('添加失败');
    	}
	}

	public function edit_bug_info()
	{
		if (IS_POST) {
		    $where['id'] 		= I('id','','intval');
		    $sid 				= I('sid','','intval');
		    $pid 				= I('pid','','intval');
		    $data['bid'] 		= I('bid','','intval');
		    $data['bug_level'] 	= I('bug_level','','intval');
		    $data['bug_url'] 	= I('bug_url','','addslashes');
		    $data['bug_param']	= I('bug_param','','addslashes');
		    $update 			= M('Result_list')->where($where)->save($data);
		    if ($update) {
		    	$this->success('修改成功',U('Index/bug_list',array('pid'=>$pid,'sid'=>$sid,"id"=>$where['id'])));
		    } else {
		    	$this->error('修改失败');
		    }
		} else {
			$where['id']			= I('id','','intval');
			$bug_type_list			= M('Bug_list')->field('id,bug_name')->select();
			$bug_info 				= M('Result_list')->field('id,pid,sid,bid,bug_level,bug_url,bug_param')->where($where)->find();
			$bug_info['bug_param'] 	= stripslashes(htmlspecialchars($bug_info['bug_param']));
			$bug_info['bug_url'] 	= stripslashes(htmlspecialchars($bug_info['bug_url']));
			$this->assign('bug_info',$bug_info);
			$this->assign('bug_type_list',$bug_type_list);
			$this->display();
		}

	}

	public function del_bug()
	{
		$where['id'] = I('id','','intval');
		$bug_img = M('Result_list')->where($where)->getField('bug_img');
		if ($bug_img) {
			$bug_img = explode(",",$bug_img);
			foreach ($bug_img as $key => $val) {
				if(file_exists('./Public/Home/image/bug/'.$val)) {
					@unlink('./Public/Home/image/bug/'.$val);
				}
			}
		}
		$delete = M('Result_list')->where($where)->delete();
		if ($delete) {
			echo 1;exit;
		} else {
			echo 0;exit;
		}
	}

	public function del_bug_img()
	{
		$where['id']	= I('id','','intval');
		$image_name		= I('img_url','','htmlspecialchars,addslashes');
		$image_name		= trim(strrchr($image_name, '/'),'/');
		$image_ext		= substr($image_name,'-3');
		if ($image_ext != 'jpg') {
			echo 0;
			exit;
		}	

		$image_list		= M('Result_list')->field('bug_img')->where($where)->find();
		$image_list		= explode(",",$image_list['bug_img']);
		if (in_array($image_name,$image_list)) {
			$image_key 	=  array_search($image_name,$image_list);
			unset($image_list[$image_key]);
			$data['bug_img']  = implode(",",$image_list);
			$update		= M('Result_list')->where($where)->save($data);
			if ($update) {
				if(file_exists('./Public/Home/image/bug/'.$image_name)) {
					@unlink('./Public/Home/image/bug/'.$image_name);
					echo 1;exit;
				} else {
					echo 0;
				}
			}
		}

	}

	public function get_temp()
	{
		$dir = "./Application/Home/View/Temp/";  //要获取的目录
	    if (false != ($handle = opendir ( $dir ))) {
	        while ( false !== ($file = readdir ( $handle )) ) {
	            if ($file != "." && $file != ".."&&substr($file,'-4') == 'html') {
	            	$filePath[] = $file;
	            }
	        }
	        closedir ( $handle );
	    }
	    return $filePath;
	}
}