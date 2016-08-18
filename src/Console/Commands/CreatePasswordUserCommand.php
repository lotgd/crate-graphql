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
 * CreatePasswordUserCommand
 * @author sauterb
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
        
        $userManager = new UserManagerService($this->game);      
        $userManager->createNewWithPassword($name, $email, $password);
    }
}
