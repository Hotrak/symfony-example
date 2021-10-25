<?php

namespace App\Controller\v1;

use App\Entity\v1\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/products", name="products.")
 */
class ProductController extends AbstractController
{

    /**
     * @Route("", name="products.index")
     */
    public function index(): JsonResponse
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->json($products);
    }

    /**
     * @Route("/create", name="products.store")
     */
    public function store(EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $product = new Product();
        $product->setName('Процессор');
        $product->setPrice(100);

        $errors = $validator->validate($product);

        if (count($errors) > 0) {
            /*
         * Uses a __toString method on the $errors variable which is a
         * ConstraintViolationList object. This gives us a nice string
         * for debugging.
         */
            $errorsString = $errors;

            return $this->json($errorsString, 422);
        }


        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json($product);
    }

    /**
     * @Route("/{id}", name="products.show")
     */
    public function show($id): JsonResponse
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);

        if (!$product) return $this->json('Given data was invalid', 422);

        return $this->json($product);
    }

    /**
     * @Route("/{id}/update", name="products.show")
     */
    public function update($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);

        if (!$product) return $this->json('Given data was invalid', 422);

        $product->setPrice(999);
        $entityManager->flush();
        return $this->json($product);
    }

    /**
     * @Route("/{id}/delete", name="products.delete")
     */
    public function destroy($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);

        if (!$product) return $this->json('Given data was invalid', 422);

        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json('ОК');
    }
}
