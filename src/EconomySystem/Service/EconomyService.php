<?php

declare(strict_types=1);

namespace EconomySystem\Service;

use EconomySystem\Data\AccountDataInterface;
use EconomySystem\Events\Money\PreTransferMoneyEvent;
use EconomySystem\Events\Money\TransferMoneyEvent;
use EconomySystem\Model\AccountInterface;
use EconomySystem\Service\Exception\AmountToTransferHigherThanBalance;
use EconomySystem\Service\Exception\MoneyAmountLessThanZeroException;
use EconomySystem\Utils\Promise\Promise;
use EconomySystem\Utils\Promise\PromiseResolver;
use EconomySystem\Utils\SystemUtils;

class EconomyService
{
    /**
     * @var AccountDataInterface
     */
    protected $data;

    protected $cache = [];

    public function __construct(AccountDataInterface $data)
    {
        $this->data = $data;
    }

    public function getAccount(string $player) : Promise
    {
        $playerName = strtolower($player);
        if(isset($this->cache[$playerName]))
        {
            return $this->cache[$playerName];
        }
        return $this->cache[$playerName] = $this->data->load($playerName);
    }

    public function transfer(string $from, string $to, int $amount) : Promise
    {
        $resolver = new PromiseResolver();
        
        $this->getAccount($from)->then(function (AccountInterface $fromAccount) use ($to, $amount, $resolver) {
            $this->getAccount($to)->then(function (AccountInterface $toAccount) use ($fromAccount, $amount, $resolver) {
                $event = SystemUtils::callEvent(new PreTransferMoneyEvent('EconomySystem', $fromAccount, $toAccount, $amount));
                if($event->isCancelled())
                {
                    return;
                }
                if($amount < 0) 
                {
                    $resolver->resolve(new MoneyAmountLessThanZeroException());
                    $resolver->getPromise();
                }

                if(!$fromAccount->has($amount))
                {
                    $resolver->resolve(new AmountToTransferHigherThanBalance());
                    return $resolver->getPromise();
                }

                $fromAccount->withdraw($amount);
                $toAccount->deposit($amount);
                $this->data->save($fromAccount);
                $this->data->save($toAccount);
                $resolver->resolve(true);
                SystemUtils::callEvent(new TransferMoneyEvent('EconomySystem', $fromAccount, $toAccount, $amount));
            });
        });

        return $resolver->getPromise();
    }

    public function setBalance(string $player, int $amount) : Promise
    {
        $resolver = new PromiseResolver();

        $this->getAccount($player)->then(function (AccountInterface $account) use ($amount, $resolver) {

            if($amount < 0)
            {
                $resolver->resolve(new MoneyAmountLessThanZeroException());
                return $resolver->getPromise();
            }

            $account->setBalance($amount);
            $this->data->save($account);
            $resolver->resolve(true);
        });
        return $resolver->getPromise();
    }

    public function getBalance(string $player)
    {
        $resolver = new PromiseResolver();
        $this->getAccount($player)->then(function (AccountInterface $account) use ($resolver) {
            $resolver->resolve($account->getBalance());
        });
        return $resolver->getPromise();
    }

    public function AddToBalance(string $player, int $amount) : Promise
    {
        $resolver = new PromiseResolver();

        $this->getAccount($player)->then(
            function (AccountInterface $account) use ($amount, $resolver) {

                if($amount < 0)
                {
                    $resolver->resolve(new MoneyAmountLessThanZeroException());
                    return $resolver->getPromise();
                }

                $account->setBalance($account->getBalance() + $amount);
                $this->data->save($account);
                $resolver->resolve($account->getBalance());
            }
        );
        return $resolver->getPromise();
    }

    public function reduceBalance(string $player, int $amount)
    {
        $resolver = new PromiseResolver();

        $this->getAccount($player)->then(
            function (AccountInterface $account) use ($amount, $resolver) {

                if($amount < 0)
                {
                    $resolver->resolve(new MoneyAmountLessThanZeroException());
                    return $resolver->getPromise();
                }

                $account->withdraw($amount);
                $this->data->save($account);
                $resolver->resolve($account->getBalance());
            }
        );
        return $resolver->getPromise();
    }

    public function saveAndClearCache(string $player)
    {
        $this->getAccount($player)->then(
            function(AccountInterface $account) {
                $this->data->save($account);
                $playerName = $account->getPlayerName();
                unset($this->cache[$playerName]);
            }
        );
    }

}