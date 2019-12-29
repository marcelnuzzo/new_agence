<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProductController extends AbstractController
{
    /**
    * @Route("/product/new", name="app_product_new")
    */
    public function new(Request $request, EntityManagerInterface $manager)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(!$product->getId()) {
                $product->setCreatedAt(new \DateTime('now'));
            }
            
            $brochureFile = $form['brochure']->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    throw new FileException('Failed to upload file');
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setBrochureFilename($newFilename);
            }

            $imageFile = $form['image']->getData();
            if($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII;Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $this->logger->error('failed to upload image: ' . $e->getMessage());
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setImage($newFilename);
            }

            /*
            $fileImage = $product->getImage();
            $fileNom = $fileImage->guessExtension();
            $fileImage->move(
                $this->getParameter('images_directory'),
                $fileNom
            );
            $product->setImage($fileNom);
            */
            $manager->persist($product);         
            $manager->flush();
            // ... persist the $product variable or any other work

            return $this->redirect($this->generateUrl('app_product_list'));
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }
    
    /**
     * @Route("/product/list", name="app_product_list")
     */
    public function list()
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $products = $repo->findAll();
        $repo1 = $this->getDoctrine()->getRepository(Product::class);
        $product = $repo1->findAll();

        return $this->render('product/list.html.twig', [
            'controller_name' => 'ProductController',
            'products'=> $products,
            'product' => $product,
        ]);
    }

}