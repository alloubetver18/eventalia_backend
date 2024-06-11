<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240523152035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, answers_id INT DEFAULT NULL, event_related_id INT NOT NULL, created_at DATETIME NOT NULL, content VARCHAR(255) NOT NULL, INDEX IDX_5F9E962AB03A8386 (created_by_id), INDEX IDX_5F9E962A79BF1BCE (answers_id), INDEX IDX_5F9E962A198C192E (event_related_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE common_users (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_933B9E23A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, eventcreated_by_id INT NOT NULL, place_id INT DEFAULT NULL, event_name VARCHAR(255) NOT NULL, fromdate DATE NOT NULL, todate DATE NOT NULL, startat TIME DEFAULT NULL, description VARCHAR(1000) NOT NULL, price DOUBLE PRECISION DEFAULT NULL, image_event LONGBLOB NOT NULL, image_eventformat VARCHAR(255) NOT NULL, INDEX IDX_5387574A772EBDC (eventcreated_by_id), INDEX IDX_5387574ADA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events_users (events_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_A43F6DCF9D6A1065 (events_id), INDEX IDX_A43F6DCF67B3B43D (users_id), PRIMARY KEY(events_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events_themes (events_id INT NOT NULL, themes_id INT NOT NULL, INDEX IDX_C7FF0BEB9D6A1065 (events_id), INDEX IDX_C7FF0BEB94F4A9D2 (themes_id), PRIMARY KEY(events_id, themes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organizations (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, organization_name VARCHAR(50) NOT NULL, organization_description VARCHAR(500) NOT NULL, organization_webpage VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_427C1C7FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE places (id INT AUTO_INCREMENT NOT NULL, placename VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, province VARCHAR(255) NOT NULL, webpage VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE themes (id INT AUTO_INCREMENT NOT NULL, denomination VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, nick VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, rol INT NOT NULL, avatarimage LONGBLOB NOT NULL, avatarimageformat VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_themes (users_id INT NOT NULL, themes_id INT NOT NULL, INDEX IDX_1AA5BC4E67B3B43D (users_id), INDEX IDX_1AA5BC4E94F4A9D2 (themes_id), PRIMARY KEY(users_id, themes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A79BF1BCE FOREIGN KEY (answers_id) REFERENCES comments (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A198C192E FOREIGN KEY (event_related_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE common_users ADD CONSTRAINT FK_933B9E23A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A772EBDC FOREIGN KEY (eventcreated_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574ADA6A219 FOREIGN KEY (place_id) REFERENCES places (id)');
        $this->addSql('ALTER TABLE events_users ADD CONSTRAINT FK_A43F6DCF9D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_users ADD CONSTRAINT FK_A43F6DCF67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_themes ADD CONSTRAINT FK_C7FF0BEB9D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_themes ADD CONSTRAINT FK_C7FF0BEB94F4A9D2 FOREIGN KEY (themes_id) REFERENCES themes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organizations ADD CONSTRAINT FK_427C1C7FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users_themes ADD CONSTRAINT FK_1AA5BC4E67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_themes ADD CONSTRAINT FK_1AA5BC4E94F4A9D2 FOREIGN KEY (themes_id) REFERENCES themes (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AB03A8386');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A79BF1BCE');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A198C192E');
        $this->addSql('ALTER TABLE common_users DROP FOREIGN KEY FK_933B9E23A76ED395');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A772EBDC');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574ADA6A219');
        $this->addSql('ALTER TABLE events_users DROP FOREIGN KEY FK_A43F6DCF9D6A1065');
        $this->addSql('ALTER TABLE events_users DROP FOREIGN KEY FK_A43F6DCF67B3B43D');
        $this->addSql('ALTER TABLE events_themes DROP FOREIGN KEY FK_C7FF0BEB9D6A1065');
        $this->addSql('ALTER TABLE events_themes DROP FOREIGN KEY FK_C7FF0BEB94F4A9D2');
        $this->addSql('ALTER TABLE organizations DROP FOREIGN KEY FK_427C1C7FA76ED395');
        $this->addSql('ALTER TABLE users_themes DROP FOREIGN KEY FK_1AA5BC4E67B3B43D');
        $this->addSql('ALTER TABLE users_themes DROP FOREIGN KEY FK_1AA5BC4E94F4A9D2');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE common_users');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE events_users');
        $this->addSql('DROP TABLE events_themes');
        $this->addSql('DROP TABLE organizations');
        $this->addSql('DROP TABLE places');
        $this->addSql('DROP TABLE themes');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_themes');
    }
}
