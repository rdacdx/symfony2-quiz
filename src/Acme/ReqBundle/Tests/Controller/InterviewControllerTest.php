<?php

namespace Acme\ReqBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Andrea Fiori
 * @since  22 October 2014
 */
class InterviewControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($crawler->filter('html:contains("interview")')->count() > 0);
    }
}