<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Entity\Product;

class CartControllerTest extends WebTestCase
{
    public function testAddUser(){
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setFirstName('John');
        $user->setLastName('Jones');
        $user->setEmail('Jones@jhon.com');
        $user->setPassword('JohnJonesMMA');

        $entityManager->persist($user);
        $entityManager->flush();

        $savedUser = $entityManager->getRepository(User::class)->findOneBy(['email' => 'Jones@jhon.com']);

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
            $product = new Product();
            $product->setImage('Vase.jpg');
            $product->setTitle('Vase');
            $product->setPromotion(5);
            $product->setPrice(500);
            $product->setDescription('Vase Grecque');

            $entityManager->persist($product);
            $entityManager->flush();

            $savedProduct = $entityManager->getRepository(Product::class)->findOneBy(['title' => 'Vase']);

            $this->assertInstanceOf(Product::class, $savedProduct);
            $this->assertEquals('Vase', $savedProduct->getTitle());
            $this->assertEquals('Vase.jpg', $savedProduct->getImage());
            $this->assertEquals(5, $savedProduct->getPromotion());
            $this->assertEquals(500, $savedProduct->getPrice());
            $this->assertEquals('Vase Grecque', $savedProduct->getDescription());
        } finally {
            $entityManager->getConnection()->rollBack();
        }
    }
}
