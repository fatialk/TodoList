<?php
namespace App\Tests\Unit\Form;

use PHPUnit\Framework\TestCase;
use App\Form\TaskType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TaskTypeTest extends TestCase
{
    private TaskType $taskType;
    private bool $added = false;

    protected function setUp(): void
    {
        $this->taskType = new TaskType();
    }

    public function testBuildForm()
    {
        $formBuilderMock = $this->createMock(FormBuilderInterface::class);
        $formBuilderMock->method('add')->withAnyParameters()->willReturnCallback(function() use($formBuilderMock) {
            $this->added = true;
            return $formBuilderMock;
        });

        $this->taskType->buildForm($formBuilderMock, []);
        static::assertTrue($this->added);
    }

}