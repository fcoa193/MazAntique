<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Entity\Product;
use App\Service\PasswordService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CartControllerTest extends WebTestCase
{
    public function testAddUser(){
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

// create a User in my fake DB
        $user = new User();
        $user->setFirstName('John');
        $user->setLastName('Jones');
        $user->setEmail('Jones@jhon.com');
        $user->setPassword('JohnJonesMMA');

        $entityManager->persist($user);
        $entityManager->flush();

        $savedUser = $entityManager->getRepository(User::class)->findOneBy(['email' => 'Jones@jhon.com']);

// Check in my fake DB if the User was indeed added
        $this->assertInstanceOf(User::class, $savedUser);
        $this->assertEquals('John', $savedUser->getFirstName());
        $this->assertEquals('Jones', $savedUser->getLastName());
        $this->assertEquals('Jones@jhon.com', $savedUser->getEmail());
        $this->assertEquals('JohnJonesMMA', $savedUser->getPassword());
    }

    public function testAddProduct()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $entityManager->getConnection()->beginTransaction();

        try {
            
// create a new Product in my fake DB
            $product = new Product();
            $product->setImage('Vase.jpg');
            $product->setTitle('Vase');
            $product->setPromotion(5);
            $product->setPrice(500);
            $product->setDescription('Vase Grecque');

            $entityManager->persist($product);
            $entityManager->flush();

            $savedProduct = $entityManager->getRepository(Product::class)->findOneBy(['title' => 'Vase']);

// Check in my fake DB if the User was added
            $this->assertInstanceOf(Product::class, $savedProduct);
            $this->assertEquals('Vase', $savedProduct->getTitle());
            $this->assertEquals('Vase.jpg', $savedProduct->getImage());
            $this->assertEquals(5, $savedProduct->getPromotion());
            $this->assertEquals(500, $savedProduct->getPrice());
            $this->assertEquals('Vase Grecque', $savedProduct->getDescription());

// Prevent changes on the db
        } finally {
            $entityManager->getConnection()->rollBack();
        }
    }

// Intergration test 
    public function testHashPassword(): void
    {
        // Mocking UserPasswordHasherInterface
        $userPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $passwordService = new PasswordService($userPasswordHasher);

        // Mock a User
        $user = new User();

        // Mocking plain password
        $plainPassword = 'some_password';

        // Mocking hashed password
        $hashedPassword = 'hashed_password';

        // look if the hashPassword method works
        $userPasswordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, $plainPassword)
            ->willReturn($hashedPassword);

        // Call the method to hash the password
        $result = $passwordService->hashPassword($user, $plainPassword);

        // Check if the hashing worked
        $this->assertEquals($hashedPassword, $result);
    }
}
