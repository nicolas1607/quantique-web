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
     * @Route("/admin/website/edit/{website}", name="edit_website")
     */
    public function edit(Request $request, Website $website): Response
    {
        $website->setName($request->get('name'))
            ->setUrl($request->get('url'));

        // Liste des contrats ajoutés
        $types = ['vitrine', 'commerce', 'google', 'facebook'];
        foreach ($types as $type) {
            $check = $request->get($type . '-check');
            $price = $request->get($type . '-price');
            $promotion = $request->get($type . '-promotion');
            $typeContract = $this->em->getRepository(TypeContract::class)->findOneBy(
                ['lib' => $type]
            );
            $flag = false;
            foreach ($website->getContracts() as $contract) {
                if ($contract->getType()->getLib() == $type && $check == 'on') {
                    if ($price) $contract->setPrice(floatval($price));
                    if ($promotion) $contract->setPromotion(floatval($promotion));
                    else $contract->setPromotion(0);
                    $flag = true;
                } else if ($contract->getType()->getLib() == $type && $check != 'on') {
                    $this->em->remove($contract);
                }
            }
            if ($check == 'on' && $flag == false) {
                $price = $request->get($type . '-price');
                $promotion = $request->get($type . '-promotion');
                $contract = new Contract;
                $contract->setPrice(floatval($price))
                    ->setType($typeContract)
                    ->setWebsite($website);
                if (($promotion != $price) && ($promotion != null)) {
                    $contract->setPromotion(floatval($promotion));
                }
                $website->addContract($contract);
                $this->em->persist($contract);
            }
        }

        $this->em->persist($website);
        $this->em->flush();
        return $this->redirectToRoute('show_contracts', ['company' => $website->getCompany()->getId()]);
    }

    /**
     * @Route("/admin/website/delete/{website}", name="delete_website")
     */
    public function delete(Website $website): Response
    {
        $website->getCompany()->removeWebsite($website);
        $this->em->remove($website);
        $this->em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    // MODAL EVENT //

    /**
     * @Route("/admin/website/add/modal/{company}", name="add_website_modal")
     */
    public function addFromModal(Request $request, Company $company): Response
    {
        $website = new Website();
        $website->setName($request->get('name'))
            ->setUrl($request->get('url'))
            ->setCompany($company);
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
                $contract->setPrice(floatval($price))
                    ->setType($typeContract)
                    ->setWebsite($website);
                if (($promotion != $price) && ($promotion != null)) {
                    $contract->setPromotion(floatval($promotion));
                }
                $website->addContract($contract);
                $this->em->persist($contract);
            }
        }

        $this->em->persist($website);
        $this->em->persist($company);
        $this->em->flush();

        return $this->redirectToRoute('show_contracts', ['company' => $company->getId()]);
    }
}
