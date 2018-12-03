<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Exception\InvalidArgumentException as InvalidArgumentConsoleException;
use App\Exception\InvalidArgumentException;
use Symfony\Component\Stopwatch\Stopwatch;
use App\Utils\UserCreator;
use App\Utils\Validator;

/**
 * Class CreateUserCommand.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class CreateAdminCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:create-admin';

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var UserCreator
     */
    private $userCreator;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * CreateAdminCommand constructor.
     *
     * @param UserCreator $userCreator
     * @param Validator   $validator
     */
    public function __construct(UserCreator $userCreator, Validator $validator)
    {
        parent::__construct();

        $this->userCreator = $userCreator;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates admins and stores them in the database')
            ->setHelp($this->getCommandHelp())
            ->addArgument('username', InputArgument::OPTIONAL, 'The username of the new admin')
            ->addArgument('password', InputArgument::OPTIONAL, 'The plain password of the new admin')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the new admin')
            ->addArgument('full_name', InputArgument::OPTIONAL, 'The full name of the new admin')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (null !== $input->getArgument('username')
            && null !== $input->getArgument('password')
            && null !== $input->getArgument('email')
            && null !== $input->getArgument('full_name')) {
            return;
        }

        $this->io->title('Create Admin Command Interactive Wizard');
        $this->io->text([
            'If you prefer not to use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:create-admin username password email full_name',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // Ask for the username if it's not defined
        $username = $input->getArgument('username');
        if (null !== $username) {
            $this->io->text(' > <info>Username</info>: '.$username);
        } else {
            $username = $this->io->ask('Username', null, [$this->validator, 'validateUsername']);
            $input->setArgument('username', $username);
        }

        // Ask for the password if it's not defined
        $password = $input->getArgument('password');
        if (null !== $password) {
            $this->io->text(' > <info>Password</info>: '.str_repeat('*', mb_strlen($password)));
        } else {
            $password = $this->io->askHidden('Password (your type will be hidden)', [$this->validator, 'validatePassword']);
            $input->setArgument('password', $password);
        }

        // Ask for the email if it's not defined
        $email = $input->getArgument('email');
        if (null !== $email) {
            $this->io->text(' > <info>Email</info>: '.$email);
        } else {
            $email = $this->io->ask('Email', null, [$this->validator, 'validateEmail']);
            $input->setArgument('email', $email);
        }

        // Ask for the full name if it's not defined
        $fullName = $input->getArgument('full_name');
        if (null !== $fullName) {
            $this->io->text(' > <info>Full Name</info>: '.$fullName);
        } else {
            $fullName = $this->io->ask('Full Name', null, [$this->validator, 'validateFullName']);
            $input->setArgument('full_name', $fullName);
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws InvalidArgumentConsoleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('create-admin-command');

        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('password');
        $email = $input->getArgument('email');
        $fullName = $input->getArgument('full_name');

        try {
            $admin = $this->userCreator->createAdmin($username, $plainPassword, $email, $fullName);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentConsoleException($e->getMessage(), $e->getCode());
        }

        $this->io->success(sprintf(
            'The admin has been successfully created: %s (%s)',
            $admin->getUsername(),
            $admin->getEmail()));

        $event = $stopwatch->stop('create-admin-command');
        if ($output->isVerbose()) {
            $this->io->comment(sprintf(
                'New user database id: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB',
                $admin->getId(),
                $event->getDuration(),
                $event->getMemory() / (1024 ** 2)));
        }
    }

    /**
     * @return string
     */
    private function getCommandHelp(): string
    {
        return <<<'HELP'
The <info>%command.name%</info> command creates new admins and saves them in the database:

  <info>php %command.full_name%</info> <comment>username password email full_name</comment>

If you omit any of the three required arguments, the command will ask you to
provide the missing values.
HELP;
    }
}
