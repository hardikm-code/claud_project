-- ============================================================
-- Incident Response Portal - Database Schema
-- ============================================================

-- Incident status updates (posted by admin)
CREATE TABLE IF NOT EXISTS `incident_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_label` varchar(50) NOT NULL COMMENT 'e.g. Investigating, Identified, Monitoring, Resolved',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `posted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Lost order claims submitted by customers
CREATE TABLE IF NOT EXISTS `order_claims` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(200) NOT NULL,
  `customer_email` varchar(200) NOT NULL,
  `order_number` varchar(100) DEFAULT NULL,
  `order_amount` decimal(10,2) DEFAULT NULL,
  `order_time` varchar(100) DEFAULT NULL COMMENT 'Approximate time order was placed',
  `description` text NOT NULL,
  `resolution_status` enum('pending','reviewing','resolved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed initial incident timeline
INSERT INTO `incident_updates` (`status_label`, `title`, `message`, `posted_at`) VALUES
('Resolved', 'Service Fully Restored', 'All systems are operating normally. The website is fully restored and accepting orders. We sincerely apologise for the disruption and are committed to ensuring this does not happen again.', '2026-03-07 03:15:00'),
('Monitoring', 'Fix Applied — Monitoring Systems', 'Our engineering team has applied an emergency fix and restored the database connection pool. We are actively monitoring all services to ensure stability.', '2026-03-07 02:55:00'),
('Identified', 'Root Cause Identified', 'We have identified the root cause as an exhausted database connection pool triggered by a sudden spike in sale traffic. Our team is applying a targeted fix now.', '2026-03-07 02:20:00'),
('Investigating', 'Investigating Website Outage', 'We are aware that the website is currently unavailable. Our engineering team has been paged and is actively investigating. We apologise for the inconvenience and will provide updates every 30 minutes.', '2026-03-06 23:55:00');
