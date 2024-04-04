<?php

// src/Controller/LibraryController.php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\BookFormType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    private BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }
    #[Route('/library', name: 'library_index')]
    public function showAllBooks(
        BookRepository $bookRepository
    ): Response {
        $books = $bookRepository->findAll();

        return $this->render('library/index.html.twig', [
            'books' => $books,
        ]);

    }

    #[Route('/books', name: 'all_books')]
    public function showAllBooksDetails(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('library/all_books.html.twig', [
            'books' => $books,
        ]);
    }


    #[Route('/new-book', name: 'new_book', methods: ['GET', 'POST'])]
    public function newBook(Request $request, BookRepository $bookRepository): Response
    {
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bookRepository->save($book);

            return $this->redirectToRoute('all_books');
        }

        return $this->render('library/new_book.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/book/{id}', name: 'book_details')]
    public function showBookDetails(Book $book): Response
    {
        return $this->render('library/book_details.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/book/{id}/edit', name: 'edit_book')]
    public function editBook(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookRepository->save($book);

            return $this->redirectToRoute('book_details', ['id' => $book->getId()]);
        }

        return $this->render('library/edit_book.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/book/delete/{id}', name: 'delete_book')]
    public function deleteBookById(ManagerRegistry $doctrine, int $id): RedirectResponse
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('No book found for id ' . $id);
        }

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('all_books');
    }


}
