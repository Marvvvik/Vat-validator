<?php

namespace App\Controller;

use App\Service\VatValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VatValidationController extends AbstractController
{
    private VatValidationService $vatValidationService;

    public function __construct(VatValidationService $vatValidationService)
    {
        $this->vatValidationService = $vatValidationService;
    }

    #[Route('/', name: 'validate_vat', methods: ['POST'])]
    public function validateVat(Request $request): Response
    {
        $vatNumber = $request->request->get('vat_number');
        $validationResult = $this->vatValidationService->validateVat($vatNumber);

        return $this->render('vat_validation/index.html.twig', [
            'validation_result' => $validationResult,
        ]);
    }
}
