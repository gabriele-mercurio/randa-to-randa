<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200527090043 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE randa DROP FOREIGN KEY FK_BFC7CD9398260155');
        $this->addSql('ALTER TABLE strategies_per_randa DROP FOREIGN KEY FK_D4336D0FE2403D36');
        $this->addSql('ALTER TABLE strategies_per_randa DROP FOREIGN KEY FK_D4336D0FD5CAD932');
        $this->addSql('ALTER TABLE retentions DROP FOREIGN KEY FK_FF2D7F0E19AB87EB');
        $this->addSql('ALTER TABLE chapters DROP FOREIGN KEY FK_C721437198260155');
        $this->addSql('ALTER TABLE chapters DROP FOREIGN KEY FK_C7214371899FB366');
        $this->addSql('ALTER TABLE renewed_members DROP FOREIGN KEY FK_8001F06F19AB87EB');
        $this->addSql('ALTER TABLE rana DROP FOREIGN KEY FK_68AC01FB579F4768');
        $this->addSql('ALTER TABLE rana DROP FOREIGN KEY FK_68AC01FBE2403D36');
        $this->addSql('ALTER TABLE economics DROP FOREIGN KEY FK_F36D0234E2403D36');
        $this->addSql('ALTER TABLE new_members DROP FOREIGN KEY FK_9EE3811119AB87EB');
        $this->addSql('ALTER TABLE rana_lifecycle DROP FOREIGN KEY FK_A2F12E4719AB87EB');
        $this->addSql('ALTER TABLE directors DROP FOREIGN KEY FK_A6ADADC498260155');
        $this->addSql('ALTER TABLE directors DROP FOREIGN KEY FK_A6ADADC419E9AC5F');
        $this->addSql('ALTER TABLE regions CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE randa CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE regionId regionId CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE strategies_per_randa CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE randa_id randa_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE strategy_id strategy_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE strategies CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE retentions CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE rana_id rana_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE chapters CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE regionId regionId CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE director_id director_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE renewed_members CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE rana_id rana_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rana CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE chapter_id chapter_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE randa_id randa_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE targets CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE economics CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE randa_id randa_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE revenue_costs CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE new_members CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE rana_id rana_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE rana_lifecycle CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE rana_id rana_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE directors CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE regionId regionId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE supervisor_id supervisor_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
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
        $this->addSql('ALTER TABLE directors ADD CONSTRAINT FK_A6ADADC498260155 FOREIGN KEY (regionId) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE directors ADD CONSTRAINT FK_A6ADADC419E9AC5F FOREIGN KEY (supervisor_id) REFERENCES directors (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE randa DROP FOREIGN KEY FK_BFC7CD9398260155');
        $this->addSql('ALTER TABLE strategies_per_randa DROP FOREIGN KEY FK_D4336D0FE2403D36');
        $this->addSql('ALTER TABLE strategies_per_randa DROP FOREIGN KEY FK_D4336D0FD5CAD932');
        $this->addSql('ALTER TABLE retentions DROP FOREIGN KEY FK_FF2D7F0E19AB87EB');
        $this->addSql('ALTER TABLE chapters DROP FOREIGN KEY FK_C721437198260155');
        $this->addSql('ALTER TABLE chapters DROP FOREIGN KEY FK_C7214371899FB366');
        $this->addSql('ALTER TABLE renewed_members DROP FOREIGN KEY FK_8001F06F19AB87EB');
        $this->addSql('ALTER TABLE rana DROP FOREIGN KEY FK_68AC01FB579F4768');
        $this->addSql('ALTER TABLE rana DROP FOREIGN KEY FK_68AC01FBE2403D36');
        $this->addSql('ALTER TABLE economics DROP FOREIGN KEY FK_F36D0234E2403D36');
        $this->addSql('ALTER TABLE new_members DROP FOREIGN KEY FK_9EE3811119AB87EB');
        $this->addSql('ALTER TABLE rana_lifecycle DROP FOREIGN KEY FK_A2F12E4719AB87EB');
        $this->addSql('ALTER TABLE directors DROP FOREIGN KEY FK_A6ADADC498260155');
        $this->addSql('ALTER TABLE directors DROP FOREIGN KEY FK_A6ADADC419E9AC5F');
        $this->addSql('ALTER TABLE chapters CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE regionId regionId INT NOT NULL, CHANGE director_id director_id INT NOT NULL');
        $this->addSql('ALTER TABLE directors CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE regionId regionId INT DEFAULT NULL, CHANGE supervisor_id supervisor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE economics CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE randa_id randa_id INT NOT NULL');
        $this->addSql('ALTER TABLE new_members CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE rana_id rana_id INT NOT NULL');
        $this->addSql('ALTER TABLE rana CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE chapter_id chapter_id INT NOT NULL, CHANGE randa_id randa_id INT NOT NULL');
        $this->addSql('ALTER TABLE rana_lifecycle CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE rana_id rana_id INT NOT NULL');
        $this->addSql('ALTER TABLE randa CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE regionId regionId INT NOT NULL');
        $this->addSql('ALTER TABLE regions CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE renewed_members CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE rana_id rana_id INT NOT NULL');
        $this->addSql('ALTER TABLE retentions CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE rana_id rana_id INT NOT NULL');
        $this->addSql('ALTER TABLE revenue_costs CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE strategies CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE strategies_per_randa CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE randa_id randa_id INT DEFAULT NULL, CHANGE strategy_id strategy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE targets CHANGE id id INT AUTO_INCREMENT NOT NULL');
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
        $this->addSql('ALTER TABLE directors ADD CONSTRAINT FK_A6ADADC498260155 FOREIGN KEY (regionId) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE directors ADD CONSTRAINT FK_A6ADADC419E9AC5F FOREIGN KEY (supervisor_id) REFERENCES directors (id)');
    }
}
