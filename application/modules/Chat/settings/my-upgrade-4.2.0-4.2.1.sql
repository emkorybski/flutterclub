
ALTER TABLE `engine4_chat_whispers` ADD KEY `recipient_deleted` (`recipient_deleted`, `sender_deleted`);

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`) VALUES
('Chat Data Maintenance', 'chat', 'Chat_Plugin_Task_Cleanup', 60);
