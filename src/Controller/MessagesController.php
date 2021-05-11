<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MessagesController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function new(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'test'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2])
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'test@test.fr'],
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => ['placeholder' => 'message test'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 10])
                ]
            ])
            ->getForm()
        ;

//        != PHP8
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $formData = $form->getData();
//            dump(sprintf('Incoming email from %s <%s>', $formData['name'], $formData['email']));
//            $this->addFlash('success', "Message sent!");
//            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
//        }
//
//        if ($form->isSubmitted() && !$form->isValid()) {
//            $response = new Response();
//            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
//
//            return $this->render('messages/new.html.twig', [
//                'form' => $form->createView()
//            ], $response);
//        }

        return $this->handleForm(
            $form,
            $request,
            function (FormInterface $form, array $data) use ($request) {
                dump(sprintf('Incoming email from %s <%s>', $data['name'], $data['email']));

                // PHP8 | strpos() PHP < 8
                if (str_contains($request->headers->get('accept'), 'text/vnd.turbo-stream.html')) {
                    return new Response(
                        $this->renderView('messages/success.stream.html.twig', [
                            'name' => $data['name']
                        ]),
                        200,
                        [
                            'Content-Type' => 'text/vnd.turbo-stream.html'
                        ]
                    );
                }

                $this->addFlash('success', "Message sent!");
                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            },
            function (FormInterface $form, ?array $data) {
                return $this->render('messages/new.html.twig', [
                    'form' => $form->createView()
                ]);
            }
        );
    }
}
