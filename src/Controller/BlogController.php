<?php

namespace App\Controller;
use App\Entity\Post;
use App\Service\NavbarHelper;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{


    private $session;


    public function __construct( SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="index")
     * @param PostRepository $postRepository
     * @param NavbarHelper $navbarHelper
     * @return Response
     */
    public function index(PostRepository $postRepository, NavbarHelper $navbarHelper): Response
    {
        if (!$this->getUser()) {
            //The user isn't logged in
            $navbar = $navbarHelper->retrieveLoggedOutBar();
        }
        else{
            //The user is logged in
            $navbar = $navbarHelper->retrieveLoggedInBar();
        }

        //Holds all the posts
        $data['users'] = $postRepository->get_posts();

        //Rendering the index template
        return $this->render('/index/index.html.twig', ['navbar' => $navbar,
             'data' => $data,
        ]);


    }

    /**
     * @Route("/post/{blog_id?}", name="post")
     * @param PostRepository $postRepository
     * @param NavbarHelper $navbarHelper
     * @param $blog_id
     * @return Response
     */
    public function post(PostRepository $postRepository, NavbarHelper $navbarHelper, $blog_id ): Response
    {
        //The user is logged in
        if($this->getUser())
        {
            //The user is accessing an existent post
            if( $blog_id)
            {
                //retrieving the existent post
                $tupple = $postRepository->get_post_by_user($blog_id,$this->getUser()->getId());

                //The post doesn't exist
                if($tupple == NULL) {
                    $this->addFlash('error', 'Permission denied. You can only update your own posts.');
                    return $this->redirectToRoute('index');
                }

                //Data regarding the existent post
                $data['blog_content'] = $tupple[0]['content']; //getting the post content
                $data['blog_id'] = $blog_id;

                //Navbar of the "Making a Post" section
                $navbar = $navbarHelper->retrievePostBar();

                //Rendering the Post template
                return $this->render('/post/post.html.twig', ['navbar' => $navbar, 'data' => $data,
            ]);

            }
            //The user is creating a new post
            else{

                //Data regarding a new post
                $data['blog_content'] = "";
                $data['blog_id'] = "";

                //Navbar of the "Making a post" section
                $navbar = $navbarHelper->retrievePostBar();

                //Rendering the Post template
                return $this->render('/post/post.html.twig', ['navbar' => $navbar, 'data' => $data,
                ]);
            }

        }
        else{
            //User isn't logged in
            $this->addFlash('error', 'Permission denied. Please sign in.');
            return $this->redirectToRoute('index');

        }


    }

    /**
     * @Route("/post_action/{blog_id?}", name="post_action")
     * @param PostRepository $postRepository
     * @param $blog_id
     * @param Request $request
     * @return Response
     */
    public function post_action(PostRepository $postRepository, $blog_id, Request $request): Response
    {

        //The user is logged in
        if($this->getUser())
        {
            //CSRF attack security
            $token = $request->request->get("token");
            if (!$this->isCsrfTokenValid('post_form', $token)) {
                $this->addFlash('error', 'Permission denied.');
                return $this->redirectToRoute('index');
            }

            //Editing an existing post
            if($blog_id)
            {
                //Content inserted by the user
                $content = $request->request->get('blog_content');

                //Updating the DB
                $postRepository->update_post($blog_id,$content);


                $this->addFlash('success', 'Post successfully updated!');
                return $this->redirectToRoute('index');


            }
            else{
                //Making a new post

                //Content inserted by the user
                $content = $request->request->get('blog_content');

                //Creating a Post object with the given information
                $post = new Post();
                $post->setContent($content);
                $post->setCreatedAt(date("Y-m-d H:i:s"));
                $post->setUpdatedAt(date("Y-m-d H:i:s"));
                $post->setUser($this->getUser());
                $post->setLikes(0);

                //Updating the DB
                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();

                $this->addFlash('success', 'Post successfully created!');
                return $this->redirectToRoute('index');
            }

        }
        else{
            $this->addFlash('error', 'Permission denied. Please sign in.');
            return $this->redirectToRoute('index');
        }


    }


}
