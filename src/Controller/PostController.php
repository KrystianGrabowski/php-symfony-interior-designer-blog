<?php

namespace App\Controller;

use App\Entity\Post;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
    /**
     * @Route("/post", name="posts", methods={"GET"})
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $posts = $entityManager->getRepository(Post::class)->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/post/new", name="post_new", methods={"GET", "POST"})
     */
    public function new(Request $request)
    {
        $post = new Post;

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('body', TextareaType::class, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Create Post', 'attr' => array('class' => 'btn btn-primary mt-2')))
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreatedAt(new DateTime('now'));
            $post->setAuthor('John Doe');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('posts');
        }
        
        return $this->render('post/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post/{id}", name="post_show", methods={"GET"})
     */
    public function show($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);

        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }
}
