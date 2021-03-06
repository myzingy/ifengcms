<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class article extends Public_Controller
{
	/**
	 * Constructor
	 */
	function __construct(){
		parent::__construct();
		// Load the Auth_form_processing class
		$this->load->library('articlelib');
		$this->load->helper('article');
	}
	//点赞
	function applaud(){
		$this->articleLib->applaud();
	}
	// 获取文章列表数据
	function getNewsList($classify=1,$limit=5,$page=0){
		$limit=array('offset'=>$page,'limit'=>$limit);
		$where=array('A.status'=>0);
		if($classify==13){
			//互动列表
			$res=$this->article_model->getArticleContentList($where,$limit,false,$classify);
			if($res->num_rows()>0){
				$info['status']=0;
				$info['data']=$res->result();
				foreach ($info['data'] as $i => $r) {
					$info['data'][$i]->src=base_url().$r->src;
					$info['data'][$i]->url=getArticleUrl($r->id,$r->type,$r->url);
					//图集处理
					$info["data"][$i]->thumb='';
					preg_match_all('/<img[^>]+src="([^"]+)"[^>]*>/', $r->content,$match);
					if($match){
						//var_dump($match);
						foreach($match[1] as $x=>$src){
							//$thumb='thumb_'.($x+1);
							//$info["data"][$i]->$thumb=$src;
							$info["data"][$i]->thumb.=('<div class="fl"><img src="'.$src.'"></div>');
							if($x==8) break;
						}
					}
					$r->content=preg_replace('/<[^>]+>/', '', $r->content);
					$r->addtime=date('m-d H:i');
				}
			}else{
				$info['status']=10000;
			}
		}else{
			$res=$this->article_model->getArticleList($where,$limit,false,$classify);
			if($res->num_rows()>0){
				$info['status']=0;
				$info['data']=$res->result();
				foreach ($info['data'] as $i => $r) {
					$info['data'][$i]->src=base_url().$r->src;
					$info['data'][$i]->url=getArticleUrl($r->id,$r->type,$r->url);
					$info['data'][$i]->showtypeblock=getArticleTypeBlock($r->type);
				}
			}else{
				$info['status']=10000;
			}
		}
		
		return $info;
	}
	// 获取新闻列表
	function newsList($classify=1,$limit=5,$page=0){
		$info=$this->getNewsList($classify,$limit,$page);
		die(json_encode($info));
	}
	// 获取首页的列表新闻和新闻图集
	function newsIndexData($classify=1,$limit=5,$page=0){
		$info=$this->getNewsList($classify,$limit,$page);
		//新闻图集 从分类中获取
		/*
		$info['pics']=array();
		$classify=12;
		$limit=array('offset'=>$page,'limit'=>2);
		$where=array('A.type'=>1);
		$res=$this->article_model->getArticleList($where,$limit,false,$classify);
		if($res->num_rows()>0){
			foreach($res->result() as $r){
				$arr=$this->article_model->getArticleAllInfo($r->id);
				$cont=$arr['content'];
				preg_match_all('/<img[^>]+src="([^"]+)"[^>]+>/', $cont,$match);
				
				$thumb=array(base_url().$r->src);
				if($match){
					$thumb=array_merge($thumb,$match[1]);
				}
				$info['pics'][]=array(
					'id'=>$arr['id']
					,'thumb_1'=>$thumb[0]
					,'thumb_2'=>$thumb[1]
					,'thumb_3'=>$thumb[2]?$thumb[2]:$thumb[0]
					,'title'=>$arr['title']
				);	
			}
		}*/
		//新闻图集 从新闻中获取
		if($info['status']==0){
			$offset=ceil($limit/2);
			for($i=1;$i<=2;$i++){
				$p=($offset*$i)-1;
				if($r=$info['data'][$p]){
					$arr=$this->article_model->getArticleAllInfo($r->id);
					$cont=$arr['content'];
					preg_match_all('/<img[^>]+src="([^"]+)"[^>]+>/', $cont,$match);
					$thumb=array($r->src);
					if($match){
						$thumb=array_merge($thumb,$match[1]);
					}
					$info['data'][$p]->thumb_1=$thumb[0];
					$info['data'][$p]->thumb_2=$thumb[1]?$thumb[1]:$thumb[0];
					$info['data'][$p]->thumb_3=$thumb[2]?$thumb[2]:$thumb[0];
				}
			}
		}
		die(json_encode($info));
	}
	//获取内容详细
	function getContent($id=0){
		$info=$this->article_model->getArticleAllInfo($id);
		$info['status']=$info['status']==0?0:10000;
		if($info['status']==0){
			$info['addtime']=date('m-d H:i',$info['addtime']);
			//获取相关新闻
			$info['news']=$this->getNewsList($info['classify'][0],5,0);
			//评论总数
			$res=$this->article_model->fetch('CT','count(*) as count',null,array('aid'=>$info['id']));
			$info['comment']=$res->row()->count;
			//点赞
			$res=$this->article_model->fetch('AA','count(*) as count',null,array('aid'=>$info['id']));
			$info['applaud']=$res->row()->count;
			//收藏
			$res=$this->article_model->fetch('AF','count(*) as count',null,array('aid'=>$info['id']));
			$info['favorites']=$res->row()->count;
			//页内广告
			$info['ads']=$this->getNewsList(10,5);
			
		}
		die(json_encode($info));
	}
}