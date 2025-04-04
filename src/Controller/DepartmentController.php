<?php

namespace App\Controller;
use App\Entity\Department;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\DepartmentRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

final class DepartmentController extends AbstractController
{

    private $registry;
    private $departmentRepository;
    private $logger;
    public function __construct(ManagerRegistry $registry, DepartmentRepository $departmentRepository, LoggerInterface $logger)
    {
        $this->registry = $registry;
        $this->departmentRepository = $departmentRepository;
        $this->logger = $logger;
    }

    #[Route('/', name: 'app_department')]
    public function index(): Response
    {
        $departments = $this->departmentRepository->findAll();
        $departmentsData = array_map(function ($department) {
            return [
                'id' => $department->getId(),
                'name' => $department->getName(),
            ];
        }, $departments);

        return $this->render('department/department.html.twig', [
            'departments' => $departmentsData]);
    }

    #[Route('/department/{id}', name: 'app_department_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $department = $this->departmentRepository->find($id);

        if (!$department) {
            return $this->json(['error' => 'Department not found'], 404);
        }

        return $this->json($department->getName());
    }

    #[Route('/department', name: 'app_department_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['name'])) {
            return $this->json(['error' => 'Department name is required'], 400);
        }
        $department = new Department();
        $department->setName($data['name']);

        $entityManager = $this->registry->getManager();
        $entityManager->persist($department);
        $entityManager->flush();

        return $this->json($department, 201);
    }

    #[Route('/department/{id}', name: 'app_department_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $department = $this->departmentRepository->find($id);

        if (!$department) {
            return $this->json(['error' => 'Department not found'], 404);
        }
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'])) {
            return $this->json(['error' => 'Department name is required'], 400);
        }
        $department->setName($data['name']);
        $entityManager = $this->registry->getManager();
        $entityManager->flush();
        return $this->json($department->getName());
    }
 
    #[Route('/department/{id}', name: 'app_department_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $department = $this->departmentRepository->find($id);

        if (!$department) {
            return $this->json(['error' => 'Department not found'], 404);
        }

        $entityManager = $this->registry->getManager();
        $entityManager->remove($department);
        $entityManager->flush();

        return $this->json(['message' => 'Department deleted successfully']);
    }

    #[Route('/department/{id}/sales', name: 'app_department_sales_by_date', methods: ['GET'])]
    public function getSalesByDepartmentAndMonth(int $id, Request $request): JsonResponse
    {
        $department = $this->departmentRepository->find($id);
        if (!$department) {
            return $this->json(['error' => 'Department not found'], 404);
        }

        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');
        $this->logger->info('Start date: ' . $startDate);
        $this->logger->info('End date: ' . $endDate);
        if (!$startDate || !$endDate) {
            $sales = $this->departmentRepository->findSalesByDepartment($id);
            $this->logger->info('Sales: ' . $this->json($sales));
            if (!$sales) {
                return $this->json(['error' => 'No sales found for this department'], 404);
            }
            return $this->json($sales);
        }
        $sales = $this->departmentRepository->findSalesByDepartmentIdAndMonth($id, $startDate, $endDate);

        if (!$sales) {
            return $this->json(['error' => 'No sales found for this department in this month'], 404);
        }
        return $this->json($sales);
    }


}
