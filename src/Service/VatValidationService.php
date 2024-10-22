<?php

namespace App\Service;

use App\Entity\VatValidationResult;
use Doctrine\ORM\EntityManagerInterface;
use SoapClient;

class VatValidationService
{
    private string $wsdlUrl;
    private EntityManagerInterface $entityManager;

    public function __construct(string $wsdlUrl, EntityManagerInterface $entityManager)
    {
        $this->wsdlUrl = $wsdlUrl;
        $this->entityManager = $entityManager;
    }

    public function validateVat(string $vatNumber): array
    {
        $validationResult = [
            'success' => false,
            'message' => '',
        ];

        if (!$vatNumber) {
            $validationResult['message'] = 'VAT Number is required.';
            return $validationResult;
        } elseif (!preg_match('/^[A-Z]{2}[0-9A-Z]{8,12}$/', strtoupper($vatNumber))) {
            $validationResult['message'] = 'Invalid VAT Number format.';
            return $validationResult;
        }

        try {
            $client = new SoapClient($this->wsdlUrl, ['cache_wsdl' => WSDL_CACHE_NONE]);
        } catch (\SoapFault $e) {
            $validationResult['message'] = 'Error connecting to VAT validation service.';
            return $validationResult;
        }

        $countryCode = substr($vatNumber, 0, 2);
        $vat = substr($vatNumber, 2);

        try {
            $response = $client->checkVat([
                'countryCode' => strtoupper($countryCode),
                'vatNumber' => $vat,
            ]);
        } catch (\Exception $e) {
            $validationResult['message'] = 'An error occurred during VAT validation.';
            return $validationResult;
        }

        if ($response->valid) {
            $message = "Your VAT Number has been successfully validated. Your company legal data was updated according to the VIES database.";

            $vatResult = new VatValidationResult();
            $vatResult->setVatNumber(strtoupper($vatNumber));
            $vatResult->setCompanyName($response->name);
            $vatResult->setAddress($response->address);
            $vatResult->setValidationDate(new \DateTime());

            $this->entityManager->persist($vatResult);
            $this->entityManager->flush();

            $validationResult = [
                'success' => true,
                'message' => $message,
                'company_name' => $response->name,
                'address' => $response->address,
            ];
        } else {
            $validationResult['message'] = "The VAT number you are requesting is not valid. Either the Number is not active or not allocated. Please double-check that you are entering the right VAT Number.";
        }

        return $validationResult;
    }
}
