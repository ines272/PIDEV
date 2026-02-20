<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin')]
class CreateAdminCommand extends Command
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Crée un administrateur par défaut');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>🚀 Création de l\'administrateur...</info>');

        // Vérifier si l'admin existe déjà
        $admin = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@sitmypet.com']);
        
        if ($admin) {
            $output->writeln('<comment>⚠️  L\'administrateur existe déjà !</comment>');
            $output->writeln('📧 Email: admin@sitmypet.com');
            return Command::SUCCESS;
        }

        // Créer l'admin
        $user = new User();
        $user->setEmail('admin@sitmypet.com');
        $user->setNom('Admin');
        $user->setPrenom('Super');
        $user->setTelephone('20123456');
        $user->setAdresse('Tunis, Tunisie');
        $user->setRole('ROLE_ADMIN');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsActive(true);
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'Admin@1234');
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('<info>✅ Administrateur créé avec succès !</info>');
        $output->writeln('📧 Email: admin@sitmypet.com');
        $output->writeln('🔑 Mot de passe: Admin@1234');

        return Command::SUCCESS;
    }
}