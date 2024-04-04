<?php
namespace App\Tests\Unit\Form;

use PHPUnit\Framework\TestCase;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RegistrationFormTypeTest extends TestCase
{
    private RegistrationFormType $registrationFormType;
    private bool $added = false;
    private bool $defaultOptionsSetted = false;

    protected function setUp(): void
    {
        $this->registrationFormType = new RegistrationFormType();
    }

    public function testBuildForm()
    {
        $formBuilderMock = $this->createMock(FormBuilderInterface::class);
        $formBuilderMock->method('add')->withAnyParameters()->willReturnCallback(function() use($formBuilderMock) {
            $this->added = true;
            return $formBuilderMock;
        });

        $this->registrationFormType->buildForm($formBuilderMock, []);
        static::assertTrue($this->added);
    }

    public function testConfigureOptions()
    {
        $optionsResolverMock = $this->createMock(OptionsResolver::class);
        $optionsResolverMock->method('setDefaults')->withAnyParameters()->willReturnCallback(function() use($optionsResolverMock) {
            $this->defaultOptionsSetted = true;
            return $optionsResolverMock;
        });

        $this->registrationFormType->configureOptions($optionsResolverMock);
        static::assertTrue($this->defaultOptionsSetted);
    }
}