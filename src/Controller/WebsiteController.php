<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Website;
use App\Form\WebsiteType;
use App\Entity\TypeContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebsiteController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/website/add/{company}", name="add_website")
     */
    public function add(Request $request, Company $company): Response
    {
        $website = new Website();
        $addWebsiteForm = $this->createForm(WebsiteType::class, $website);
        $addWebsiteForm->handleRequest($request);

        if ($addWebsiteForm->isSubmitted() && $addWebsiteForm->isValid()) {
            $website = $addWebsiteForm->getData();
            $company->addWebsite($website);

            $this->em->persist($website);
            $this->em->persist($company);
            $this->em->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('website/add.html.twig', [
            'add_website_form' => $addWebsiteForm->createView()
        ]);
    }

    /**
     * @Route("/website/edit/{website}", name="edit_website")
     */
    public function edit(Request $request, Website $website): Response
    {
        $updateWebsiteForm = $this->createForm(WebsiteType::class, $website);
        $updateWebsiteForm->handleRequest($request);

        if ($updateWebsiteForm->isSubmitted() && $updateWebsiteForm->isValid()) {
            $this->em->flush();
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        return $this->render('website/edit.html.twig', [
            'edit_website_form' => $updateWebsiteForm->createView(),
            'website' => $website,
        ]);
    }

    /**
     * @Route("/website/show/{website}", name="show_website")
     */
    public function show(Website $website): Response
    {
        $types = $this->em->getRepository(TypeContract::class)->findAll();
        return $this->render('website/show.html.twig', [
            'website' => $website,
            'types' => $types
        ]);
    }
}
