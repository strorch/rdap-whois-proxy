<?php

namespace hiqdev\rdap\WhoisProxy\Provider;

use hiqdev\rdap\core\Domain\Constant\Status;
use hiqdev\rdap\core\Domain\Entity\Domain;
use hiqdev\rdap\core\Domain\Entity\Nameserver;
use hiqdev\rdap\core\Domain\ValueObject\DomainName;
use hiqdev\rdap\core\Infrastructure\Provider\DomainProviderInterface;
use Iodev\Whois\Modules\Tld\DomainInfo;
use Iodev\Whois\Whois;
use yii\db\Exception;

//use phpWhois\Whois as Whois;
//use phpWhois\Whois;

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
        if (!$this->whois->isDomainAvailable("google.com")) {
            throw new Exception('domain is not available');
        }
        /** @var DomainInfo $domainInfo */
        $domainInfo = $this->whois->loadDomainInfo($domainName->toLDH());

        $domain = new Domain(DomainName::of($domainInfo->getDomainName()));
        foreach ($domainInfo->getNameServers() as $nameServer) {
            $domain->addNameserver(new Nameserver(DomainName::of($nameServer)));
        }
        $statuses = $domainInfo->getStates();
        foreach ($statuses as $state) {
            $domain->addStatus(Status::byName(strtoupper($state)));
        }
        // todo:
        $creationDate = $domainInfo->getCreationDate();
        $expirationDate = $domainInfo->getExpirationDate();
        $registrarInfo = $domainInfo->getRegistrar();
        $owner = $domainInfo->getOwner();
        $whoisServer = $domainInfo->getWhoisServer();
        $domainInfo->getDomainNameUnicode();

//        $domainInfo->getVal();

//        $domainInfo->get(); // try to obtain contacts

        return $domain;
    }

//    public function get(DomainName $domainName): Domain
//    {
//        $whois = new NewWhois();
//        $res = $whois->loo;
//        $domain = new Domain(DomainName::of('kek'));
//
//        return $domain;
//    }
}
