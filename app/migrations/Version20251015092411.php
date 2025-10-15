<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251015092411 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
        CREATE TABLE reservation (
          id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
          vehicle_id INT NOT NULL,
          customer_email VARCHAR(255)  NOT NULL,
          start_date DATETIME NOT NULL,
          end_date DATETIME NOT NULL,
          pickup_location VARCHAR(128)  NOT NULL,
          drop_off_location VARCHAR(128)  NOT NULL,
          is_custom_drop_off_location TINYINT(1) NOT NULL DEFAULT 0,
          status VARCHAR(16) NOT NULL DEFAULT 'REQUESTED',
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (id),
          INDEX idx_availability (vehicle_id, status, start_date, end_date),
          INDEX idx_customer_created (customer_email, created_at),
          CONSTRAINT chk_pickup_before_return CHECK (start_date < end_date),
          CONSTRAINT chk_length_1_60_days CHECK (TIMESTAMPDIFF(DAY, start_date, end_date) BETWEEN 1 AND 60)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE reservation');
    }
}
