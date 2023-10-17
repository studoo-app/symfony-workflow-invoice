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
use Symfony\Component\Workflow\WorkflowInterface;

class InvoiceController extends AbstractController
{

    public function __construct(
        // Symfony will inject the 'blog_publishing' workflow configured before
        private readonly WorkflowInterface $invoiceStatusStateMachine,
        private readonly EntityManagerInterface $manager
    ) {
    }

    #[Route('/', name: 'app_invoice')]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoiceRepository->findAll(),
        ]);
    }

    #[Route('/generate', name: 'app_invoice_generate')]
    public function generate(): RedirectResponse
    {
        $faker = Factory::create('fr_FR');

        $invoice = new Invoice();
        $invoice->setDate($faker->dateTimeThisMonth);
        $invoice->setClient($faker->company);
        $invoice->setTotal($faker->randomNumber(4));
        $this->invoiceStatusStateMachine->getMarking($invoice);

        $this->manager->persist($invoice);
        $this->manager->flush();

        $this->addFlash("success","Facture générée avec succès !");

        return $this->redirectToRoute('app_invoice');
    }

    #[Route('/{id}', name: 'app_invoice_details')]
    public function details(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig',["invoice"=>$invoice]);
    }

    #[Route('/{id}/edit', name: 'app_invoice_edit')]
    public function edit(Invoice $invoice){
        if($this->invoiceStatusStateMachine->can($invoice,'to_edit')){
            $this->invoiceStatusStateMachine->apply($invoice,'to_edit');
            $this->manager->persist($invoice);
            $this->manager->flush();
        }

        return $this->redirectToRoute('app_invoice_details',['id'=>$invoice->getId()]);
    }

    #[Route('/{id}/send', name: 'app_invoice_send')]
    public function send(Invoice $invoice){
        if($this->invoiceStatusStateMachine->can($invoice,'to_send')){
            $this->invoiceStatusStateMachine->apply($invoice,'to_send');
            $this->manager->persist($invoice);
            $this->manager->flush();
        }

        return $this->redirectToRoute('app_invoice_details',['id'=>$invoice->getId()]);
    }

    #[Route('/{id}/paid', name: 'app_invoice_paid')]
    public function paid(Invoice $invoice){
        if($this->invoiceStatusStateMachine->can($invoice,'to_paid')){
            $this->invoiceStatusStateMachine->apply($invoice,'to_paid');
            $this->manager->persist($invoice);
            $this->manager->flush();
        }

        return $this->redirectToRoute('app_invoice_details',['id'=>$invoice->getId()]);
    }

    #[Route('/{id}/unpaid', name: 'app_invoice_unpaid')]
    public function unpaid(Invoice $invoice){
        if($this->invoiceStatusStateMachine->can($invoice,'to_unpaid')){
            $this->invoiceStatusStateMachine->apply($invoice,'to_unpaid');
            $this->manager->persist($invoice);
            $this->manager->flush();
        }

        return $this->redirectToRoute('app_invoice_details',['id'=>$invoice->getId()]);
    }

    #[Route('/{id}/cancel', name: 'app_invoice_cancel')]
    public function cancel(Invoice $invoice){
        if($this->invoiceStatusStateMachine->can($invoice,'to_cancel')){
            $this->invoiceStatusStateMachine->apply($invoice,'to_cancel');
            $this->manager->persist($invoice);
            $this->manager->flush();
        }

        return $this->redirectToRoute('app_invoice_details',['id'=>$invoice->getId()]);
    }
}
