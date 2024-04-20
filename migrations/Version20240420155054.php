<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240420155054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_followings (user_id INT NOT NULL, following_user_id INT NOT NULL, PRIMARY KEY(user_id, following_user_id))');
        $this->addSql('CREATE INDEX IDX_851B05A8A76ED395 ON user_followings (user_id)');
        $this->addSql('CREATE INDEX IDX_851B05A81896F387 ON user_followings (following_user_id)');
        $this->addSql('ALTER TABLE user_followings ADD CONSTRAINT FK_851B05A8A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_followings ADD CONSTRAINT FK_851B05A81896F387 FOREIGN KEY (following_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d649a8b65994');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d64915bf9993');
        $this->addSql('DROP INDEX idx_8d93d64915bf9993');
        $this->addSql('DROP INDEX idx_8d93d649a8b65994');
        $this->addSql('ALTER TABLE "user" DROP followings_id');
        $this->addSql('ALTER TABLE "user" DROP followers_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_followings DROP CONSTRAINT FK_851B05A8A76ED395');
        $this->addSql('ALTER TABLE user_followings DROP CONSTRAINT FK_851B05A81896F387');
        $this->addSql('DROP TABLE user_followings');
        $this->addSql('ALTER TABLE "user" ADD followings_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD followers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d649a8b65994 FOREIGN KEY (followings_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d64915bf9993 FOREIGN KEY (followers_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8d93d64915bf9993 ON "user" (followers_id)');
        $this->addSql('CREATE INDEX idx_8d93d649a8b65994 ON "user" (followings_id)');
    }
}
