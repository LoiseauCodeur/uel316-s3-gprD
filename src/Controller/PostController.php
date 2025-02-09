<?php 

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PostController extends AbstractController
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/post/new', name: 'post_new')]
    public function new(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $post = new \App\Entity\Post();
        $form = $this->createForm(\App\Form\PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $post->setImageFile($imageFile); 
            }

            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return $this->redirectToRoute('post_success');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/posts", name="admin_post_list")
     */
    public function listAction(PaginatorInterface $paginator, Request $request)
    {
        $queryBuilder = $this->entityManager->getRepository(\App\Entity\Post::class)->createQueryBuilder('p');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/post/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/admin/posts/create", name="admin_post_create")
     */
    public function createAction(Request $request)
    {
        $post = new \App\Entity\Post();
        $form = $this->createForm(\App\Form\PostType::class, $post);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
        
            if ($user) {
                $post->setAuthor($user);
            } else {
                throw new \Exception('User is not authenticated.');
            }
        
            if ($post->getImageFile()) {
                $this->logger->info('Uploading image: ' . $post->getImageName());
            }
        
            $this->entityManager->persist($post);
            $this->entityManager->flush();
        
            return $this->redirectToRoute('admin_post_list');
        }
        
        return $this->render('admin/post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function uploadImage(Request $request)
    {
        $post = new \App\Entity\Post();
    
        $file = $request->files->get('imageFile');
    
        if ($file) {
            $post->setImageFile($file);
    
            $this->entityManager->persist($post);
            $this->entityManager->flush();
    
            $imageName = $post->getImageName();
            return $this->json(['imageName' => $imageName]);
        }
    
        return $this->json(['error' => 'No file uploaded'], 400);
    }
}
