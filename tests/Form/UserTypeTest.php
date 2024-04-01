<?php
namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Form\UserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserTypeTest extends TestCase
{
    private UserType $userType;
    private bool $added = false;

    protected function setUp(): void
    {
        $this->userType = new UserType();
    }

    public function testBuildForm()
    {
        $formBuilderMock = $this->createMock(FormBuilderInterface::class);
        $formBuilderMock->method('add')->withAnyParameters()->willReturnCallback(function() use($formBuilderMock) {
            $this->added = true;
            return $formBuilderMock;
        });

        $this->userType->buildForm($formBuilderMock, []);
        static::assertTrue($this->added);
    }
}