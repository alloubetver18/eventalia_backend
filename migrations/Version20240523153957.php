<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240523153957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE common_users_common_users (common_users_source INT NOT NULL, common_users_target INT NOT NULL, INDEX IDX_63ED75016840B1F (common_users_source), INDEX IDX_63ED75011F615B90 (common_users_target), PRIMARY KEY(common_users_source, common_users_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE common_users_organizations (common_users_id INT NOT NULL, organizations_id INT NOT NULL, INDEX IDX_97EC8B7028373AA (common_users_id), INDEX IDX_97EC8B7086288A55 (organizations_id), PRIMARY KEY(common_users_id, organizations_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE common_users_common_users ADD CONSTRAINT FK_63ED75016840B1F FOREIGN KEY (common_users_source) REFERENCES common_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE common_users_common_users ADD CONSTRAINT FK_63ED75011F615B90 FOREIGN KEY (common_users_target) REFERENCES common_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE common_users_organizations ADD CONSTRAINT FK_97EC8B7028373AA FOREIGN KEY (common_users_id) REFERENCES common_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE common_users_organizations ADD CONSTRAINT FK_97EC8B7086288A55 FOREIGN KEY (organizations_id) REFERENCES organizations (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE common_users_common_users DROP FOREIGN KEY FK_63ED75016840B1F');
        $this->addSql('ALTER TABLE common_users_common_users DROP FOREIGN KEY FK_63ED75011F615B90');
        $this->addSql('ALTER TABLE common_users_organizations DROP FOREIGN KEY FK_97EC8B7028373AA');
        $this->addSql('ALTER TABLE common_users_organizations DROP FOREIGN KEY FK_97EC8B7086288A55');
        $this->addSql('DROP TABLE common_users_common_users');
        $this->addSql('DROP TABLE common_users_organizations');
    }
}
