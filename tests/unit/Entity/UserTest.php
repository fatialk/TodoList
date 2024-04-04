<?php
namespace App\Tests\Unit\Entity;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User;
    }

    public function testGetUsername()
    {
        $this->user->setUsername('usertest');
        $this->assertEquals('usertest', $this->user->getUsername());
    }

    public function testGetPassword()
    {
        $this->user->setPassword('password');
        $this->assertEquals('password', $this->user->getPassword());
    }

    public function testGetEmail()
    {
        $this->user->setEmail('test@gmail.com');
        $this->assertEquals('test@gmail.com', $this->user->getEmail());
        $this->assertEquals('test@gmail.com', $this->user->getUserIdentifier());
    }

    public function testGetRoles()
    {
        $this->user->setRoles(['ROLE_USER']);
        $this->assertEquals(['ROLE_USER'], $this->user->getRoles());
    }

    public function testAddTask()
    {
        $this->user->addTask(new Task());

        $this->assertInstanceOf(ArrayCollection::class, $this->user->getTasks());
    }

    public function testRemoveTask()
    {
        $task = new Task();
        $this->user->addTask($task);
        $user = $this->user->removeTask($task);
        $this->assertInstanceOf(User::class, $user);
    }

    public function testGetSalt()
    {
        $this->assertEquals(null, $this->user->getSalt());
    }

}
