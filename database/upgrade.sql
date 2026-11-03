USE fashionhub;

DROP PROCEDURE IF EXISTS ftdp_upgrade;

DELIMITER $$

CREATE PROCEDURE ftdp_upgrade()
BEGIN

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'notifications' AND column_name = 'type'
    ) THEN
        ALTER TABLE notifications
            ADD COLUMN type VARCHAR(30) NOT NULL DEFAULT 'general' AFTER user_id;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'notifications' AND column_name = 'link'
    ) THEN
        ALTER TABLE notifications
            ADD COLUMN link VARCHAR(255) DEFAULT NULL AFTER message;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'jobs' AND column_name = 'status'
    ) THEN
        ALTER TABLE jobs
            ADD COLUMN status ENUM('open','closed') NOT NULL DEFAULT 'open' AFTER deadline;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'notifications' AND index_name = 'idx_notifications_user_read'
    ) THEN
        CREATE INDEX idx_notifications_user_read ON notifications (user_id, is_read, created_at);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'messages' AND index_name = 'idx_messages_pair'
    ) THEN
        CREATE INDEX idx_messages_pair ON messages (sender_id, receiver_id, id);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'messages' AND index_name = 'idx_messages_receiver_unread'
    ) THEN
        CREATE INDEX idx_messages_receiver_unread ON messages (receiver_id, is_read, sender_id);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'jobs' AND index_name = 'idx_jobs_company_status'
    ) THEN
        CREATE INDEX idx_jobs_company_status ON jobs (company_id, status);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'job_applications' AND index_name = 'idx_applications_job_status'
    ) THEN
        CREATE INDEX idx_applications_job_status ON job_applications (job_id, application_status, applied_at);
    END IF;

END$$

DELIMITER ;

CALL ftdp_upgrade();

DROP PROCEDURE ftdp_upgrade;
