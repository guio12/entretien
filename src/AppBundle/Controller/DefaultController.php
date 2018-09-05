<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('AppBundle:Article')->findAll();
        $comments = $em->getRepository('AppBundle:Comment')->findAll();

        $userId = $this->getUser() ? $this->getUser()->getId() : null;
        $username = $this->getUser() ? $this->getUser()->getUsername() : null;
        $comment = new Comment();
        $comment->setUser($userId);

        $form = $this->createForm('AppBundle\Form\CommentType', $comment);
        $form->handleRequest($request);

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR, 'articles' => $articles, 'comments' => $comments, 'username' => $username, 'form' => $form->createView()
        ));
    }

    /**
     * @Route("/newComment", name="comment_new")
     */
    public function newComment(Request $request)
    {


        $userId = $this->getUser() ? $this->getUser()->getId() : null;

        $em = $this->getDoctrine()->getManager();
        $id_user = $em->getRepository('AppBundle:User')->find($userId);
        $comment = new Comment();
        $comment->setUser($id_user);
        $comment->setDate(strftime('%c'));

        $form = $this->createForm('AppBundle\Form\CommentType', $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('homepage', array('id' => $comment->getId()));
        }
        return $this->render('default/index.html.twig', array(
            'articles' => $articles,
            'form' => $form->createView()));
    }
}
