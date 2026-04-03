<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260403150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add marketplace Aid market lines and responsible user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE aid_marche ADD COLUMN responsable_id INTEGER DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_1B47E9E2B5D7A1C7 ON aid_marche (responsable_id)');
        $this->addSql('CREATE TABLE aid_marche_ligne (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, marche_id INTEGER NOT NULL, lot_id INTEGER NOT NULL, quantite_amenes INTEGER NOT NULL, quantite_vendus INTEGER NOT NULL, CONSTRAINT FK_8F0C3A8D3D1E6B2E FOREIGN KEY (marche_id) REFERENCES aid_marche (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8F0C3A8D47E6B2C4 FOREIGN KEY (lot_id) REFERENCES aid_lot (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8F0C3A8D3D1E6B2E ON aid_marche_ligne (marche_id)');
        $this->addSql('CREATE INDEX IDX_8F0C3A8D47E6B2C4 ON aid_marche_ligne (lot_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE aid_marche_ligne');
    }
}
