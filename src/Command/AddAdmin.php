<?php
namespace App\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Admin;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AddAdmin extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('admin:add')->setDescription('Add new admin')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name?')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $admin = new Admin();
        $admin->setUsername($input->getArgument('name'));
//        $encoded = $encoder->encodePassword($admin, $input->getArgument('password'));
        $admin->setPassword(password_hash($input->getArgument('password'), PASSWORD_DEFAULT));
//        $admin->setPassword($input->getArgument('password'));
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($admin);
        $em->flush();
        $output->writeln("Admin added!");
    }
}