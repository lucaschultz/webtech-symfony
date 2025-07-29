<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250729092005 extends AbstractMigration {
  public function getDescription(): string {
    return "";
  }

  public function up(Schema $schema): void {
    // 1. Assign the first member of each team as the owner (PostgreSQL syntax).
    $this->addSql('
        UPDATE team t
        SET created_by_id = (
            SELECT user_id
            FROM user_team ut
            WHERE ut.team_id = t.id
            ORDER BY user_id ASC
            LIMIT 1
        )
        WHERE created_by_id IS NULL
    ');

    // 2. Make the column NOT NULL
    $this->addSql("ALTER TABLE team ALTER COLUMN created_by_id SET NOT NULL");
  }

  public function down(Schema $schema): void {
    $this->addSql("ALTER TABLE team ALTER COLUMN created_by_id DROP NOT NULL");
  }
}
