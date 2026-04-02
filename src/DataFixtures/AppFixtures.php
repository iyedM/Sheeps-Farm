<?php

namespace App\DataFixtures;

use App\Entity\CommerceAchat;
use App\Entity\CommerceVente;
use App\Entity\Depense;
use App\Entity\FactureAchat;
use App\Entity\FactureVente;
use App\Entity\Grange;
use App\Entity\Infos;
use App\Entity\Mouton;
use App\Entity\SousLotAchat;
use App\Entity\User;
use App\Entity\Vaccin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $admin = (new User())
            ->setNom('Admin')
            ->setPrenom('Ferme')
            ->setEmail('admin@ferme.tn')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin123!'));
        $manager->persist($admin);

        foreach ([['Employe', 'Un', 'employe1@ferme.tn'], ['Employe', 'Deux', 'employe2@ferme.tn']] as $e) {
            $user = (new User())
                ->setNom($e[0])
                ->setPrenom($e[1])
                ->setEmail($e[2])
                ->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'User123!'));
            $manager->persist($user);
        }

        $granges = [];
        foreach ([['Grange Nord', 'Bizerte'], ['Grange Sud', 'Sfax'], ['Grange Est', 'Nabeul']] as $g) {
            $grange = (new Grange())->setNom($g[0])->setLocalisation($g[1]);
            $manager->persist($grange);
            $granges[] = $grange;
        }

        $races = ['Barbarine', 'Sicilo-Sarde', 'Queue Fine', "D'man"];
        $moutons = [];
        for ($i = 0; $i < 25; ++$i) {
            $m = (new Mouton())
                ->setRace($faker->randomElement($races))
                ->setGenre($faker->randomElement([Mouton::GENRE_MALE, Mouton::GENRE_FEMELLE]))
                ->setOrigine($faker->randomElement([Mouton::ORIGINE_INTERNE, Mouton::ORIGINE_EXTERNE]))
                ->setAgeInitialMois($faker->numberBetween(4, 72))
                ->setDateAjout(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-24 months', 'now')))
                ->setPrix((float) $faker->randomFloat(2, 150, 1200))
                ->setEstVendu($faker->boolean(25))
                ->setGrange($faker->randomElement($granges));
            $manager->persist($m);
            $moutons[] = $m;

            foreach (range(1, $faker->numberBetween(2, 4)) as $_) {
                $v = (new Vaccin())
                    ->setNom($faker->randomElement(['Clavelée', 'Brucellose', 'Fièvre aphteuse']))
                    ->setDateVaccination(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')))
                    ->setMouton($m);
                $manager->persist($v);
            }
            foreach (range(1, $faker->numberBetween(1, 3)) as $_) {
                $info = (new Infos())
                    ->setDescription($faker->sentence())
                    ->setDateAjout(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')))
                    ->setMouton($m);
                $manager->persist($info);
            }
        }

        foreach (range(1, 10) as $_) {
            $dep = (new Depense())
                ->setDescription($faker->randomElement(['Alimentation', 'Transport', 'Vétérinaire', 'Entretien']))
                ->setMontant((float) $faker->randomFloat(2, 80, 1000))
                ->setDate(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-8 months', 'now')));
            $manager->persist($dep);
        }

        foreach (range(1, 5) as $_) {
            $a = (new CommerceAchat())
                ->setFournisseur($faker->company())
                ->setNumeroFournisseur($faker->numerify('FRN-###'))
                ->setRace($faker->randomElement($races))
                ->setQuantite($faker->numberBetween(2, 20))
                ->setPrixUnitaire((float) $faker->randomFloat(2, 120, 600))
                ->setDateAchat(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-8 months', 'now')));
            $a->computePrixTotal();
            $manager->persist($a);
        }

        foreach (range(1, 2) as $_) {
            $fa = (new FactureAchat())
                ->setFournisseur($faker->company())
                ->setDateAchat(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));

            foreach (range(1, 3) as $_i) {
                $s = (new SousLotAchat())
                    ->setRace($faker->randomElement($races))
                    ->setAge($faker->numberBetween(4, 36))
                    ->setGenre($faker->randomElement([Mouton::GENRE_MALE, Mouton::GENRE_FEMELLE]))
                    ->setPrix((float) $faker->randomFloat(2, 120, 900))
                    ->setQuantite($faker->numberBetween(1, 8))
                    ->setGrange($faker->randomElement($granges));
                $fa->addSousLot($s);
            }
            $fa->computeTotalGlobal();
            $manager->persist($fa);
        }

        foreach (range(1, 5) as $_) {
            $v = (new CommerceVente())
                ->setClient($faker->name())
                ->setNumeroClient($faker->numerify('CL-###'))
                ->setRace($faker->randomElement($races))
                ->setQuantite($faker->numberBetween(1, 10))
                ->setPrixUnitaire((float) $faker->randomFloat(2, 250, 900))
                ->setPrixAdditionnel((float) $faker->randomFloat(2, 0, 200))
                ->setDateVente(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-4 months', 'now')));
            $v->computePrixTotal();
            $manager->persist($v);
        }

        $notSold = array_values(array_filter($moutons, static fn (Mouton $m) => !$m->isEstVendu()));
        for ($i = 0; $i < 2; ++$i) {
            $fv = (new FactureVente())
                ->setClient($faker->name())
                ->setNumeroClient($faker->numerify('FC-###'))
                ->setPrixAdditionnel((float) $faker->randomFloat(2, 0, 200))
                ->setDateVente(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 months', 'now')));

            $selected = array_slice($notSold, $i * 2, 2);
            $total = $fv->getPrixAdditionnel();
            foreach ($selected as $m) {
                $m->setEstVendu(true);
                $fv->addMouton($m);
                $total += $m->getPrix();
            }
            $fv->setMontantTotal($total);
            $manager->persist($fv);
        }

        $manager->flush();
    }
}
