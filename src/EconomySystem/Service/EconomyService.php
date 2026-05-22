<?php

declare(strict_types=1);

namespace EconomySystem\Service;

use EconomySystem\Data\Account\AccountDataInterface;
use EconomySystem\Events\Account\PlayerBalanceIncreaseEvent;
use EconomySystem\Events\Account\PlayerBalanceReduceEvent;
use EconomySystem\Events\Account\PreTransferMoneyEvent;
use EconomySystem\Events\Account\TransferMoneyEvent;
use EconomySystem\Model\Account\AccountInterface;
use EconomySystem\Service\Exception\AddToBalanceEventCancelledException;
use EconomySystem\Service\Exception\AmountToReduceHigherThanBalance;
use EconomySystem\Service\Exception\AmountToTransferHigherThanBalance;
use EconomySystem\Service\Exception\MoneyAmountLessThanZeroException;
use EconomySystem\Service\Exception\ReduceBalanceEventCancelledException;
use EconomySystem\Service\Exception\TransferEventCancelledException;
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

    public function getAccount(string $player) : AccountInterface
    {
        $playerName = strtolower($player);
        if(isset($this->cache[$playerName]))
        {
            return $this->cache[$playerName];
        }
        $this->data->load($playerName)->then(
            function (AccountInterface $result) use ($playerName) {
                $this->cache[$playerName] =  $result;
            }
        );
        return $this->cache[$playerName];
    }

    public function transfer(string $from, string $to, int $amount, $origin = 'EconomySystem') : Promise
    {
        $resolver = new PromiseResolver();
        $fromAccount = $this->getAccount($from);
        $toAccount = $this->getAccount($to);
        $event = SystemUtils::callEvent(new PreTransferMoneyEvent($origin, $fromAccount, $toAccount, $amount));
        if($event->isCancelled())
        {
            $resolver->resolve(false);
            return $resolver->getPromise();
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
        return $resolver->getPromise();
    }

    public function setBalance(string $player, int $amount, bool $ignoreNegativeBalance = false) : Promise
    {
        $resolver = new PromiseResolver();

        $account = $this->getAccount($player);

        if($amount < 0 && $ignoreNegativeBalance === false)
        {
            $resolver->resolve(new MoneyAmountLessThanZeroException());
            return $resolver->getPromise();
        }

        $account->setBalance($amount);
        $this->data->save($account);
        $resolver->resolve(true);
        return $resolver->getPromise();
    }

    public function getBalance(string $player)
    {
        $resolver = new PromiseResolver();
        $account = $this->getAccount($player);
        $resolver->resolve($account->getBalance());
        return $resolver->getPromise();
    }

    public function AddToBalance(string $player, int $amount, string $origin = 'EconomySystem') : Promise
    {
        $resolver = new PromiseResolver();

        $account = $this->getAccount($player);

        $event = SystemUtils::callEvent(new PlayerBalanceIncreaseEvent($origin, $account, $amount));
        if($event->isCancelled())
        {
            $resolver->resolve(false);
            return $resolver->getPromise();
        }

        if($amount < 0)
        {
            $resolver->resolve(new MoneyAmountLessThanZeroException());
            return $resolver->getPromise();
        }
        
        $account->setBalance($account->getBalance() + $amount);
        $this->data->save($account);
                $resolver->resolve($account->getBalance());
        return $resolver->getPromise();
    }

    public function reduceBalance(string $player, int $amount, bool $ignoreNegativeBalance = false, $origin = 'EconomySystem')
    {
        $resolver = new PromiseResolver();

        $account = $this->getAccount($player);

        $event = SystemUtils::callEvent(new PlayerBalanceReduceEvent($origin, $account, $amount, $ignoreNegativeBalance === false));
        if($event->isCancelled())
        {
            $resolver->resolve(false);
            return $resolver->getPromise();
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
        return $resolver->getPromise();
    }

    public function saveAndClearCache(string $player)
    {
        $account = $this->getAccount($player);
        $this->data->save($account);
        $playerName = $account->getPlayerName();
        unset($this->cache[$playerName]);
    }

}