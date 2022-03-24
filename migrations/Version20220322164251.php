<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220322164251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE conversation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, last_message_id INTEGER DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8A8E26E9BA0E79C3 ON conversation (last_message_id)');
        $this->addSql('CREATE INDEX last_message_id_index ON conversation (last_message_id)');
        $this->addSql('CREATE TABLE message (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, conversation_id INTEGER DEFAULT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_B6BD307FA76ED395 ON message (user_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F9AC0396 ON message (conversation_id)');
        $this->addSql('CREATE TABLE participant (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, conversation_id INTEGER DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_D79F6B11A76ED395 ON participant (user_id)');
        $this->addSql('CREATE INDEX IDX_D79F6B119AC0396 ON participant (conversation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE participant');
    }
}
