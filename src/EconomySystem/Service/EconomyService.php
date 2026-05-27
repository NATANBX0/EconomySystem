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
use EconomySystem\Utils\EconomySystemConfig;
use EconomySystem\Utils\Promise\Promise;
use EconomySystem\Utils\Promise\PromiseResolver;
use EconomySystem\Utils\SystemUtils;
use pocketmine\Player;

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

    /**
     * @param AccountInterface|Player|string $player
     * @return Promise
     */
    public function getAccount($player) : Promise
    {
        $playerName = $this->getType($player);
        if(isset($this->cache[$playerName]))
        {
            return $this->cache[$playerName];
        }
        return $this->cache[$playerName] = $this->data->load($playerName);
    }

    /**
     * @param AccountInterface|Player|string $from
     * @param AccountInterface|Player|string $to
     * @param int $amount
     * @param mixed $origin
     * @return Promise
     */
    public function transfer($from, $to, int $amount, $origin = 'EconomySystem') : Promise
    {
        $resolver = new PromiseResolver();
        $this->getAccount($from)->then(function (AccountInterface $fromAccount) use ($origin, $to, $amount, $resolver, $from) {
            $this->getAccount($to)->then(function (AccountInterface $toAccount) use ($fromAccount, $amount, $origin, $resolver, $from, $to) {
                $event = SystemUtils::callEvent(new PreTransferMoneyEvent($origin, $fromAccount, $toAccount, $amount));
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

    /**
     * Summary of setBalance
     * @param AccountInterface|Player|string $player
     * @param int $amount
     * @param bool $ignoreNegativeBalance
     * @return Promise
     */
    public function setBalance($player, int $amount, bool $ignoreNegativeBalance = false) : Promise
    {
        $resolver = new PromiseResolver();

        $this->getAccount($player)->then(
            function (AccountInterface $account) use ($amount, $ignoreNegativeBalance, $resolver) {
                if($amount < 0 && $ignoreNegativeBalance === false)
                {
                    $resolver->resolve(new MoneyAmountLessThanZeroException());
                    return $resolver->getPromise();
                }

                $account->setBalance($amount);
                $this->data->save($account);
                $resolver->resolve(true);
            }
        );
        return $resolver->getPromise();
    }

    /**
     * @param AccountInterface|Player|string $player
     * @return Promise
     */
    public function getBalance($player)
    {
        $resolver = new PromiseResolver();
        $this->getAccount($player)->then(function (AccountInterface $account) use ($resolver) {
            $resolver->resolve($account->getBalance());
        });
        return $resolver->getPromise();
    }

    /**
     * @param AccountInterface|Player|string $player
     * @param int $amount
     * @param mixed $origin
     * @return Promise
     */
    public function AddToBalance($player, int $amount, $origin = 'EconomySystem') : Promise
    {
        $resolver = new PromiseResolver();

        $this->getAccount($player)->then(
            function (AccountInterface $account) use ($amount, $origin, $resolver) {

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
            }
        );
        return $resolver->getPromise();
    }

    /**
     * @param AccountInterface|Player|string $player
     * @param int $amount
     * @param mixed $origin
     * @return Promise
     */
    public function reduceBalance($player, int $amount, $origin = 'EconomySystem')
    {
        $resolver = new PromiseResolver();

        $this->getAccount($player)->then(
            function (AccountInterface $account) use ($amount, $origin, $resolver) {

                $event = SystemUtils::callEvent(new PlayerBalanceReduceEvent($origin, $account, $amount));
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

                if(!$account->has($amount))
                {
                    $resolver->resolve(new AmountToReduceHigherThanBalance());
                    return $resolver->getPromise();
                }
                
                $account->withdraw($amount);
                $this->data->save($account);
                $resolver->resolve($account->getBalance());
            }
        );
        return $resolver->getPromise();
    }

    /**
     * @param AccountInterface|Player|string $player
     * @return void
     */
    public function saveAndClearCache($player)
    {
        $this->getAccount($player)->then(
            function(AccountInterface $account) {
                $this->data->save($account);
                $playerName = $account->getPlayerName();
                unset($this->cache[$playerName]);
            }
        );
    }

    /**
     * @param AccountInterface|Player|string $entry
     * @return string
     */
    public function getType($entry) : string
    {
        if($entry instanceof AccountInterface)
        {
            return strtolower($entry->getPlayerName());
        } 
        else if($entry instanceof Player)
        {
            return strtolower($entry->getName());
        } 
        else 
        {
            return strtolower($entry);
        }
    }
}