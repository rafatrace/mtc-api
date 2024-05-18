<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516124323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__log AS SELECT id, version_id, type, text FROM log');
        $this->addSql('DROP TABLE log');
        $this->addSql('CREATE TABLE log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, version_id INTEGER NOT NULL, type VARCHAR(30) NOT NULL, text CLOB DEFAULT NULL, CONSTRAINT FK_8F3F68C54BBC2705 FOREIGN KEY (version_id) REFERENCES version (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO log (id, version_id, type, text) SELECT id, version_id, type, text FROM __temp__log');
        $this->addSql('DROP TABLE __temp__log');
        $this->addSql('CREATE INDEX IDX_8F3F68C54BBC2705 ON log (version_id)');
        $this->addSql('ALTER TABLE user ADD COLUMN name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD COLUMN avatar CLOB DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE log ADD COLUMN name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE log ADD COLUMN avatar CLOB DEFAULT NULL');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, roles, password FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO user (id, email, roles, password) SELECT id, email, roles, password FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }
}
