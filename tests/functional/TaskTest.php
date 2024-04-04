<?php
namespace App\Tests\Functional;

use App\DataFixtures\UsersFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class TaskTest extends WebTestCase
{
  protected KernelBrowser|null $client = null;
  protected EntityManagerInterface|null $em = null;

  public function setUp() : void
  {
    $this->client = static::createClient();
    $container = $this->client->getContainer();
    $this->em = $container->get('doctrine.orm.entity_manager');
    $userPasswordHasher = $container->get('security.user_password_hasher');
    $fixture = new UsersFixtures($userPasswordHasher);
    $fixture->load($this->em);
  }

  protected function tearDown(): void
  {
    parent::tearDown();
    $purger = new ORMPurger($this->em);
    $purger->purge();
  }

  public function testLogin()
  {

    //login
    $crawlerLogin = $this->client->request(Request::METHOD_GET, '/login');
    $this->assertResponseIsSuccessful();
    $extract = $crawlerLogin->filter('input[name="_csrf_token"]')->extract(['value']);
    $csrf_token = $extract[0];
    $this->client->submitForm('Se connecter', [
      '_username' => 'admin1',
      '_password' => 'password',
      '_csrf_token' => $csrf_token
    ]);
    $this->assertResponseRedirects();
    $this->client->followRedirect();
    $this->assertSelectorExists("a[href='/logout']");
    $this->assertSelectorExists("a[href='/register']");

    // homepage
    $this->client->request(Request::METHOD_GET, '/login');
    $this->client->followRedirect();
    $this->assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");

    //register
    $crawlerRegister = $this->client->request(Request::METHOD_GET, '/register');
    $this->assertResponseIsSuccessful();
    $this->assertSelectorTextContains('h1', "Créer un utilisateur");
    $extract = $crawlerRegister->filter('input[name="registration_form[_token]"]')->extract(['value']);
    $registration_token = $extract[0];
    $this->client->submitForm('Ajouter', [
      'registration_form[username]' => 'test',
      'registration_form[email]' => 'test@test.com',
      'registration_form[plainPassword]' => 'testtest',
      'registration_form[roles]' => '1',
      'registration_form[agreeTerms]' => '1',
      'registration_form[_token]' => $registration_token
    ]);

    $this->client->followRedirect();

    //task
    $crawlerTask = $this->client->request(Request::METHOD_GET, '/tasks/create');
    $this->assertResponseIsSuccessful();
    $this->assertSelectorExists("form[name='task']");
    $extract = $crawlerTask->filter('input[name="task[_token]"]')->extract(['value']);
    $token = $extract[0];
    $this->client->submitForm('Ajouter', [
      'task[title]' => 'test functional',
      'task[content]' => 'test',
      'task[_token]' => $token,
    ]);
    $this->assertResponseRedirects();
    $this->client->followRedirect();
    $this->client->clickLink('test functional');
    $this->assertResponseIsSuccessful();
    $this->assertSelectorExists("form[name='task']");
    $extract = $crawlerTask->filter('input[name="task[_token]"]')->extract(['value']);
    $token = $extract[0];
    $this->client->submitForm('Modifier', [
      'task[title]' => 'test functional edited',
      'task[content]' => 'test',
      'task[_token]' => $token,
    ]);
    $this->assertResponseRedirects();
    $this->client->followRedirect();
    $this->assertAnySelectorTextContains('button', 'Supprimer');
    $this->client->submitForm('Marquer comme faite', []);
    $this->assertResponseRedirects();
    $this->client->followRedirect();
    $this->client->submitForm('Marquer non terminée', []);
    $this->assertResponseRedirects();
    $this->client->followRedirect();
    $this->client->submitForm('Supprimer', []);
    $this->assertResponseRedirects();
    $this->client->followRedirect();

    //user
    $crawlerUsers = $this->client->request(Request::METHOD_GET, '/users');
    $this->assertResponseIsSuccessful();
    $this->assertAnySelectorTextContains("h1", "Liste des utilisateurs");
    $crawlerEditUser = $this->client->clickLink('Edit');
    $this->assertResponseIsSuccessful();
    $extract = $crawlerEditUser->filter('input[name="user[_token]"]')->extract(['value']);
    $edit_user_token = $extract[0];
    $this->client->submitForm('Modifier', [
      'user[username]' => 'admin1',
      'user[email]' => 'admin@todolist.com',
      'user[plainPassword]' => 'password',
      'user[roles]' => '1',
      'user[_token]' => $edit_user_token
    ]);
    $this->assertResponseRedirects();
    $this->client->followRedirect();
    $this->assertAnySelectorTextContains("h1", "Liste des utilisateurs");
  }
}