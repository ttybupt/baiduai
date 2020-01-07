CREATE TABLE tblBdAITask (
    id                 bigint(20) unsigned     NOT NULL AUTO_INCREMENT     COMMENT '自增主键ID',
    taskId             varchar(200)            NOT NULL DEFAULT 0          COMMENT '百度创建的任务id',
    taskName           varchar(200)            NOT NULL DEFAULT ''         COMMENT '任务备注简介',
    status             tinyint(4) unsigned     NOT NULL DEFAULT 0          COMMENT '任务状态',
    contentFile        varchar(200)            NOT NULL DEFAULT ''         COMMENT '内容文件名',
    detailFile         varchar(200)            NOT NULL DEFAULT ''         COMMENT '详细内容名',
    createTime         bigint(20) unsigned     NOT NULL DEFAULT 0          COMMENT '创建时间',
    updateTime         bigint(20) unsigned     NOT NULL DEFAULT 0          COMMENT '更新时间',
    uid                bigint(20) unsigned     NOT NULL DEFAULT 0          COMMENT '所属用户id',
    uname              varchar(200)            NOT NULL DEFAULT ''         COMMENT '所属用户名',
    extFlag            int(10) unsigned        NOT NULL DEFAULT 0          COMMENT '扩展id',
    extData            varchar(2000)           NOT NULL DEFAULT ''         COMMENT '扩展数据',
    PRIMARY KEY                                (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
COMMENT '百度AI录音转文本任务表';