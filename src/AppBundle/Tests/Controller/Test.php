<?php
namespace AppBundle\Tests\Controller;

use \Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MinicartControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $result = $crawler->filter('html:contains("MiSimpleCart App Symfony2")')->count();
        $expected = 0;
        $this->assertGreaterThan($expected, $result);
    }
}
