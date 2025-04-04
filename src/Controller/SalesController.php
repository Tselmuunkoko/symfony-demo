<?php

namespace App\Controller;

use App\Entity\Department;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Sales;

final class SalesController extends AbstractController
{
    private $registry;
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    #[Route('/sales', name: 'app_sales', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $sales = $this->registry->getRepository(Sales::class)->findAll();
        return $this->json($sales);
    }

    #[Route('/sales/{id}', name: 'app_sales_show')]
    public function show(int $id): JsonResponse
    {
        $sales = $this->registry->getRepository(Sales::class)->find($id);

        if (!$sales) {
            return $this->json(['error' => 'Sales not found'], 404);
        }

        return $this->json($sales);
    }
    #[Route('/sales', name: 'app_sales_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['department_id'])) {
            return $this->json(['error' => 'Department name is required'], 400);
        }
        if (!isset($data['amount'])) {
            return $this->json(['error' => 'Amount is required'], 400);
        }
        if (!isset($data['date'])) {
            return $this->json(['error' => 'Date is required'], 400);
        }
        $sales = new Sales();
        $sales->setAmount($data['amount']);
        $sales->setMonth(new \DateTime($data['date']));
        $department = $this->registry->getRepository(Department::class)->find($data['department_id']);
        if (!$department) {
            return $this->json(['error' => 'Department not found'], 404);
        }
        $sales->setDepartment($department);

        $entityManager = $this->registry->getManager();
        $entityManager->persist($sales);
        $entityManager->flush();

        return $this->json($sales, 201);
    }

    #[Route('/sales/department/{departmentId}', name: 'app_sales_by_department')]
    public function getSalesByDepartment(int $departmentId): JsonResponse
    {
        $sales = $this->registry->getRepository(Sales::class)->findBy(['department_id' => $departmentId]);

        if (!$sales) {
            return $this->json(['error' => 'No sales found for this department'], 404);
        }
        return $this->json($sales);
    }
}

