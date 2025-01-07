<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\CompanyProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-company-profile',
    description: 'Crée un profil entreprise pour un utilisateur',
)]
class CreateCompanyProfileCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur')
            ->addArgument('company_name', InputArgument::REQUIRED, 'Nom de l\'entreprise');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $companyName = $input->getArgument('company_name');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $output->writeln('<error>Utilisateur non trouvé</error>');
            return Command::FAILURE;
        }

        if ($user->getCompanyProfile()) {
            $output->writeln('<error>L\'utilisateur a déjà un profil entreprise</error>');
            return Command::FAILURE;
        }

        $profile = new CompanyProfile();
        $profile->setUser($user);
        $profile->setCompanyName($companyName);
        $profile->setLocation('À définir');
        $profile->setDescription('Description à venir');
        $profile->setWebsite('https://example.com');
        $profile->setIndustry('Technologie');
        $profile->setCompanySize(1);
        $user->setCompanyProfile($profile);
        $user->setRoles(['ROLE_COMPANY']);

        $this->entityManager->persist($profile);
        $this->entityManager->flush();

        $output->writeln('<info>Le profil entreprise a été créé avec succès</info>');
        return Command::SUCCESS;
    }
}
