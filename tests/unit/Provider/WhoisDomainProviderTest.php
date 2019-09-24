<?php

namespace hiqdev\rdap\WhoisProxy\tests\unit\Provider;

use hiqdev\rdap\core\Domain\ValueObject\DomainName;
use hiqdev\rdap\WhoisProxy\Provider\WhoisDomainProvider;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Loaders\ILoader;
use Iodev\Whois\Whois;

class WhoisDomainProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WhoisDomainProvider
     */
    private $whoisDomainProvider;

    protected function setUp()
    {
        $whois = Whois::create(new class implements ILoader {
            function loadText($whoisHost, $query)
            {
                return file_get_contents(sprintf("%s/../stub/%s.txt", __DIR__, trim($query)));
            }
        });
        $this->whoisDomainProvider = new WhoisDomainProvider($whois);
    }

    public function testGet()
    {
        $domain = $this->whoisDomainProvider->get(DomainName::of('google.com'));

        $this->assertTrue($domain->getLdhName()->equals(DomainName::of('google.com')));
        var_dump($domain->getNameservers());
    }
}
