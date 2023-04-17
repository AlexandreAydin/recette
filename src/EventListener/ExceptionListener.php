<?php 

// namespace App\EventListener;

// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpKernel\Event\ExceptionEvent;
// use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

// class ExceptionListener
// {
//     public function __invoke(ExceptionEvent $event): void
//     {
//         // You get the exception object from the received event
//         $exception = $event->getThrowable();
//         $message = sprintf(
//             'My Error says: %s with code: %s',
//             $exception->getMessage(),
//             $exception->getCode()
//         );

//         // Customize your response object to display the exception details
//         $response = new Response();
//         $response->setContent($message);

//         // HttpExceptionInterface is a special type of exception that
//         // holds status code and header details
//         if ($exception instanceof HttpExceptionInterface) {
//             $response->setStatusCode($exception->getStatusCode());
//             $response->headers->replace($exception->getHeaders());
//         } else {
//             $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
//         }

//         // sends the modified response object to the event
//         $event->setResponse($response);
//     }
// }

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        // Récupérez l'exception et faites quelque chose avec elle
        $exception = $event->getThrowable();

        // Vous pouvez créer une réponse personnalisée en fonction de l'exception, par exemple :
        $response = new Response();
        $response->setContent('<h1>Une erreur s\'est produite</h1>');

        // Envoyez la réponse personnalisée au client
        $event->setResponse($response);
    }
}