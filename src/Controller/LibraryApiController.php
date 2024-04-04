<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LibraryApiController extends AbstractController
{
    #[Route('/api/library/books', name: 'api_library_books', methods: ['GET'])]
    public function getAllBooks(BookRepository $bookRepository): JsonResponse
    {
        $books = $bookRepository->findAll();
        $formattedBooks = [];

        foreach ($books as $book) {
            $formattedBooks[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'isbn' => $book->getIsbn(),
                'author' => $book->getAuthor(),
                'image' => $book->getImage(),
            ];
        }

        $response = $this->json($formattedBooks);

        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route('/api/library/book/{isbn}', name: 'api_library_book_by_isbn', methods: ['GET'])]
    public function getBookByIsbn(BookRepository $bookRepository, string $isbn): JsonResponse
    {
        $book = $bookRepository->findOneBy(['isbn' => $isbn]);

        if (!$book) {
            return $this->json(['error' => 'Book not found'], 404);
        }

        $formattedBook = [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'isbn' => $book->getIsbn(),
            'author' => $book->getAuthor(),
            'image' => $book->getImage(),
        ];

        $response = $this->json($formattedBook);


        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
