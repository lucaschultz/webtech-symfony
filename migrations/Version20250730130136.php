<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250730130136 extends AbstractMigration {
  public function getDescription(): string {
    return "";
  }

  public function up(Schema $schema): void {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql(
      "CREATE TABLE team_join_request (id SERIAL NOT NULL, team_id INT NOT NULL, requester_id INT NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))"
    );
    $this->addSql(
      "CREATE INDEX IDX_E1B4E93D296CD8AE ON team_join_request (team_id)"
    );
    $this->addSql(
      "CREATE INDEX IDX_E1B4E93DED442CF4 ON team_join_request (requester_id)"
    );
    $this->addSql(
      "ALTER TABLE team_join_request ADD CONSTRAINT FK_E1B4E93D296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE"
    );
    $this->addSql(
      'ALTER TABLE team_join_request ADD CONSTRAINT FK_E1B4E93DED442CF4 FOREIGN KEY (requester_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
    );
  }

  public function down(Schema $schema): void {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql("CREATE SCHEMA public");
    $this->addSql(
      "ALTER TABLE team_join_request DROP CONSTRAINT FK_E1B4E93D296CD8AE"
    );
    $this->addSql(
      "ALTER TABLE team_join_request DROP CONSTRAINT FK_E1B4E93DED442CF4"
    );
    $this->addSql("DROP TABLE team_join_request");
  }
}
