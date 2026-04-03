<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260403100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Marketplace Aïd tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE aid_campagne (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE aid_lot (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, campagne_id INTEGER NOT NULL, quantite INTEGER NOT NULL, age_mois INTEGER NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, CONSTRAINT FK_B5B5D6A786D9F3F FOREIGN KEY (campagne_id) REFERENCES aid_campagne (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B5B5D6A786D9F3F ON aid_lot (campagne_id)');
        $this->addSql('CREATE TABLE aid_depense (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, campagne_id INTEGER NOT NULL, libelle VARCHAR(180) NOT NULL, montant NUMERIC(10, 2) NOT NULL, date DATE NOT NULL, CONSTRAINT FK_2B7C4A786D9F3F FOREIGN KEY (campagne_id) REFERENCES aid_campagne (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2B7C4A786D9F3F ON aid_depense (campagne_id)');
        $this->addSql('CREATE TABLE aid_marche (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, campagne_id INTEGER NOT NULL, nom VARCHAR(180) NOT NULL, date DATE NOT NULL, moutons_amenes INTEGER NOT NULL, moutons_vendus INTEGER NOT NULL, reduction NUMERIC(10, 2) DEFAULT NULL, CONSTRAINT FK_8D8B2E786D9F3F FOREIGN KEY (campagne_id) REFERENCES aid_campagne (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8D8B2E786D9F3F ON aid_marche (campagne_id)');
        $this->addSql('CREATE TABLE aid_marche_lot (aid_marche_id INTEGER NOT NULL, aid_lot_id INTEGER NOT NULL, PRIMARY KEY(aid_marche_id, aid_lot_id), CONSTRAINT FK_BD64F3C7A7D3A7BA FOREIGN KEY (aid_marche_id) REFERENCES aid_marche (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BD64F3C7B3B21E0 FOREIGN KEY (aid_lot_id) REFERENCES aid_lot (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BD64F3C7A7D3A7BA ON aid_marche_lot (aid_marche_id)');
        $this->addSql('CREATE INDEX IDX_BD64F3C7B3B21E0 ON aid_marche_lot (aid_lot_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE aid_marche_lot');
        $this->addSql('DROP TABLE aid_marche');
        $this->addSql('DROP TABLE aid_depense');
        $this->addSql('DROP TABLE aid_lot');
        $this->addSql('DROP TABLE aid_campagne');
    }
}
