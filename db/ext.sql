#2014-11-12
alter table `ifengcms`.`cms_vote` add column `remoteurl` varchar (250)  NULL  COMMENT '外部服务地址'  after `enum`, add column `displayurl` varchar (250)  NULL  COMMENT '外部展示地址'  after `remoteurl`;


#2014-12-18
alter table `ifengcms`.`cms_vote_source` add column `source_pic_ext` varchar (255)  NULL  COMMENT '图片扩展信息'  after `source_pic`,change `source_pic` `source_pic` varchar (32) CHARACTER SET utf8  COLLATE utf8_general_ci   NULL;

create table `ifengcms`.`cms_vote_members_ext` (  `id` int (10) NOT NULL AUTO_INCREMENT , `mid` int , `type` varchar (20) , `data` varchar (250) , PRIMARY KEY (`id`));

