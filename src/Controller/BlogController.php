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
     * @Route("/index", name="index")
     * @param PostRepository $postRepository
     * @param NavbarHelper $navbarHelper
     * @return Response
     */
    public function index(PostRepository $postRepository, NavbarHelper $navbarHelper): Response
    {
        if (!$this->getUser()) {
            //The user isn't logged in
            $navbar = $navbarHelper->retrieveLoggedOutBar();
            $UserDetails['isLoggedIn'] = False;
        }
        else{
            //The user is logged in
            $navbar = $navbarHelper->retrieveLoggedInBar();
            //might change this to start using session..
            $UserDetails['isLoggedIn'] = True;
            $UserDetails['username'] = $this->getUser()->getName();
            $UserDetails['id'] = $this->getUser()->getId();
        }


        $data['users'] = $postRepository->get_posts();
        return $this->render('/index/index.html.twig', ['navbar' => $navbar,
            'UserDetails' => $UserDetails, 'data' => $data,
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

            if( $blog_id)
            {

                $tupple = $postRepository->get_post_by_user($blog_id,$this->getUser()->getId());
                if($tupple == NULL) {
                    $this->addFlash('error', 'Permission denied. You can only update your own posts.');
                    return $this->redirectToRoute('index');

                    //return $this->redirectToRoute('permissionDenied');
                }
                $data['blog_content'] = $tupple[0]['content']; //getting the post content
                $data['blog_id'] = $blog_id;
                $navbar = $navbarHelper->retrievePostBar();
                return $this->render('/post/post.html.twig', ['navbar' => $navbar, 'data' => $data,
            ]);

            }
            else{
                $data['blog_content'] = "";
                $data['blog_id'] = "";
                $navbar = $navbarHelper->retrievePostBar();
                return $this->render('/post/post.html.twig', ['navbar' => $navbar, 'data' => $data,
                ]);
            }

        }
        else{
            $this->addFlash('error', 'Permission denied. Please sign in.');
            return $this->redirectToRoute('index');
//        return $this->redirectToRoute('loginRequired');
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

            $token = $request->request->get("token");
            if (!$this->isCsrfTokenValid('post_form', $token)) {
                return new Response("Operation not allowed", Response::HTTP_OK,
                    ['content-type' => 'text/plain']);
            }

            if($blog_id)
            {
                $content = $request->request->get('blog_content');
                $postRepository->update_post($blog_id,$content);
                $this->addFlash('success', 'Post successfully updated!');
                return $this->redirectToRoute('index');
               // return $this->redirectToRoute('postUpdated');

            }
            else{
                $content = $request->request->get('blog_content');


                $post = new Post();
                $post->setContent($content);
                $post->setCreatedAt(date("Y-m-d H:i:s"));
                $post->setUpdatedAt(date("Y-m-d H:i:s"));
                $post->setUser($this->getUser());
                $post->setLikes(0);

                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();

                $this->addFlash('success', 'Post successfully created!');
                return $this->redirectToRoute('index');
                //return $this->redirectToRoute('postCreated');
            }

        }
        else{
            $this->addFlash('error', 'Permission denied. Please sign in.');
            return $this->redirectToRoute('index');
           // return $this->redirectToRoute('loginRequired');
        }


    }


}
