<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostTag;
use App\Entity\Tag;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
    public function new(Request $request, SluggerInterface $slugger)
    {
        // TODO
        // Split code

        $post = new Post;

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('body', TextareaType::class, array('attr' => array('class' => 'form-control')))
            ->add('tags', TextType::class, array('attr' => array('class' => 'form-control'), 'mapped' => false))
            ->add('photo', FileType::class, [
                'label' => 'Photo (image)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new Image([
                        'maxSize' => '10M'
                    ])
                ],
            ])
            ->add('save', SubmitType::class, array('label' => 'Create Post', 'attr' => array('class' => 'btn btn-primary mt-2')))
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreatedAt(new DateTime('now'));
            $post->setAuthor('John Doe');
            
            
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('post_photo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // TODO
                }

                $post->setPhotoName($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);

            $tags = $form->get('tags')->getData();
            $tags = preg_split('/\s+/', $tags, -1, PREG_SPLIT_NO_EMPTY);

            foreach($tags as $tagName)
            {
                $postTag = new PostTag;
                $postTag->setPost($post);

                $tag = $entityManager->getRepository(Tag::class)->findOneBy(['name' => $tagName]);
                if ($tag == null)
                {
                    $tag = new Tag;
                    $tag->setName($tagName);
                    $entityManager->persist($tag);
                }

                $postTag->setTag($tag);
                $entityManager->persist($postTag);
            }

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

    /**
     * @Route("/post/{id}", name="post_delete", methods={"DELETE"})
     */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);

        $entityManager->remove($post);
        $entityManager->flush();

        return new Response("Post has been deleted");
    }
}
