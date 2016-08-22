<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Console\Commands;

use Symfony\Component\Console\ {
    Input\InputDefinition,
    Input\InputOption,
    Input\InputInterface,
    Output\OutputInterface
};

use LotGD\Core\Console\Command\BaseCommand;

use LotGD\Crate\GraphQL\Services\UserManagerService;

/**
 * Command to create a password user.
 */
class CreatePasswordUserCommand extends BaseCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('crate:user:add')
            ->setDescription('Adds a user.')
            ->setDefinition(
               new InputDefinition(array(
                   new InputOption('username', 'u', InputOption::VALUE_REQUIRED),
                   new InputOption('email', 'e', InputOption::VALUE_REQUIRED),
                   new InputOption('password', 'p', InputOption::VALUE_REQUIRED),
               ))
           );
    }
    
    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption('username');
        $email = $input->getOption('email');
        $password = $input->getOption('password');
        
        if ($name === null || $email === null || $password === null) {
            $output->write("Name, email and password are not allowed to be null.");
        }
        else {
            $userManager = new UserManagerService($this->game);      
            $user = $userManager->createNewWithPassword($name, $email, $password);

            $output->write(sprintf("User created with id %i", $user->getId()));
        }
        
        $output->write("\n\n");
    }
}
