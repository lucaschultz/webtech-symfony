<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250727121326 extends AbstractMigration {
  public function getDescription(): string {
    return "";
  }

  public function up(Schema $schema): void {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql(
      "CREATE TABLE task (id SERIAL NOT NULL, team_id INT NOT NULL, created_by_id INT NOT NULL, assigned_to_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, status VARCHAR(255) NOT NULL, priority VARCHAR(255) NOT NULL, deadline TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))"
    );
    $this->addSql("CREATE INDEX IDX_527EDB25296CD8AE ON task (team_id)");
    $this->addSql("CREATE INDEX IDX_527EDB25B03A8386 ON task (created_by_id)");
    $this->addSql("CREATE INDEX IDX_527EDB25F4BD7827 ON task (assigned_to_id)");
    $this->addSql(
      "ALTER TABLE task ADD CONSTRAINT FK_527EDB25296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE"
    );
    $this->addSql(
      'ALTER TABLE task ADD CONSTRAINT FK_527EDB25B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
    );
    $this->addSql(
      'ALTER TABLE task ADD CONSTRAINT FK_527EDB25F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
    );
  }

  public function down(Schema $schema): void {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql("CREATE SCHEMA public");
    $this->addSql("ALTER TABLE task DROP CONSTRAINT FK_527EDB25296CD8AE");
    $this->addSql("ALTER TABLE task DROP CONSTRAINT FK_527EDB25B03A8386");
    $this->addSql("ALTER TABLE task DROP CONSTRAINT FK_527EDB25F4BD7827");
    $this->addSql("DROP TABLE task");
  }
}
