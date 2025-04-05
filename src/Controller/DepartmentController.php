<?php

namespace App\Controller;
use App\Entity\Department;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\DepartmentRepository;
use App\Repository\SalesRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Type\DepartmentType;

final class DepartmentController extends AbstractController
{

    private $registry;
    private $departmentRepository;
    private $logger;
    private $salesRepository;
    public function __construct(ManagerRegistry $registry, DepartmentRepository $departmentRepository, LoggerInterface $logger, SalesRepository $salesRepository)
    {
        $this->registry = $registry;
        $this->departmentRepository = $departmentRepository;
        $this->salesRepository = $salesRepository;
        $this->logger = $logger;
    }

    #[Route('/', name: 'app_department')]
    public function index(Request $request): Response
    {
        $departments = $this->departmentRepository->findAll();
        $departmentsData = array_map(function ($department) {
            return [
                'id' => $department->getId(),
                'name' => $department->getName(),
            ];
        }, $departments);
        $date = new \DateTime($request->query->get('month') ?: 'now');
        $twoMonthPrior = clone $date;
        $twoMonthPrior->sub(new \DateInterval('P2M'));

        $monthPrior = clone $date;
        $monthPrior->sub(new \DateInterval('P1M'));

        $sales = $this->salesRepository->findSalesByDateRange($twoMonthPrior, $monthPrior, $date);    
        $this->logger->info('Sales: ' . $this->json($sales));
        return $this->render('department/department.html.twig', [
            'departments' => $departmentsData,
            'sales' => $sales,
            'month' => $date->format('Y-m'),
        ]);
    }

    #[Route('/department', name: 'app_department_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $name = $request->request->get('name');
        $department = new Department();
        $department->setName($name);

        $entityManager = $this->registry->getManager();
        $entityManager->persist($department);
        $entityManager->flush();
        return $this->redirectToRoute('app_department');
    }

    #[Route('/department/{id}', name: 'app_department_update', methods: ['GET', 'POST'])]
    public function edit(Department $department, Request $request): Response
    {
        $form = $this->createForm(DepartmentType::class, $department);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->registry->getManager();
                $entityManager->flush();

                return $this->redirectToRoute('app_department');
            }
        }
        $sales = $this->departmentRepository->findSalesByDepartment($department->getId());
        $this->logger->info('Sales: ' . $this->json($sales));
        return $this->render('department/edit.html.twig', [
            'form' => $form->createView(),
            'department' => $department,
            'sales' => $sales,
        ]);
    }
 
    #[Route('/department/delete/{id}', name: 'app_department_delete', methods: ['GET'])]
    public function delete(Department $department): Response
    {
        $entityManager = $this->registry->getManager();
        $entityManager->remove($department);
        $entityManager->flush();

        return $this->redirectToRoute('app_department');
    }
}
