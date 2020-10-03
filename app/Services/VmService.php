<?php


namespace App\Services;

use App\Models\BankCoin;
use App\Models\Drink;
use App\Models\VmSetting;
use App\Models\WalletCoin;
use Database\Seeders\BankCoinSeeder;
use Database\Seeders\DrinkSeeder;
use Database\Seeders\VmSettingSeeder;
use Database\Seeders\WalletCoinSeeder;
use Illuminate\Support\Facades\DB;

class VmService
{
    /**
     * @return array
     */
    public function index() : array
    {

        $this->updateCashTotals();

        $data['drinks'] = Drink::all();
        $data['bank'] = BankCoin::all();
        $data['wallet'] = WalletCoin::all();
        $data['deposit'] = $this->getSettingByKey('deposit');
        $data['userWalletTotal'] = $this->getSettingByKey('user_wallet_total');
        $data['vmBankTotal'] = $this->getSettingByKey('vm_bank_total');

        return $data;
    }

    /**
     * @param array $coin
     * @return bool
     */
    public function moveCoinToWallet(array $coin): bool
    {

        $bankCoin = BankCoin::find($coin['name']);

        if ($bankCoin->amount > 0) {

            $bankCoin->amount = $bankCoin->amount - 1;
            $bankCoin->save();

            $walletCoin = WalletCoin::find($bankCoin->name);
            $walletCoin->amount = intval($walletCoin->amount) + 1;
            $walletCoin->save();

            $this->depositPut($bankCoin->name);
        }

        return true;
    }

    /**
     * @param array $coin
     * @return bool
     */
    public function moveCoinToVM(array $coin): bool
    {

        $walletCoin = WalletCoin::find($coin['name']);

        if ($walletCoin->amount > 0) {

            $walletCoin->amount = $walletCoin->amount - 1;
            $walletCoin->save();

            $bankCoin = BankCoin::find($walletCoin->name);
            $bankCoin->amount = intval($bankCoin->amount) + 1;
            $bankCoin->save();

            $this->depositPut($walletCoin->name);
        }

        return true;
    }

    /**
     * @param array $drink
     * @return bool
     */
    public function buy(array $drink): bool
    {

        $drink = Drink::find($drink['id']);

        $drink->amount = $drink->amount - 1;
        $drink->save();

        $this->depositCut($drink->price);

        $this->updateCashTotals();

        return true;
    }

    /**
     * @return bool
     */
    public function getChange(): bool
    {

        $bankCoins = BankCoin::orderBy('name', 'desc')->get();
        $deposit = $this->depositGet();

        if ($deposit <= 0) {
            return false;
        }

        foreach ($bankCoins as $bankCoin) {

            for ($i = 0; $i <= $bankCoin->amount; $i++) {

                if ($deposit < $bankCoin->name) {
                    continue;
                }

                $deposit = $deposit - $bankCoin->name;

                $this->moveCoinToWallet($bankCoin);
            }
        }

        $this->depositSet($deposit);

        return true;
    }

    /**
     * @param int $amount
     * @return int
     */
    public function depositPut(int $amount): int
    {
        $oldDeposit = $this->getSettingByKey('deposit');
        $newDeposit = $oldDeposit + $amount;
        $this->setSettingByKey('deposit', $newDeposit);
        $this->updateCashTotals();
        return $newDeposit;
    }

    /**
     * @param int $amount
     * @return int
     */
    public function depositCut(int $amount): int
    {
        $oldDeposit = $this->getSettingByKey('deposit');
        $newDeposit = $oldDeposit - $amount;
        $this->setSettingByKey('deposit', $newDeposit);
        $this->updateCashTotals();
        return $newDeposit;
    }

    /**
     * @return int
     */
    public function depositGet(): int
    {
        return $this->getSettingByKey('deposit');
    }


    /**
     * @param $amount
     * @return int
     */
    public function depositSet($amount) : int
    {
        $this->setSettingByKey('deposit', $amount);
    }

    /**
     * @param array $items
     * @return int
     */
    public function calculateTotal(array $items) : int
    {

        $total = 0;

        foreach ($items as $item) {
            $total += intval($item->name) * intval($item->amount);
        }

        return $total;
    }

    /**
     * @return bool
     */
    private function updateCashTotals() : bool
    {
        $this->setSettingByKey('user_wallet_total', $this->calculateTotal(WalletCoin::all()));
        $this->setSettingByKey('vm_bank_total', $this->calculateTotal(BankCoin::all()));

        return true;
    }


    /**
     * @param string $key
     * @return string
     */
    public function getSettingByKey(string $key) : string
    {
        return VmSetting::find($key)->val;
    }

    /**
     * @param string $key
     * @param string $val
     * @return bool
     */
    public function setSettingByKey(string $key, string $val)  : bool
    {
        VmSetting::updateOrCreate(
            ['key' => $key],
            ['val' => $val]
        );

        return true;
    }

    /**
     * @return bool
     */
    public function resetDemoData() : bool
    {

        //clear tables
        DB::transaction(function () {
            VmSetting::query()->truncate();
            Drink::query()->truncate();
            BankCoin::query()->truncate();
            WalletCoin::query()->truncate();
        });

        //insert data to tables
        $bankCoinSeeder = new WalletCoinSeeder();
        $walletCoinSeeder = new BankCoinSeeder();
        $drinkSeeder = new DrinkSeeder();
        $vmSettingSeeder = new VmSettingSeeder();

        $bankCoinSeeder->run();
        $walletCoinSeeder->run();
        $drinkSeeder->run();
        $vmSettingSeeder->run();

        $this->updateCashTotals();

        return true;
    }


}
