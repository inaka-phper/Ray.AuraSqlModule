<?php
namespace Ray\AuraSqlModule\Pagerfanta;

use Ray\AuraSqlModule\AuraSqlModule;
use Ray\AuraSqlModule\FakePagerInject;
use Ray\Di\Injector;

class AuraSqlPagerModuleTest extends AbstractPdoTestCase
{
    public function testNewInstance()
    {
        $factory = (new Injector(new AuraSqlPagerModule()))->getInstance(AuraSqlPagerFactoryInterface::class);
        /* @var $factory AuraSqlPagerFactoryInterface */
        $this->assertInstanceOf(AuraSqlPagerFactory::class, $factory);
        $sql = 'SELECT * FROM posts';
        $pager = $factory->newInstance($this->pdo, $sql, [], 1, '/?page={page}&category=sports');
        $this->assertInstanceOf(AuraSqlPager::class, $pager);

        return $pager;
    }

    /**
     * @depends testNewInstance
     */
    public function testArrayAccess(AuraSqlPagerInterface $pager)
    {
        /** @var $page Page */
        $page = $pager[2];
        $this->assertTrue($page->hasNext);
        $this->assertTrue($page->hasPrevious);
        $expected = [
                [
                    'id' => '2',
                    'username' => 'BEAR',
                    'post_content' => 'entry #2',
                ],
        ];
        $this->assertSame($expected, $page->data);
        $expected = '<nav><a href="/?page=1&category=sports">Previous</a><a href="/?page=1&category=sports">1</a><span class="current">2</span><a href="/?page=3&category=sports">3</a><a href="/?page=4&category=sports">4</a><a href="/?page=5&category=sports">5</a><span class="dots">...</span><a href="/?page=50&category=sports">50</a><a href="/?page=3&category=sports">Next</a></nav>';
        $this->assertSame($expected, (string) $page);
        $this->assertSame(50, $page->total);
    }

    public function testInjectPager()
    {
        /** @var $fakeInject FakePagerInject */
        $fakeInject = (new Injector(new AuraSqlModule('')))->getInstance(FakePagerInject::class);
        list($pager, $queryPager) = $fakeInject->get();
        $this->assertInstanceOf(AuraSqlPagerFactoryInterface::class, $pager);
        $this->assertInstanceOf(AuraSqlQueryPagerFactoryInterface::class, $queryPager);
    }
}
