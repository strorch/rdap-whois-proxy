<?php

namespace hiqdev\rdap\WhoisProxy\Provider;

use hiqdev\rdap\core\Domain\Constant\Status;
use hiqdev\rdap\core\Domain\Entity\Domain;
use hiqdev\rdap\core\Domain\Entity\Nameserver;
use hiqdev\rdap\core\Domain\ValueObject\DomainName;
use hiqdev\rdap\core\Infrastructure\Provider\DomainProviderInterface;
use Iodev\Whois\Whois;

class WhoisDomainProvider implements DomainProviderInterface
{
    /**
     * @var Whois
     */
    private $whois;

    public function __construct(Whois $whois)
    {
        $this->whois = $whois;
    }

    public function get(DomainName $domainName): Domain
    {
        $domainInfo = $this->whois->loadDomainInfo($domainName->toLDH());

        $domain = new Domain($domainInfo->getDomainName());
        foreach ($domainInfo->getNameServers() as $nameServer) {
            $domain->addNameserver(new Nameserver(DomainName::of($nameServer)));
        }
        foreach ($domainInfo->getStates() as $state) {
            $domain->addStatus(Status::byName($state));
        }
        // todo:
        $domainInfo->getCreationDate();
        $domainInfo->getExpirationDate();
        $domainInfo->getRegistrar();
        $domainInfo->get(); // try to obtain contacts

        return $domain;
    }
}
