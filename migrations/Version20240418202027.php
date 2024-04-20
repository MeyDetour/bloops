<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418202027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE audio ADD display_comment BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE comment ADD audio_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C3A3123C7 FOREIGN KEY (audio_id) REFERENCES audio (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9474526C3A3123C7 ON comment (audio_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE audio DROP display_comment');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C3A3123C7');
        $this->addSql('DROP INDEX IDX_9474526C3A3123C7');
        $this->addSql('ALTER TABLE comment DROP audio_id');
    }
}
