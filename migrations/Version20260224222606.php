<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260224222606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animal (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE announcement (id INT AUTO_INCREMENT NOT NULL, address VARCHAR(255) NOT NULL, visit_hours JSON DEFAULT NULL, care_type VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, visit_per_day INT DEFAULT NULL, renumeration_min DOUBLE PRECISION NOT NULL, renumeration_max DOUBLE PRECISION DEFAULT NULL, services VARCHAR(255) DEFAULT NULL, pet_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4DB9D91C966F7FB6 (pet_id), INDEX IDX_4DB9D91CA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, owner_id INT NOT NULL, gardien_id INT NOT NULL, postulation_id INT NOT NULL, INDEX IDX_8A8E26E97E3C61F9 (owner_id), INDEX IDX_8A8E26E936F5263E (gardien_id), UNIQUE INDEX UNIQ_8A8E26E9D749FDF1 (postulation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, date DATE NOT NULL, heure VARCHAR(255) NOT NULL, addresse VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, target_pet_type VARCHAR(50) DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_3BAE0AA7A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE event_participants (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9C7A7A6171F7E88B (event_id), INDEX IDX_9C7A7A61A76ED395 (user_id), PRIMARY KEY (event_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, conversation_id INT NOT NULL, sender_id INT NOT NULL, INDEX IDX_B6BD307F9AC0396 (conversation_id), INDEX IDX_B6BD307FF624B39D (sender_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, type VARCHAR(50) NOT NULL, action_url VARCHAR(255) DEFAULT NULL, priority VARCHAR(20) NOT NULL, is_read TINYINT NOT NULL, read_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CADA46F46 (is_read), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE pet (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, birth_date DATE NOT NULL, type_pet VARCHAR(255) NOT NULL, breed VARCHAR(255) NOT NULL, weight DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, has_contagious_disease TINYINT NOT NULL, has_medical_record TINYINT NOT NULL, has_critical_condition TINYINT NOT NULL, is_vaccinated TINYINT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, owner_id INT NOT NULL, INDEX IDX_E4529B857E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE postulation (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, applicant_id INT NOT NULL, announcement_id INT NOT NULL, owner_id INT NOT NULL, INDEX IDX_DA7D4E9B97139001 (applicant_id), INDEX IDX_DA7D4E9B913AEA17 (announcement_id), INDEX IDX_DA7D4E9B7E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, sujet VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_reclamation DATETIME DEFAULT NULL, statut VARCHAR(50) DEFAULT NULL, priorite VARCHAR(50) NOT NULL, nom_client VARCHAR(100) NOT NULL, email_client VARCHAR(180) NOT NULL, user_id INT NOT NULL, INDEX IDX_CE606404A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, date_reponse DATETIME NOT NULL, auteur VARCHAR(100) NOT NULL, reclamation_id INT NOT NULL, INDEX IDX_5FB6DEC72D6BA2D9 (reclamation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, telephone VARCHAR(20) NOT NULL, adresse LONGTEXT DEFAULT NULL, role VARCHAR(50) NOT NULL, is_active TINYINT NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91C966F7FB6 FOREIGN KEY (pet_id) REFERENCES pet (id)');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91CA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E97E3C61F9 FOREIGN KEY (owner_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E936F5263E FOREIGN KEY (gardien_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9D749FDF1 FOREIGN KEY (postulation_id) REFERENCES postulation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A6171F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A61A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pet ADD CONSTRAINT FK_E4529B857E3C61F9 FOREIGN KEY (owner_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE postulation ADD CONSTRAINT FK_DA7D4E9B97139001 FOREIGN KEY (applicant_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE postulation ADD CONSTRAINT FK_DA7D4E9B913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id)');
        $this->addSql('ALTER TABLE postulation ADD CONSTRAINT FK_DA7D4E9B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC72D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91C966F7FB6');
        $this->addSql('ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91CA76ED395');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E97E3C61F9');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E936F5263E');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9D749FDF1');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7A76ED395');
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A6171F7E88B');
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A61A76ED395');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE pet DROP FOREIGN KEY FK_E4529B857E3C61F9');
        $this->addSql('ALTER TABLE postulation DROP FOREIGN KEY FK_DA7D4E9B97139001');
        $this->addSql('ALTER TABLE postulation DROP FOREIGN KEY FK_DA7D4E9B913AEA17');
        $this->addSql('ALTER TABLE postulation DROP FOREIGN KEY FK_DA7D4E9B7E3C61F9');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A76ED395');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC72D6BA2D9');
        $this->addSql('DROP TABLE animal');
        $this->addSql('DROP TABLE announcement');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_participants');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE pet');
        $this->addSql('DROP TABLE postulation');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE utilisateurs');
    }
}
