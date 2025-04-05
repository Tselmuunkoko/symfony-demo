<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Department;
use App\Entity\Sales;

#[AsCommand(
    name: 'app:backfill',
    description: 'fill database with data',
)]
class AppCommandBackfillCommand extends Command
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $departmentsData = [
            'Marketing',
            'Sales',
            'Engineering',
            'HR',
            'financial',
        ];

        foreach ($departmentsData as $name) {
            $existing = $this->em->getRepository(Department::class)->findOneBy(['name' => $name]);
            if (!$existing) {
                $department = new Department();
                $department->setName($name);
                $this->em->persist($department);
                $output->writeln("Created department: $name");
            } else {
                $output->writeln("Skipped existing: $name");
            }
        }

        $this->em->flush();

        $output->writeln('<info>Backfilling Sales...</info>');

        $departments = $this->em->getRepository(Department::class)->findAll();

        foreach ($departments as $department) {
            for ($i = 1; $i <= 10; $i++) {
                $sale = new Sales();
                $sale->setAmount(rand(100, 5000));
                $sale->setMonth(new \DateTime('-' . rand(0, 30) . ' days'));
                $sale->setDepartment($department);
                $this->em->persist($sale);
                $output->writeln("Sale of {$sale->getAmount()} added to {$department->getName()}");
            }
        }

        $this->em->flush();

        $output->writeln('<info>Backfill complete!</info>');

        return Command::SUCCESS;
    }
}
