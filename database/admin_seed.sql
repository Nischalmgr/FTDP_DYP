INSERT INTO users (full_name, email, password, role, is_verified)
SELECT 'Admin', 'admin@ftdp.com', '$2y$10$oRz2jt8OqsR1wKNi6mLsRebiQI1eNjgqBTLCsQAYM2rT7X4QvP2JG', 'admin', 'Y'
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'admin@ftdp.com'
);
