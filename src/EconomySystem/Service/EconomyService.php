<?php

declare(strict_types=1);

namespace EconomySystem\Service;

use EconomySystem\Data\Account\AccountDataInterface;
use EconomySystem\Events\Account\PlayerBalanceIncreaseEvent;
use EconomySystem\Events\Account\PlayerBalanceReduceEvent;
use EconomySystem\Events\Account\PreTransferMoneyEvent;
use EconomySystem\Events\Account\TransferMoneyEvent;
use EconomySystem\Model\Account\AccountInterface;
use EconomySystem\Service\Exception\AmountToReduceHigherThanBalance;
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

    public function transfer(string $from, string $to, int $amount, $origin = 'EconomySystem') : Promise
    {
        $resolver = new PromiseResolver();
        
        $this->getAccount($from)->then(function (AccountInterface $fromAccount) use ($origin, $to, $amount, $resolver, $from) {
            $this->getAccount($to)->then(function (AccountInterface $toAccount) use ($fromAccount, $amount, $origin, $resolver, $from, $to) {
                $event = SystemUtils::callEvent(new PreTransferMoneyEvent($origin, $fromAccount, $toAccount, $amount));
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

                $this->reduceBalance($from, $amount);
                $this->AddToBalance($to, $amount);
                $resolver->resolve(true);
                SystemUtils::callEvent(new TransferMoneyEvent($origin, $fromAccount, $toAccount, $amount));
            });
        });

        return $resolver->getPromise();
    }

    public function setBalance(string $player, int $amount, bool $ignoreNegativeBalance = false) : Promise
    {
        $resolver = new PromiseResolver();

        $this->getAccount($player)->then(function (AccountInterface $account) use ($amount, $ignoreNegativeBalance, $resolver) {

            if($amount < 0 && $ignoreNegativeBalance === false)
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

    public function AddToBalance(string $player, int $amount, string $origin = 'EconomySystem') : Promise
    {
        $resolver = new PromiseResolver();

        $this->getAccount($player)->then(
            function (AccountInterface $account) use ($amount, $origin, $resolver) {

                $event = SystemUtils::callEvent(new PlayerBalanceIncreaseEvent($origin, $account, $amount));
                if($event->isCancelled())
                {
                    return;
                }

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

    public function reduceBalance(string $player, int $amount, bool $ignoreNegativeBalance = false, $origin = 'EconomySystem')
    {
        $resolver = new PromiseResolver();

        $this->getAccount($player)->then(
            function (AccountInterface $account) use ($amount, $ignoreNegativeBalance, $origin, $resolver) {

                $event = SystemUtils::callEvent(new PlayerBalanceReduceEvent($origin, $account, $amount, $ignoreNegativeBalance === false));
                if($event->isCancelled())
                {
                    return;
                }

                if($amount < 0)
                {
                    $resolver->resolve(new MoneyAmountLessThanZeroException());
                    return $resolver->getPromise();
                }

                if(!$account->has($amount) && $event->isIgnoringNegativeBalance())
                {
                    $resolver->resolve(new AmountToReduceHigherThanBalance());
                    return $resolver;
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