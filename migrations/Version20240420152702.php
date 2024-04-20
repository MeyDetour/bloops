<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240420152702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_user DROP CONSTRAINT fk_f7129a803ad8644e');
        $this->addSql('ALTER TABLE user_user DROP CONSTRAINT fk_f7129a80233d34c1');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('ALTER TABLE "user" ADD followings_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD followers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649A8B65994 FOREIGN KEY (followings_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D64915BF9993 FOREIGN KEY (followers_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D649A8B65994 ON "user" (followings_id)');
        $this->addSql('CREATE INDEX IDX_8D93D64915BF9993 ON "user" (followers_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, PRIMARY KEY(user_source, user_target))');
        $this->addSql('CREATE INDEX idx_f7129a80233d34c1 ON user_user (user_target)');
        $this->addSql('CREATE INDEX idx_f7129a803ad8644e ON user_user (user_source)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT fk_f7129a803ad8644e FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT fk_f7129a80233d34c1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649A8B65994');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D64915BF9993');
        $this->addSql('DROP INDEX IDX_8D93D649A8B65994');
        $this->addSql('DROP INDEX IDX_8D93D64915BF9993');
        $this->addSql('ALTER TABLE "user" DROP followings_id');
        $this->addSql('ALTER TABLE "user" DROP followers_id');
    }
}
