<?php

namespace Skaffold\Tests\Cache;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Skaffold\Cache\CacheRepositoryInterface;
use Skaffold\Cache\FileCacheRepository;

/**
 * @covers \Skaffold\Cache\FileCacheRepository
 */
class FileCacheRepositoryTest extends TestCase
{
    private Filesystem $files;
    private FileCacheRepository $cache;

    protected function setUp(): void
    {
        $this->files = new Filesystem(new LocalFilesystemAdapter(__DIR__));
        $this->cache = new FileCacheRepository($this->files);
    }

    protected function tearDown(): void
    {
        $this->files->deleteDirectory('.cache');
    }

    public function test_it_can_create_repository()
    {
        $this->assertInstanceOf(FileCacheRepository::class, $this->cache);
        $this->assertInstanceOf(CacheRepositoryInterface::class, $this->cache);
    }

    public function test_it_can_get_and_set()
    {
        $file = __DIR__ . '/.cache/' . md5('test') . '.cache';

        $this->assertFileDoesNotExist($file);
        $this->assertEquals(null, $this->cache->get('test'));
        $this->assertEquals('Hey', $this->cache->get('test', 'Hey'));

        $this->cache->set('test', 'Hello world!');

        $this->assertFileExists($file);
        $this->assertEquals('Hello world!', $this->cache->get('test'));
    }

    public function test_it_can_delete()
    {
        $file = __DIR__ . '/.cache/' . md5('test') . '.cache';

        $this->assertFileDoesNotExist($file);

        $this->cache->set('test', 'Test Value');

        $this->assertFileExists($file);
        $this->assertEquals('Test Value', $this->cache->get('test'));

        $this->cache->delete('test');

        $this->assertFileDoesNotExist($file);
        $this->assertEquals(null, $this->cache->get('test'));
    }

    public function test_it_can_flush()
    {
        $file1 = __DIR__ . '/.cache/' . md5('test') . '.cache';
        $file2 = __DIR__ . '/.cache/' . md5('test2') . '.cache';

        $this->cache->set('test', 'Testing');
        $this->cache->set('test2', 'Testing');

        $this->assertFileExists($file1);
        $this->assertFileExists($file2);

        $this->cache->flush();

        $this->assertFileDoesNotExist($file1);
        $this->assertFileDoesNotExist($file2);
    }
}
