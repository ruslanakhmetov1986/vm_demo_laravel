<?php


namespace App\Services;

use App\Models\BankCoin;
use App\Models\Drink;
use App\Models\VmSetting;
use App\Models\WalletCoin;
use Illuminate\Support\Facades\DB;

class VmService
{
    public function index(){

        $this->updateCashTotals();

        $data['drinks'] = Drink::all();
        $data['bank'] = BankCoin::all();
        $data['wallet'] = WalletCoin::all();
        $data['deposit'] = $this->getSettingByKey('deposit');
        $data['userWalletTotal'] = $this->getSettingByKey('user_wallet_total');
        $data['vmBankTotal'] =  $this->getSettingByKey('vm_bank_total');

        return $data;
    }

    public function moveCoinToWallet($coin){

        $bankCoin = BankCoin::find( $coin['name'] );

        if ($bankCoin->amount > 0) {

            $bankCoin->amount = $bankCoin->amount - 1;
            $bankCoin->save();

            $walletCoin = WalletCoin::find( $bankCoin->name );
            $walletCoin->amount = intval( $walletCoin->amount ) + 1;
            $walletCoin->save();

            $this->depositPut($bankCoin->name);
        }

        return true;
    }

    public function moveCoinToVM($coin){

        $walletCoin = WalletCoin::find( $coin['name'] );

        if ($walletCoin->amount > 0) {

            $walletCoin->amount = $walletCoin->amount - 1;
            $walletCoin->save();

            $bankCoin = BankCoin::find( $walletCoin->name );
            $bankCoin->amount = intval( $bankCoin->amount ) + 1;
            $bankCoin->save();

            $this->depositPut($walletCoin->name);
        }

        return true;
    }

    public function buy($drink){

        $drink = Drink::find($drink['id']);

        $drink->amount = $drink->amount - 1;
        $drink->save();

        $this->depositCut($drink->price);

        $this->updateCashTotals();

        return true;
    }


    public function getChange(){

        $bankCoins = BankCoin::orderBy('name','desc')->get();
        $deposit = $this->depositGet();

        if($deposit <= 0){
            return false;
        }

        foreach ($bankCoins as $bankCoin) {

            for ($i = 0; $i <= $bankCoin->amount; $i++) {

                if($deposit < $bankCoin->name){
                    continue;
                }

                $deposit = $deposit - $bankCoin->name;

                $this->moveCoinToWallet($bankCoin);
            }
        }

        $this->depositSet($deposit);

        return true;
    }

    public function depositPut($amount){
        $oldDeposit = $this->getSettingByKey('deposit');
        $newDeposit = $oldDeposit + $amount ;
        $this->setSettingByKey('deposit', $newDeposit);
        $this->updateCashTotals();
        return $newDeposit;
    }

    public function depositCut($amount){
        $oldDeposit = $this->getSettingByKey('deposit');
        $newDeposit = $oldDeposit - $amount ;
        $this->setSettingByKey('deposit', $newDeposit);
        $this->updateCashTotals();
        return $newDeposit;
    }

    public function depositGet(){
        return $this->getSettingByKey('deposit');
    }

    public function depositSet($amount){
        $this->setSettingByKey('deposit', $amount);
    }

    public function calculateTotal($items){

        $total = 0;

        foreach ($items as $item) {
            $total += intval ($item->name ) * intval( $item->amount );
        }

        return $total;
    }

    private function updateCashTotals(){
        $this->setSettingByKey('user_wallet_total', $this->calculateTotal(WalletCoin::all()));
        $this->setSettingByKey('vm_bank_total',  $this->calculateTotal(BankCoin::all()));
    }


    public function getSettingByKey($key){
        return VmSetting::find($key)->val;
    }

    public function setSettingByKey($key, $val){
        VmSetting::updateOrCreate(
            ['key' =>  $key],
            ['val' => $val]
        );
    }

    public function resetDemoData(){

        //clear tables
        DB::transaction(function() {
            VmSetting::query()->truncate();
            Drink::query()->truncate();
            BankCoin::query()->truncate();
            WalletCoin::query()->truncate();
        });

        //insert data to tables
        $bankCoinSeeder = new \Database\Seeders\WalletCoinSeeder();
        $walletCoinSeeder = new \Database\Seeders\BankCoinSeeder();
        $drinkSeeder = new \Database\Seeders\DrinkSeeder();
        $vmSettingSeeder = new \Database\Seeders\VmSettingSeeder();

        $bankCoinSeeder->run();
        $walletCoinSeeder->run();
        $drinkSeeder->run();
        $vmSettingSeeder->run();

        return true;
    }


}
