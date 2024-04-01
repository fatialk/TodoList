<?php
namespace App\Tests;

use App\Entity\User;
use Twig\Environment;
use PHPUnit\Framework\TestCase;
use App\Controller\DefaultController;
use App\Controller\SecurityController;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\RegistrationController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityControllerTest extends TestCase
{
    private SecurityController $securityControllerMock;
    private bool $formIsValid = true;
    private ?User $user = null;

    protected function setUp(): void
    {

        $templatingMock = $this->createMock(Environment::class);
        $templatingMock->method('render')->withAnyParameters()->willReturn('rendered content');

        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->method('handleRequest')->withAnyParameters()->willReturn($formInterfaceMock);
        $formInterfaceMock->method('isValid')->withAnyParameters()->willReturnCallback(fn() => $this->formIsValid);
        $formInterfaceMock->method('isSubmitted')->withAnyParameters()->willReturn(true);
        $formInterfaceMock->method('get')->withAnyParameters()->willReturn($formInterfaceMock);
        $formInterfaceMock->method('getData')->withAnyParameters()->willReturn('toto');

        $formFactoryInterfaceMock = $this->createMock(FormFactoryInterface::class);
        $formFactoryInterfaceMock->method('create')->withAnyParameters()->willReturn($formInterfaceMock);

        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->method('generate')->withAnyParameters()->willReturn('https://url');

        $tokenInterfaceMock = $this->createMock(TokenInterface::class);
        $tokenInterfaceMock->method('getUser')->withAnyParameters()->willReturnCallback(fn() => $this->user);

        $securityTokenStorageMock = $this->createMock(TokenStorageInterface::class);
        $securityTokenStorageMock->method('getToken')->withAnyParameters()->willReturn($tokenInterfaceMock);

        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->method('get')->willReturnCallback(function ($param) use (
            $templatingMock,
         $formFactoryInterfaceMock,
         $routerMock,
         $securityTokenStorageMock
         ) {
            return match ($param) {
                'twig' => $templatingMock,
                'form.factory' => $formFactoryInterfaceMock,
                'router' => $routerMock,
                'security.token_storage' => $securityTokenStorageMock
            };
        });
        $containerMock->method('has')->willReturnCallback(function ($param) {
            return match ($param) {
                'twig', 'form.factory', 'router', 'request_stack' , 'security.token_storage'=> true
            };
        });

        $this->securityControllerMock = new SecurityController();
        $this->securityControllerMock->setContainer($containerMock);
    }

    public function testLoginAction()
    {

        $authenticationUtilsMock = $this->createMock(AuthenticationUtils::class);
        $authenticationUtilsMock->method('getLastAuthenticationError')->withAnyParameters()->willReturn(null);
        $authenticationUtilsMock->method('getLastUsername')->withAnyParameters()->willReturn('toto');


        $response = $this->securityControllerMock->loginAction($authenticationUtilsMock);
        static::assertInstanceOf(Response::class, $response);


        $this->user = new User();
        $response = $this->securityControllerMock->loginAction($authenticationUtilsMock);
        static::assertInstanceOf(RedirectResponse::class, $response);

    }

    public function testLogoutAction()
    {
        static::expectException(\LogicException::class);
        $this->securityControllerMock->logout();
    }

}