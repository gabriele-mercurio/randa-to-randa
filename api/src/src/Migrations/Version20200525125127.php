<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200525125127 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE randa (id INT AUTO_INCREMENT NOT NULL, regionId INT NOT NULL, year INT NOT NULL, current_timeslot VARCHAR(255) DEFAULT \'T0\' NOT NULL, INDEX IDX_BFC7CD9398260155 (regionId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE regions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, notes LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE strategies_per_randa (id INT AUTO_INCREMENT NOT NULL, randa_id INT DEFAULT NULL, strategy_id INT DEFAULT NULL, INDEX IDX_D4336D0FE2403D36 (randa_id), INDEX IDX_D4336D0FD5CAD932 (strategy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE strategies (id INT AUTO_INCREMENT NOT NULL, target VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, timestap DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE retentions (id INT AUTO_INCREMENT NOT NULL, rana_id INT NOT NULL, value_type VARCHAR(4) NOT NULL, m1 INT DEFAULT NULL, m2 INT DEFAULT NULL, m3 INT DEFAULT NULL, m4 INT DEFAULT NULL, m5 INT DEFAULT NULL, m6 INT DEFAULT NULL, m7 INT DEFAULT NULL, m8 INT DEFAULT NULL, m9 INT DEFAULT NULL, m10 INT DEFAULT NULL, m11 INT DEFAULT NULL, m12 INT DEFAULT NULL, INDEX IDX_FF2D7F0E19AB87EB (rana_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chapters (id INT AUTO_INCREMENT NOT NULL, regionId INT NOT NULL, director_id INT NOT NULL, name VARCHAR(32) NOT NULL, current_state VARCHAR(10) DEFAULT \'PROJECT\' NOT NULL, prev_launch_coregroup_date DATE NOT NULL, actual_launch_coregroup_date DATE NOT NULL, prev_launch_Chapter_date DATE NOT NULL, actual_launch_Chapter_date DATE NOT NULL, susp_date DATE NOT NULL, prev_resume_date DATE NOT NULL, actual_resume_date DATE NOT NULL, closure_date DATE NOT NULL, INDEX IDX_C721437198260155 (regionId), INDEX IDX_C7214371899FB366 (director_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE renewed_members (id INT AUTO_INCREMENT NOT NULL, rana_id INT NOT NULL, timeslot VARCHAR(2) NOT NULL, value_type VARCHAR(4) NOT NULL, m1 INT DEFAULT NULL, m2 INT DEFAULT NULL, m3 INT DEFAULT NULL, m4 INT DEFAULT NULL, m5 INT DEFAULT NULL, m6 INT DEFAULT NULL, m7 INT DEFAULT NULL, m8 INT DEFAULT NULL, m9 INT DEFAULT NULL, m10 INT DEFAULT NULL, m11 INT DEFAULT NULL, m12 INT DEFAULT NULL, INDEX IDX_8001F06F19AB87EB (rana_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rana (id INT AUTO_INCREMENT NOT NULL, chapter_id INT NOT NULL, randa_id INT NOT NULL, UNIQUE INDEX UNIQ_68AC01FB579F4768 (chapter_id), INDEX IDX_68AC01FBE2403D36 (randa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE targets (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE economics (id INT AUTO_INCREMENT NOT NULL, randa_id INT NOT NULL, year INT NOT NULL, timeslot VARCHAR(2) NOT NULL, extra_incomings INT NOT NULL, deprecations INT NOT NULL, provisions INT NOT NULL, financial_charges INT NOT NULL, tax INT NOT NULL, INDEX IDX_F36D0234E2403D36 (randa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE revenue_costs (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(32) NOT NULL, value INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE new_members (id INT AUTO_INCREMENT NOT NULL, rana_id INT NOT NULL, timeslot VARCHAR(2) NOT NULL, value_type VARCHAR(4) NOT NULL, m1 INT DEFAULT NULL, m2 INT DEFAULT NULL, m3 INT DEFAULT NULL, m4 INT DEFAULT NULL, m5 INT DEFAULT NULL, m6 INT DEFAULT NULL, m7 INT DEFAULT NULL, m8 INT DEFAULT NULL, m9 INT DEFAULT NULL, m10 INT DEFAULT NULL, m11 INT DEFAULT NULL, m12 INT DEFAULT NULL, INDEX IDX_9EE3811119AB87EB (rana_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rana_lifecycle (id INT AUTO_INCREMENT NOT NULL, rana_id INT NOT NULL, current_timeslot VARCHAR(2) NOT NULL, current_status VARCHAR(8) DEFAULT \'TODO\' NOT NULL, INDEX IDX_A2F12E4719AB87EB (rana_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_client (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', id_client VARCHAR(255) NOT NULL, secret VARCHAR(255) DEFAULT NULL, redirect_uri VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE directors (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, regionId INT DEFAULT NULL, supervisor_id INT DEFAULT NULL, role VARCHAR(10) NOT NULL, pay_type VARCHAR(8) NOT NULL, launch_percentage DOUBLE PRECISION DEFAULT \'0\' NOT NULL, green_light_percentage DOUBLE PRECISION DEFAULT \'0\' NOT NULL, yellow_light_percentage DOUBLE PRECISION DEFAULT \'0\' NOT NULL, red_light_percentage DOUBLE PRECISION DEFAULT \'0\' NOT NULL, grey_light_percentage DOUBLE PRECISION DEFAULT \'0\' NOT NULL, fixed_percentage DOUBLE PRECISION DEFAULT \'0\' NOT NULL, INDEX IDX_A6ADADC4A76ED395 (user_id), INDEX IDX_A6ADADC498260155 (regionId), INDEX IDX_A6ADADC419E9AC5F (supervisor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_refresh_token (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', token VARCHAR(255) NOT NULL, id_client VARCHAR(255) NOT NULL, id_user VARCHAR(255) NOT NULL, expires DATETIME NOT NULL, scope VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_access_token (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', token VARCHAR(255) NOT NULL, id_client VARCHAR(255) NOT NULL, id_user VARCHAR(255) NOT NULL, expires DATETIME NOT NULL, scope VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE randa ADD CONSTRAINT FK_BFC7CD9398260155 FOREIGN KEY (regionId) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE strategies_per_randa ADD CONSTRAINT FK_D4336D0FE2403D36 FOREIGN KEY (randa_id) REFERENCES targets (id)');
        $this->addSql('ALTER TABLE strategies_per_randa ADD CONSTRAINT FK_D4336D0FD5CAD932 FOREIGN KEY (strategy_id) REFERENCES strategies (id)');
        $this->addSql('ALTER TABLE retentions ADD CONSTRAINT FK_FF2D7F0E19AB87EB FOREIGN KEY (rana_id) REFERENCES rana (id)');
        $this->addSql('ALTER TABLE chapters ADD CONSTRAINT FK_C721437198260155 FOREIGN KEY (regionId) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE chapters ADD CONSTRAINT FK_C7214371899FB366 FOREIGN KEY (director_id) REFERENCES directors (id)');
        $this->addSql('ALTER TABLE renewed_members ADD CONSTRAINT FK_8001F06F19AB87EB FOREIGN KEY (rana_id) REFERENCES rana (id)');
        $this->addSql('ALTER TABLE rana ADD CONSTRAINT FK_68AC01FB579F4768 FOREIGN KEY (chapter_id) REFERENCES chapters (id)');
        $this->addSql('ALTER TABLE rana ADD CONSTRAINT FK_68AC01FBE2403D36 FOREIGN KEY (randa_id) REFERENCES randa (id)');
        $this->addSql('ALTER TABLE economics ADD CONSTRAINT FK_F36D0234E2403D36 FOREIGN KEY (randa_id) REFERENCES randa (id)');
        $this->addSql('ALTER TABLE new_members ADD CONSTRAINT FK_9EE3811119AB87EB FOREIGN KEY (rana_id) REFERENCES rana (id)');
        $this->addSql('ALTER TABLE rana_lifecycle ADD CONSTRAINT FK_A2F12E4719AB87EB FOREIGN KEY (rana_id) REFERENCES rana (id)');
        $this->addSql('ALTER TABLE directors ADD CONSTRAINT FK_A6ADADC4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE directors ADD CONSTRAINT FK_A6ADADC498260155 FOREIGN KEY (regionId) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE directors ADD CONSTRAINT FK_A6ADADC419E9AC5F FOREIGN KEY (supervisor_id) REFERENCES directors (id)');
        $this->addSql('INSERT INTO oauth2_client VALUES(UUID(), "Randa2RandaAppClient", "FwPMFRlCa78GPQrO9zRWVRbjPCoPmaBQP254nx3g", "")');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rana DROP FOREIGN KEY FK_68AC01FBE2403D36');
        $this->addSql('ALTER TABLE economics DROP FOREIGN KEY FK_F36D0234E2403D36');
        $this->addSql('ALTER TABLE randa DROP FOREIGN KEY FK_BFC7CD9398260155');
        $this->addSql('ALTER TABLE chapters DROP FOREIGN KEY FK_C721437198260155');
        $this->addSql('ALTER TABLE directors DROP FOREIGN KEY FK_A6ADADC498260155');
        $this->addSql('ALTER TABLE strategies_per_randa DROP FOREIGN KEY FK_D4336D0FD5CAD932');
        $this->addSql('ALTER TABLE rana DROP FOREIGN KEY FK_68AC01FB579F4768');
        $this->addSql('ALTER TABLE directors DROP FOREIGN KEY FK_A6ADADC4A76ED395');
        $this->addSql('ALTER TABLE retentions DROP FOREIGN KEY FK_FF2D7F0E19AB87EB');
        $this->addSql('ALTER TABLE renewed_members DROP FOREIGN KEY FK_8001F06F19AB87EB');
        $this->addSql('ALTER TABLE new_members DROP FOREIGN KEY FK_9EE3811119AB87EB');
        $this->addSql('ALTER TABLE rana_lifecycle DROP FOREIGN KEY FK_A2F12E4719AB87EB');
        $this->addSql('ALTER TABLE strategies_per_randa DROP FOREIGN KEY FK_D4336D0FE2403D36');
        $this->addSql('ALTER TABLE chapters DROP FOREIGN KEY FK_C7214371899FB366');
        $this->addSql('ALTER TABLE directors DROP FOREIGN KEY FK_A6ADADC419E9AC5F');
        $this->addSql('DROP TABLE randa');
        $this->addSql('DROP TABLE regions');
        $this->addSql('DROP TABLE strategies_per_randa');
        $this->addSql('DROP TABLE strategies');
        $this->addSql('DROP TABLE retentions');
        $this->addSql('DROP TABLE chapters');
        $this->addSql('DROP TABLE renewed_members');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE rana');
        $this->addSql('DROP TABLE targets');
        $this->addSql('DROP TABLE economics');
        $this->addSql('DROP TABLE revenue_costs');
        $this->addSql('DROP TABLE new_members');
        $this->addSql('DROP TABLE rana_lifecycle');
        $this->addSql('DROP TABLE oauth2_client');
        $this->addSql('DROP TABLE directors');
        $this->addSql('DROP TABLE oauth2_refresh_token');
        $this->addSql('DROP TABLE oauth2_access_token');
    }
}
