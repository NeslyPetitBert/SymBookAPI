<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookController extends APIController
{
    private $manager;

    private $bookRepo;

    public function __construct(EntityManagerInterface $manager, BookRepository $bookRepo)
    {
        $this->manager = $manager;
        $this->bookRepo = $bookRepo;
    }
    
    /**
    * @Route("/books", methods="GET")
    */
    public function index()
    {
        $books = $this->bookRepo->transformAll();

        return $this->respond($books);
    }

    /**
    * @Route("/books/creer", methods="POST")
    */
    public function create(Request $request)
    {
        $request = $this->transformJsonBody($request);

        if (!$request) {
            return $this->respondValidationError('Please provide a valid request!');
        }

        // validate the title
        if (!$request->get('title')) {
            return $this->respondValidationError('Please provide a title!');
        }

        // validate the title
        if (!$request->get('content')) {
            return $this->respondValidationError('Please provide a content!');
        }
        if (strlen($request->get('content')) > 30) {
            return $this->respondValidationError('Vous ne pouvez pas choisir plus de 30 caractères');
        }
        if (strlen($request->get('content')) < 5) {
            return $this->respondValidationError('Vous devez choisir au moins 5 caractères');
        }

        // persist the new movie
        $book = new Book;
        $book->setTitle($request->get('title'));
        $book->setContent($request->get('content'));
        $this->manager->persist($book);
        $this->manager->flush();

        return $this->respondCreated($this->bookRepo->transform($book));
    }

}
