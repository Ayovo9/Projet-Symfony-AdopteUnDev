<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:user:update-role',
    description: 'Met à jour le rôle d\'un utilisateur',
)]
class UpdateUserRoleCommand extends Command
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
            ->addArgument('role', InputArgument::REQUIRED, 'Nouveau rôle (ROLE_DEVELOPER ou ROLE_COMPANY)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');

        if (!in_array($role, ['ROLE_DEVELOPER', 'ROLE_COMPANY'])) {
            $output->writeln('<error>Le rôle doit être ROLE_DEVELOPER ou ROLE_COMPANY</error>');
            return Command::FAILURE;
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $output->writeln('<error>Utilisateur non trouvé</error>');
            return Command::FAILURE;
        }

        $user->setRoles([$role]);
        $this->entityManager->flush();

        $output->writeln('<info>Le rôle de l\'utilisateur a été mis à jour avec succès</info>');
        return Command::SUCCESS;
    }
}
