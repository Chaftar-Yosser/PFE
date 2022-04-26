<?php

// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Entity\Contrat;
use App\Entity\Leave;
use App\Entity\SuiviLeave;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-leave',
    description: 'Creates a new user.',
    aliases: ['app:update-leave'],
    hidden: false
)]
class SuiviCongeCommand extends Command
{

    private $repository;
    private $em;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        parent::__construct();
        $this->em = $em;
        $this->repository = $userRepository;
    }

// the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:update-leave';

    protected function configure(): void
    {
// ...
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $users = $this->repository->findAll();
        foreach ($users as $user ) {
            $name = $user->getLastname();
            $contrats = $this->em->getRepository(Contrat::class)->findBy(["user" => $user]);
            foreach ($contrats as $contrat) {
                $contrat->getTypeContrat()->getName();

                $start_date = date_create($contrat->getDateDebut()->format("d/m/Y"));
                $end_date = date_create($contrat->getDateFin()->format("d/m/Y"));

                $interval = \DateInterval::createFromDateString('1 month');
                $daterange = new \DatePeriod($start_date, $interval, $end_date);

                foreach ($daterange as $date1) {
                    echo $date1->format('Y-m-d') . '<br>';
                    $params = ["user" => $user ,
                        "mois" => $date1->format('m') , "annees" =>$date1->format('Y') ];
                    $suiviCurrent = $this->em->getRepository(SuiviLeave::class)->findOneBy($params);
//                    dd($suiviCurrent, $params);
                    if ($suiviCurrent)
                        continue;

                    $dateStart = clone $date1;
                    $dateStart->modify('first day of this month');
                    $dateStart->setTime(0,0,0);
                    $dateEnd = clone $date1;
                    $dateEnd->modify('last day of this month');

                    $dateEnd ->setTime(23,59,59);
//                    dump($dateStart , $dateEnd);

                    $leaves = $this->em->getRepository(Leave::class)->findLeaveBy($user,$dateStart , $dateEnd);
//                    dd($leaves);
                    $pris = 0;
                    foreach ($leaves as $leave){
                        //get total pris
                        $end = $leave->getEndDate();
                        $pris += $end->diff($leave->getStartDate())->format("%a") ;
                    }
//                    dd($pris);
//                    dd(count($leaves));
                    $previousMonth = clone $date1 ;
                    $previousMonth->modify('-1 month') ;
                    $suivi = $this->em->getRepository(SuiviLeave::class)->findOneBy(["user" => $user ,
                        "mois" => $previousMonth->format('m') , "annees" =>$previousMonth->format('Y') ]);

                    $maxJourMois = $contrat->getTypeContrat()->getMaxJours();

                    $oldRest = $suivi ? $suivi->getRestant() : 0;
                    $total = $oldRest + $maxJourMois;

                    $newSuivi = new SuiviLeave();
                        $newSuivi->setUser($user)
                            ->setAnnees($date1->format('Y'))
                            ->setMois($date1->format('m'))
                            ->setTotal($total)
                            ->setPris($pris)
                            ->setRestant($total - $pris)
                        ;

                    $output->writeln([$name . "=> " . $newSuivi->getMois(). "=> " . $newSuivi->getAnnees()
                        . "=> " . $newSuivi->getTotal() . "=> " .  $newSuivi->getRestant() . "=> " .  $newSuivi->getPris()]);


                    $this->em->persist($newSuivi);
                    $this->em->flush();
//                    dd($suivi);
                }
                echo '<br>';

                $output->writeln([$name . "=> " . $contrat->getTypeContrat()->getName()]);

            }
        }
        return Command::SUCCESS;
    }
}