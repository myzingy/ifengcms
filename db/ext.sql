#2014-11-12
alter table `ifengcms`.`cms_vote` add column `remoteurl` varchar (250)  NULL  COMMENT '外部服务地址'  after `enum`, add column `displayurl` varchar (250)  NULL  COMMENT '外部展示地址'  after `remoteurl`;


#2014-12-18
alter table `ifengcms`.`cms_vote_source` add column `source_pic_ext` varchar (255)  NULL  COMMENT '图片扩展信息'  after `source_pic`,change `source_pic` `source_pic` varchar (32) CHARACTER SET utf8  COLLATE utf8_general_ci   NULL;

create table `ifengcms`.`cms_vote_members_ext` (  `id` int (10) NOT NULL AUTO_INCREMENT , `mid` int , `type` varchar (20) , `data` varchar (250) , PRIMARY KEY (`id`));


#2015-03-02
alter table `ifengcms`.`cms_fields` add column `type` tinyint (1) DEFAULT '0' NULL  COMMENT '默认0普通表单，1为答题'  after `fields_style_mobile`

#2015-03-06
alter table `ifengcms`.`cms_fields` change `fields_html` `fields_html` longtext   NULL , change `fields_htmls` `fields_htmls` longtext   NULL  COMMENT '发布的html' 

#2015-03-13
alter table `ifengcms`.`cms_draw_history` add column `type` tinyint (1) DEFAULT '0' NULL  COMMENT '1测试，0正式'  after `addtime`;


#2015-04-15
alter table `ifengcms`.`yidong_devices` add column `pinpai` varchar (20)  NULL  COMMENT '品牌'  after `status`, add column `xinghao` varchar (20)  NULL  COMMENT '型号'  after `pinpai`, add column `danshuangka` varchar (20)  NULL  COMMENT '单双卡'  after `xinghao`, add column `pingmu` varchar (20)  NULL  COMMENT '屏幕'  after `danshuangka`, add column `neicun` varchar (20)  NULL  COMMENT '内存'  after `pingmu`, add column `heshu` varchar (20)  NULL  COMMENT '核数'  after `neicun`, add column `zhijiangjia` varchar (20)  NULL  COMMENT '直降价'  after `heshu`, add column `bianma` varchar (20)  NULL  COMMENT '编码'  after `zhijiangjia`

