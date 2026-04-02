<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260402222942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commerce_achat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fournisseur VARCHAR(150) NOT NULL, numero_fournisseur VARCHAR(50) NOT NULL, race VARCHAR(100) NOT NULL, quantite INTEGER NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, date_achat DATETIME NOT NULL, prix_total DOUBLE PRECISION NOT NULL)');
        $this->addSql('CREATE TABLE commerce_vente (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client VARCHAR(150) NOT NULL, numero_client VARCHAR(50) NOT NULL, race VARCHAR(100) NOT NULL, quantite INTEGER NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, prix_additionnel DOUBLE PRECISION NOT NULL, prix_total DOUBLE PRECISION NOT NULL, date_vente DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE depense (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, description VARCHAR(255) NOT NULL, montant DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE facture_achat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fournisseur VARCHAR(150) NOT NULL, date_achat DATETIME NOT NULL, total_global DOUBLE PRECISION NOT NULL)');
        $this->addSql('CREATE TABLE facture_vente (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client VARCHAR(150) NOT NULL, numero_client VARCHAR(50) NOT NULL, date_vente DATETIME NOT NULL, montant_total DOUBLE PRECISION NOT NULL, prix_additionnel DOUBLE PRECISION NOT NULL)');
        $this->addSql('CREATE TABLE facture_vente_mouton (facture_vente_id INTEGER NOT NULL, mouton_id INTEGER NOT NULL, PRIMARY KEY (facture_vente_id, mouton_id), CONSTRAINT FK_271CE656F3819FB FOREIGN KEY (facture_vente_id) REFERENCES facture_vente (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_271CE6535960C9F FOREIGN KEY (mouton_id) REFERENCES mouton (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_271CE656F3819FB ON facture_vente_mouton (facture_vente_id)');
        $this->addSql('CREATE INDEX IDX_271CE6535960C9F ON facture_vente_mouton (mouton_id)');
        $this->addSql('CREATE TABLE grange (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(120) NOT NULL, localisation VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE infos (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, description CLOB NOT NULL, date_ajout DATETIME NOT NULL, mouton_id INTEGER NOT NULL, CONSTRAINT FK_EECA826D35960C9F FOREIGN KEY (mouton_id) REFERENCES mouton (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_EECA826D35960C9F ON infos (mouton_id)');
        $this->addSql('CREATE TABLE mouton (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, race VARCHAR(100) NOT NULL, genre VARCHAR(20) NOT NULL, age_initial_mois INTEGER NOT NULL, date_ajout DATETIME NOT NULL, prix DOUBLE PRECISION NOT NULL, origine VARCHAR(20) NOT NULL, est_vendu BOOLEAN NOT NULL, grange_id INTEGER NOT NULL, CONSTRAINT FK_59BCF6AE91ABF71B FOREIGN KEY (grange_id) REFERENCES grange (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_59BCF6AE91ABF71B ON mouton (grange_id)');
        $this->addSql('CREATE TABLE sous_lot_achat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, race VARCHAR(100) NOT NULL, age INTEGER NOT NULL, genre VARCHAR(20) NOT NULL, prix DOUBLE PRECISION NOT NULL, quantite INTEGER NOT NULL, grange_id INTEGER NOT NULL, facture_achat_id INTEGER NOT NULL, CONSTRAINT FK_4BE8021791ABF71B FOREIGN KEY (grange_id) REFERENCES grange (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4BE80217EC6ADFE6 FOREIGN KEY (facture_achat_id) REFERENCES facture_achat (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_4BE8021791ABF71B ON sous_lot_achat (grange_id)');
        $this->addSql('CREATE INDEX IDX_4BE80217EC6ADFE6 ON sous_lot_achat (facture_achat_id)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE vaccin (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(120) NOT NULL, date_vaccination DATETIME NOT NULL, mouton_id INTEGER NOT NULL, CONSTRAINT FK_B5DCA0A735960C9F FOREIGN KEY (mouton_id) REFERENCES mouton (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B5DCA0A735960C9F ON vaccin (mouton_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE commerce_achat');
        $this->addSql('DROP TABLE commerce_vente');
        $this->addSql('DROP TABLE depense');
        $this->addSql('DROP TABLE facture_achat');
        $this->addSql('DROP TABLE facture_vente');
        $this->addSql('DROP TABLE facture_vente_mouton');
        $this->addSql('DROP TABLE grange');
        $this->addSql('DROP TABLE infos');
        $this->addSql('DROP TABLE mouton');
        $this->addSql('DROP TABLE sous_lot_achat');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE vaccin');
    }
}
