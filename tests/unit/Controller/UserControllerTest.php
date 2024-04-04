<?php
namespace App\Tests\Unit\Controller;

use App\Entity\Task;
use App\Entity\User;
use Twig\Environment;
use PHPUnit\Framework\TestCase;
use App\Controller\TaskController;
use Doctrine\ORM\EntityRepository;
use App\Controller\DefaultController;
use App\Controller\SecurityController;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\RegistrationController;
use App\Controller\UserController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserControllerTest extends TestCase
{
    private UserController $userController;
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
        $formInterfaceMock->method('getData')->withAnyParameters()->willReturn('bobo');

        $formFactoryInterfaceMock = $this->createMock(FormFactoryInterface::class);
        $formFactoryInterfaceMock->method('create')->withAnyParameters()->willReturn($formInterfaceMock);

        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->method('generate')->withAnyParameters()->willReturn('https://url');

        $tokenInterfaceMock = $this->createMock(TokenInterface::class);
        $tokenInterfaceMock->method('getUser')->withAnyParameters()->willReturnCallback(fn() => $this->user);

        $securityTokenStorageMock = $this->createMock(TokenStorageInterface::class);
        $securityTokenStorageMock->method('getToken')->withAnyParameters()->willReturn($tokenInterfaceMock);

        $flashBagMock = $this->createMock(FlashBag::class);
        $flashBagMock->method('add')->withAnyParameters();

        $sessionInterfaceMock = $this->createMock(Session::class);
        $sessionInterfaceMock->method('getFlashBag')->withAnyParameters()->willReturn($flashBagMock);

        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->method('getSession')->withAnyParameters()->willReturn($sessionInterfaceMock);

        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->method('get')->willReturnCallback(function ($param) use (
            $templatingMock,
         $formFactoryInterfaceMock,
         $routerMock,
         $requestStackMock,
         $securityTokenStorageMock
         ) {
            return match ($param) {
                'twig' => $templatingMock,
                'form.factory' => $formFactoryInterfaceMock,
                'router' => $routerMock,
                'security.token_storage' => $securityTokenStorageMock,
                'request_stack' => $requestStackMock
            };
        });
        $containerMock->method('has')->willReturnCallback(function ($param) {
            return match ($param) {
                'twig', 'form.factory', 'router', 'request_stack' , 'security.token_storage'=> true
            };
        });

        $this->userController = new UserController();
        $this->userController->setContainer($containerMock);
    }

    public function testListAction()
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $entityRepositoryMock->method('findAll')->withAnyParameters()->willReturn([]);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->withAnyParameters()->willReturn($entityRepositoryMock);

        $response = $this->userController->listAction($entityManagerMock);
        static::assertInstanceOf(Response::class, $response);
    }

    public function testEditAction()
    {
        $requestMock  = $this->createMock(Request::class);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('persist')->withAnyParameters();
        $entityManagerMock->method('flush')->withAnyParameters();


        $response = $this->userController->editAction(new User(), $requestMock, $entityManagerMock);
        static::assertInstanceOf(RedirectResponse::class, $response);

        $this->formIsValid = false;
        $response = $this->userController->editAction(new User(), $requestMock, $entityManagerMock);
        static::assertInstanceOf(Response::class, $response);
        static::assertEquals('rendered content', $response->getContent());
    }

}