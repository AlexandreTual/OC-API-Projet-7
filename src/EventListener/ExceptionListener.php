<?php
namespace App\EventListener;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        // Customize your response object to display the exception details
        $response = new Response();

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
            $message = [
                'code' => $exception->getStatusCode(),
                'message' => $exception->getMessage(),
            ];

            $body = $this->serializer->serialize($message, 'json');
            $response->setContent($body);

            // sends the modified response object to the event
            $event->setResponse($response);

            return;
        }

        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $message = [
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => $exception->getMessage(),
        ];

        $body = $this->serializer->serialize($message, 'json');
        $response->setContent($body);

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}
