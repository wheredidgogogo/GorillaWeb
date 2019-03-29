<?php

namespace Tests\Unit\Entities;

use Gorilla\Contracts\EntityInterface;
use Gorilla\Contracts\MethodType;
use Gorilla\Contracts\RequestInterface;
use Gorilla\Entities\Menu;
use PHPUnit\Framework\TestCase;

class MenuTest extends TestCase
{
    /**
     * @var Menu
     */
    private $menu;

    protected function setUp(): void
    {
        parent::setUp();
        $this->menu = new Menu();
    }

    /** @test */
    public function get_method()
    {
        // Assert
        $this->assertEquals(MethodType::GET, $this->menu->method());
    }

    /** @test */
    public function get_parameters()
    {
        $this->assertEquals([], $this->menu->parameters());
    }

    /** @test */
    public function get_all_menus_endpoint()
    {
       // Assert
        $this->assertEquals('/website/menus', $this->menu->endpoint());
    }

    /** @test */
    public function get_single_menu()
    {
        // Arrange
        $menu = new Menu(['name']);

        // Assert
        $this->assertEquals('/website/menus/name', $menu->endpoint());
    }
}

class Request implements RequestInterface
{

    /**
     * @param EntityInterface $entity
     *
     * @return mixed
     */
    public function request(EntityInterface $entity)
    {
        return null;
    }
}
