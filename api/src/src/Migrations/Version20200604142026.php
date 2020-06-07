<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200604142026 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE traffic_lights (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', rana_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', timeslot VARCHAR(2) NOT NULL, m1 INT DEFAULT NULL, m2 INT DEFAULT NULL, m3 INT DEFAULT NULL, m4 INT DEFAULT NULL, m5 INT DEFAULT NULL, m6 INT DEFAULT NULL, m7 INT DEFAULT NULL, m8 INT DEFAULT NULL, m9 INT DEFAULT NULL, m10 INT DEFAULT NULL, m11 INT DEFAULT NULL, m12 INT DEFAULT NULL, INDEX IDX_DDF89ECF19AB87EB (rana_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE traffic_lights ADD CONSTRAINT FK_DDF89ECF19AB87EB FOREIGN KEY (rana_id) REFERENCES rana (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE traffic_lights');
    }
}
