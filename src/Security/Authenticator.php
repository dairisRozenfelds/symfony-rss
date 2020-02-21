<?php

namespace App\Security;

use App\Controller\AuthController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class Authenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Authenticator constructor.
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return 'app_login_post' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    public function getCredentials(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $credentials = [
            'email' => $content['email'] ?? null,
            'password' => $content['password'] ?? null,
            'csrf_token' => $content['token'] ?? null,
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User|object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken(AuthController::CSRF_TOKEN_NAME, $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException($this->translator->trans('validation.email_not_found'));
        }

        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param $credentials
     * @return string|null
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return JsonResponse
     * @throws Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($exception instanceof BadCredentialsException) {
            $errors = ['password' => [$this->translator->trans('validation.bad_credentials')]];
        } elseif ($exception instanceof InvalidCsrfTokenException) {
            $errors = ['email' => [$this->translator->trans('validation.csrf_invalid')]];
        } elseif ($exception instanceof CustomUserMessageAuthenticationException) {
            $errors = ['email' => [$exception->getMessage()]];
        } else {
            $errors = ['password' => [$this->translator->trans('validation.auth_error')]];
        }

        return new JsonResponse([
            'success' => false,
            'errors' => $errors
        ]);
    }

    /**
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login_get');
    }
}
