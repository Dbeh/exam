<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Repository\MessagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessagesController extends AbstractController
{
    /**
     * @Route("/", name="messages")
     */
    public function index()
    {
        return $this->render('messages/index.html.twig');
    }

    /**
     * @Route("/show_messages", name="show_messages")
     */
    public function showMessages(MessagesRepository $messagesRepository)
    {
        return $this->getShowTable($messagesRepository);
    }



    /**
     * @Route("/add_message", name="add_message")
     */
    public function addMessage(
        MessagesRepository $messagesRepository,
        Request $request
    )
    {
        $message = (string)$request->request->get('message');
        $message = substr($message, 0, 255);

        if($message){
            $messages = new Messages();
            $messages->setCreateAt(new \DateTime());
            $messages->setMessage($message);

            $em = $this->getDoctrine()->getManager();
            $em->persist($messages);
            $em->flush();
        }

        return $this->getShowTable($messagesRepository);

    }

    private function getShowTable(MessagesRepository $messagesRepository)
    {
        $messages = $messagesRepository->findBy([], ['id'=>'DESC']);

        if(count($messages) > 10){

            $em = $this->getDoctrine()->getManager();

            $sliceMessages = array_slice($messages, 10);

            foreach ($sliceMessages as $message){
                $mess = $em->getRepository(Messages::class)->find($message->getId());
                $em->remove($mess);
            }

            $em->flush();

            //$messages = $messagesRepository->findBy([], ['id'=>'DESC']);
            $messages = array_slice($messages,0, 10);
        }


        if(!$messages) {
            $view = "/messages/_messages_not_found.html.twig";
        } else {
            $view = "/messages/_messages_table.html.twig";
        }

        return $this->render($view, [
            "messages" => $messages,
        ]);
    }

}
