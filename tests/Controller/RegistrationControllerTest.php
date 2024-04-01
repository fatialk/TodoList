<?php
namespace App\Tests;

use Twig\Environment;
use PHPUnit\Framework\TestCase;
use App\Controller\DefaultController;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\RegistrationController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationControllerTest extends TestCase
{
    private RegistrationController $registrationController;
    private bool $formIsValid = true;

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

        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->method('get')->willReturnCallback(function ($param) use ( $templatingMock, $formFactoryInterfaceMock, $routerMock) {
            return match ($param) {
                'twig' => $templatingMock,
                'form.factory' => $formFactoryInterfaceMock,
                'router' => $routerMock
            };
        });
        $containerMock->method('has')->willReturnCallback(function ($param) {
            return match ($param) {
                'twig', 'form.factory', 'router', 'request_stack' => true
            };
        });

        $this->registrationController = new RegistrationController();
        $this->registrationController->setContainer($containerMock);
    }

    public function testRegister()
    {
        $requestMock  = $this->createMock(Request::class);

        $userPasswordMock = $this->createMock(UserPasswordHasherInterface::class);
        $userPasswordMock->method('hashPassword')->withAnyParameters()->willReturn('sdhjczsdcedchb');

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('persist')->withAnyParameters();
        $entityManagerMock->method('flush')->withAnyParameters();


        $response = $this->registrationController->register($requestMock, $userPasswordMock, $entityManagerMock);
        static::assertInstanceOf(RedirectResponse::class, $response);

        $this->formIsValid = false;
        $response = $this->registrationController->register($requestMock, $userPasswordMock, $entityManagerMock);
        static::assertInstanceOf(Response::class, $response);
        static::assertEquals('rendered content', $response->getContent());
    }
}