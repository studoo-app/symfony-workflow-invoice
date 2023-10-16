<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    #[Route('/', name: 'app_invoice')]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoiceRepository->findAll(),
        ]);
    }

    #[Route('/generate', name: 'app_invoice_generate')]
    public function generate(EntityManagerInterface $manager): RedirectResponse
    {
        $faker = Factory::create('fr_FR');

        $invoice = new Invoice();
        $invoice->setDate($faker->dateTimeThisMonth);
        $invoice->setClient($faker->company);
        $invoice->setTotal($faker->randomNumber(4));

        $manager->persist($invoice);
        $manager->flush();

        $this->addFlash("success","Facture générée avec succès !");

        return $this->redirectToRoute('app_invoice');
    }

    #[Route('/{id}', name: 'app_invoice_details')]
    public function details(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig',["invoice"=>$invoice]);
    }
}
