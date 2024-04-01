<?php
namespace App\Tests;

use Twig\Environment;
use PHPUnit\Framework\TestCase;
use App\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefaultControllerTest extends TestCase
{
    private DefaultController $defaultController;

    protected function setUp(): void
    {

        $templatingMock = $this->createMock(Environment::class);
        $templatingMock->method('render')->withAnyParameters()->willReturn('rendered content');
        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->method('get')->willReturnCallback(function ($param) use ( $templatingMock) {
            return match ($param) {
                'twig' => $templatingMock
            };
        });
        $containerMock->method('has')->willReturnCallback(function ($param) {
            return match ($param) {
                'twig', 'form.factory', 'router', 'request_stack' => true
            };
        });

        $this->defaultController = new DefaultController();
        $this->defaultController->setContainer($containerMock);
    }
    public function testIndexAction()
    {
        $response = $this->defaultController->indexAction();
        static::assertEquals('rendered content', $response->getContent());
    }
}