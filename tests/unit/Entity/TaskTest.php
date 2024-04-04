<?php
namespace App\Tests\Unit\Entity;
use DateTime;
use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private Task $task;

    protected function setUp(): void
    {
        $this->task = new Task;
    }

    public function testGetCreatedAt()
    {
        $now  = new DateTime();
        $this->task->setCreatedAt($now);
        $this->assertEquals($now, $this->task->getCreatedAt());
    }

    public function testGetTitle()
    {
        $this->task->setTitle('task test');
        $this->assertEquals('task test', $this->task->getTitle());
    }

    public function testGetContent()
    {
        $this->task->setContent('ceci est un test');
        $this->assertEquals('ceci est un test', $this->task->getContent());
    }

    public function testIsDone()
    {
        $this->task->toggle(1);
        $this->assertEquals(1, $this->task->isDone());
    }

    public function testGetUser()
    {
        $user  = new User();
        $this->task->setUser($user);
        $this->assertEquals($user, $this->task->getUser());
    }
}
