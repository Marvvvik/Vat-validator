<?php

namespace App\Controller;

use App\Entity\VatValidationResult;
use Doctrine\ORM\EntityManagerInterface;
use SoapClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VatValidationController extends AbstractController
{
    #[Route('/', name: 'validate_vat', methods: ['POST'])]
    public function validateVat(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vatNumber = $request->request->get('vat_number');

        if (!$vatNumber) {
            return $this->render('vat_validation/index.html.twig', [
                'validation_result' => [
                    'success' => false,
                    'message' => 'VAT Number is required.',
                ],
            ]);
        }

        if (!preg_match('/^[A-Z]{2}[0-9A-Z]{8,12}$/', strtoupper($vatNumber))) {
            return $this->render('vat_validation/index.html.twig', [
                'validation_result' => [
                    'success' => false,
                    'message' => 'Invalid VAT Number format.',
                ],
            ]);
        }

        $wsdl = 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

        try {
            $client = new SoapClient($wsdl, ['cache_wsdl' => WSDL_CACHE_NONE]);
        } catch (\SoapFault $e) {
            return $this->render('vat_validation/index.html.twig', [
                'validation_result' => [
                    'success' => false,
                    'message' => 'Error connecting to VAT validation service.',
                ],
            ]);
        }

        $countryCode = substr($vatNumber, 0, 2);
        $vat = substr($vatNumber, 2);

        try {
            $response = $client->checkVat([
                'countryCode' => strtoupper($countryCode),
                'vatNumber' => $vat,
            ]);
        } catch (\Exception $e) {
            return $this->render('vat_validation/index.html.twig', [
                'validation_result' => [
                    'success' => false,
                    'message' => 'An error occurred during VAT validation.',
                ],
            ]);
        }

        if ($response->valid) {
            $message = "Your VAT Number has been successfully validated. Your company legal data was updated according to the VIES database.";

            $vatResult = new VatValidationResult();
            $vatResult->setVatNumber(strtoupper($vatNumber));
            $vatResult->setCompanyName($response->name);
            $vatResult->setAddress($response->address);
            $vatResult->setValidationDate(new \DateTime());

            $entityManager->persist($vatResult);
            $entityManager->flush();

            return $this->render('vat_validation/index.html.twig', [
                'validation_result' => [
                    'success' => true,
                    'message' => $message,
                    'company_name' => $response->name,
                    'address' => $response->address,
                ],
            ]);
        } else {
            $message = "The VAT number you are requesting is not valid. Either the Number is not active or not allocated. Please double-check that you are entering the right VAT Number.";

            return $this->render('vat_validation/index.html.twig', [
                'validation_result' => [
                    'success' => false,
                    'message' => $message,
                ],
            ]);
        }
    }
}
