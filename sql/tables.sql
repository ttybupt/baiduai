CREATE TABLE tblBdAITask (
  id int NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
  createTime int(10) unsigned COMMENT 'Create Time',
  updateTime int(10) unsigned COMMENT 'Update Time',
  taskId VARCHAR(255) COMMENT '',
  taskName VARCHAR(255) COMMENT '',
  contentFile VARCHAR(255) COMMENT '',
  detailFile VARCHAR(255) COMMENT '',
  uid bigint(20) unsigned COMMENT '',
  uname VARCHAR(255) COMMENT '',
  extFlag int(10) unsigned COMMENT '',
  extData TEXT COMMENT '',
  UNIQUE KEY          taskid_ctime            (taskId,createTime),
  KEY                 ctime                   (createTime)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COMMENT '百度ai语音任务表';

