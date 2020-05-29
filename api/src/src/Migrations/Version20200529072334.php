<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200529072334 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE strategies ADD target_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', DROP target, CHANGE timestap timestamp DATE NOT NULL');
        $this->addSql('ALTER TABLE strategies ADD CONSTRAINT FK_611F2213158E0B66 FOREIGN KEY (target_id) REFERENCES targets (id)');
        $this->addSql('CREATE INDEX IDX_611F2213158E0B66 ON strategies (target_id)');
        $this->addSql('ALTER TABLE chapters CHANGE actual_launch_coregroup_date actual_launch_coregroup_date DATE DEFAULT NULL, CHANGE actual_launch_chatper_date actual_launch_chatper_date DATE DEFAULT NULL, CHANGE susp_date susp_date DATE DEFAULT NULL, CHANGE prev_resume_date prev_resume_date DATE DEFAULT NULL, CHANGE actual_resume_date actual_resume_date DATE DEFAULT NULL, CHANGE closure_date closure_date DATE DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE chapters CHANGE actual_launch_coregroup_date actual_launch_coregroup_date DATE NOT NULL, CHANGE actual_launch_chatper_date actual_launch_chatper_date DATE NOT NULL, CHANGE susp_date susp_date DATE NOT NULL, CHANGE prev_resume_date prev_resume_date DATE NOT NULL, CHANGE actual_resume_date actual_resume_date DATE NOT NULL, CHANGE closure_date closure_date DATE NOT NULL');
        $this->addSql('ALTER TABLE strategies DROP FOREIGN KEY FK_611F2213158E0B66');
        $this->addSql('DROP INDEX IDX_611F2213158E0B66 ON strategies');
        $this->addSql('ALTER TABLE strategies ADD target VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP target_id, CHANGE timestamp timestap DATE NOT NULL');
    }
}
