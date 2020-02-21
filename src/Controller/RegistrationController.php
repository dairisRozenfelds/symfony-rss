<?php

namespace App\Controller;

use App\Entity\User;
use App\Tools\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use function json_decode;

class RegistrationController extends AbstractController
{
    const CSRF_TOKEN_NAME = 'registration';

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param Validator $validator
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Validator $validator,
        TranslatorInterface $translator
    ) {
        $this->validator = $validator;
        $this->translator = $translator;
    }

    /**
     * @Route("/register", name="app_register_get", methods={"GET"})
     * @return Response
     */
    public function register(): Response
    {
        return $this->render('registration/register.html.twig', [
            'submitRoute' => $this->generateUrl('app_register_post'),
            'submitRedirectRoute' => $this->generateUrl('index'),
            'validateEmailRoute' => $this->generateUrl('app_register_validate_email'),
            'tokenName' => self::CSRF_TOKEN_NAME
        ]);
    }

    /**
     * @Route("/register", name="app_register_post", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     */
    public function registerAjax(Request $request, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        // Validate the csrf token
        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_NAME, $content['token'] ?? null)) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    $this->translator->trans('validation.csrf_invalid')
                ]
            ]);
        }

        $validatorConstraints = [
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'password' => [new Assert\NotBlank()]
        ];

        $errors = $this->validator->validate($validatorConstraints, $content);

        if (count($errors) > 0) {
            return new JsonResponse([
                'success' => false,
                'errors' => $errors
            ]);
        }

        $user = new User();
        $user->setEmail($content['email'] ?? null);
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $content['password']
            )
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @Route("/register/validate-email", name="app_register_validate_email")
     * @param Request $request
     * @return JsonResponse
     */
    public function validateEmail(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        $validatorConstraints = [
            'email' => [new Assert\Email()]
        ];

        // Validate the e-mail format first
        $errors = $this->validator->validate($validatorConstraints, $content);

        if (count($errors) > 0) {
            return new JsonResponse([
                'success' => false,
                'errors' => $errors
            ]);
        }

        // Validate if the e-mail address already exists
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $content['email']]);

        if ($user) {
            return new JsonResponse([
                'success' => false,
                'errors' => ['email' => ['User with the e-mail already exists.']]
            ]);
        }

        return new JsonResponse([
            'success' => true
        ]);
    }
}
