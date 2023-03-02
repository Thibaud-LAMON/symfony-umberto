<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230226000617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE branches (id INT AUTO_INCREMENT NOT NULL, universes_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_D760D16FB4C7E38F (universes_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ideas (id INT AUTO_INCREMENT NOT NULL, branches_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_1DB2F1DEF05BDCFC (branches_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, users_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_5C93B3A467B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE snippets (id INT AUTO_INCREMENT NOT NULL, ideas_id INT NOT NULL, snippet VARCHAR(255) NOT NULL, truncated VARCHAR(255) DEFAULT NULL, INDEX IDX_ED21F5DCC99EDF4B (ideas_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE suggestions (id INT AUTO_INCREMENT NOT NULL, ideas_id INT NOT NULL, suggestion VARCHAR(255) NOT NULL, INDEX IDX_91B68614C99EDF4B (ideas_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE universes (id INT AUTO_INCREMENT NOT NULL, projects_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_4DDC3E101EDE0F55 (projects_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE branches ADD CONSTRAINT FK_D760D16FB4C7E38F FOREIGN KEY (universes_id) REFERENCES universes (id)');
        $this->addSql('ALTER TABLE ideas ADD CONSTRAINT FK_1DB2F1DEF05BDCFC FOREIGN KEY (branches_id) REFERENCES branches (id)');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A467B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE snippets ADD CONSTRAINT FK_ED21F5DCC99EDF4B FOREIGN KEY (ideas_id) REFERENCES ideas (id)');
        $this->addSql('ALTER TABLE suggestions ADD CONSTRAINT FK_91B68614C99EDF4B FOREIGN KEY (ideas_id) REFERENCES ideas (id)');
        $this->addSql('ALTER TABLE universes ADD CONSTRAINT FK_4DDC3E101EDE0F55 FOREIGN KEY (projects_id) REFERENCES projects (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE branches DROP FOREIGN KEY FK_D760D16FB4C7E38F');
        $this->addSql('ALTER TABLE ideas DROP FOREIGN KEY FK_1DB2F1DEF05BDCFC');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A467B3B43D');
        $this->addSql('ALTER TABLE snippets DROP FOREIGN KEY FK_ED21F5DCC99EDF4B');
        $this->addSql('ALTER TABLE suggestions DROP FOREIGN KEY FK_91B68614C99EDF4B');
        $this->addSql('ALTER TABLE universes DROP FOREIGN KEY FK_4DDC3E101EDE0F55');
        $this->addSql('DROP TABLE branches');
        $this->addSql('DROP TABLE ideas');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE snippets');
        $this->addSql('DROP TABLE suggestions');
        $this->addSql('DROP TABLE universes');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
