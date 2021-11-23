<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Contract;
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
     * @Route("/website/add/{company}", name="add_website_with_company")
     */
    public function addForCompany(Request $request, Company $company): Response
    {
        $typesContract = $this->em->getRepository(TypeContract::class)->findAll();

        $website = new Website();
        $addWebsiteForm = $this->createForm(WebsiteType::class, $website, ['method' => 'GET']);
        $addWebsiteForm->handleRequest($request);

        if ($addWebsiteForm->isSubmitted() && $addWebsiteForm->isValid()) {
            $website = $addWebsiteForm->getData();
            $company->addWebsite($website);


            // Liste des contrats ajoutés
            $types = ['vitrine', 'commerce', 'google', 'facebook'];
            foreach ($types as $type) {
                $check = $request->query->get($type . '-check');
                if ($check == 'on') {
                    $typeContract = $this->em->getRepository(TypeContract::class)->findOneBy(
                        ['lib' => $type]
                    );
                    $price = $request->query->get($type . '-price');
                    $promotion = $request->query->get($type . '-promotion');
                    $contract = new Contract;
                    $contract->setPrice($price)
                        ->setPromotion($promotion)
                        ->setType($typeContract)
                        ->setWebsite($website);
                    $website->addContract($contract);
                    $this->em->persist($contract);
                }
            }

            $this->em->persist($website);
            $this->em->persist($company);
            $this->em->flush();

            return $this->redirectToRoute('show_contracts', ['company' => $company->getId()]);
        }

        return $this->render('website/add_with_company.html.twig', [
            'add_website_form' => $addWebsiteForm->createView(),
            'company' => $company,
            'typesContract' => $typesContract
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
            return $this->redirectToRoute('show_company', ['company' => $website->getCompany()->getId()]);
        }

        return $this->render('website/edit.html.twig', [
            'edit_website_form' => $updateWebsiteForm->createView(),
            'website' => $website,
        ]);
    }

    /**
     * @Route("/website/delete/{website}", name="delete_website")
     */
    public function delete(Website $website): Response
    {
        $website->getCompany()->removeWebsite($website);
        $this->em->remove($website);
        $this->em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
