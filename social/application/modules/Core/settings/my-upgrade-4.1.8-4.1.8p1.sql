
ALTER TABLE `engine4_core_jobs`
  CHANGE COLUMN `state` `state` enum('pending','active','sleeping','failed','cancelled','completed','timeout') NOT NULL default 'pending' ;
