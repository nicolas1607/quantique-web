<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220105124900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, num_tva VARCHAR(255) DEFAULT NULL, siret VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contract (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, website_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, promotion DOUBLE PRECISION DEFAULT NULL, INDEX IDX_E98F2859C54C8C93 (type_id), INDEX IDX_E98F285918F45C82 (website_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, company_id INT NOT NULL, released_at DATETIME NOT NULL, files VARCHAR(255) NOT NULL, file VARCHAR(255) NOT NULL, INDEX IDX_90651744C54C8C93 (type_id), INDEX IDX_90651744979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, contract_id INT NOT NULL, message VARCHAR(4600) NOT NULL, released_at DATETIME NOT NULL, INDEX IDX_CFBDFA142576E0FD (contract_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_contract (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, lib VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_invoice (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, nb_connection INT DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_company (user_id INT NOT NULL, company_id INT NOT NULL, INDEX IDX_17B21745A76ED395 (user_id), INDEX IDX_17B21745979B1AD6 (company_id), PRIMARY KEY(user_id, company_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE website (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_476F5DE7979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859C54C8C93 FOREIGN KEY (type_id) REFERENCES type_contract (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F285918F45C82 FOREIGN KEY (website_id) REFERENCES website (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744C54C8C93 FOREIGN KEY (type_id) REFERENCES type_invoice (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA142576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
        $this->addSql('ALTER TABLE user_company ADD CONSTRAINT FK_17B21745A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_company ADD CONSTRAINT FK_17B21745979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE website ADD CONSTRAINT FK_476F5DE7979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744979B1AD6');
        $this->addSql('ALTER TABLE user_company DROP FOREIGN KEY FK_17B21745979B1AD6');
        $this->addSql('ALTER TABLE website DROP FOREIGN KEY FK_476F5DE7979B1AD6');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA142576E0FD');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F2859C54C8C93');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744C54C8C93');
        $this->addSql('ALTER TABLE user_company DROP FOREIGN KEY FK_17B21745A76ED395');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F285918F45C82');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE type_contract');
        $this->addSql('DROP TABLE type_invoice');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_company');
        $this->addSql('DROP TABLE website');
    }
}
