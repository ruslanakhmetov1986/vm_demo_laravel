<template>
  <div class="vm-wrapper d-flex flex-column">
    <div class="d-flex">

<!--      Витрина-->

      <div class="left w-50">

        <div class="mt-5 h2">
          Витрина
        </div>

        <div class="vm-items mt-5 d-flex flex-row">
          <template v-for="item in items">
            <el-card class="vm-items__card mr-3" :body-style="{ padding: '0px' }">
              <img src="../../images/tea.jpg" class="image">
              <div class="p-3 d-flex flex-column">
                <div class="font-weight-bold" style="line-height: 1.1em; min-height: 40px">{{item.name}}</div>
                <div class="d-flex flex-column justify-content-center mt-2">

                  <div class="text-info" style="white-space: nowrap">{{item.amount}} шт.</div>
                  <div class="font-weight-bold" style="white-space: nowrap">{{item.price}} ₽</div>

                  <el-button @click="buy(item)" type="text">Купить</el-button>
                </div>
              </div>
            </el-card>
          </template>
        </div>
      </div>


<!--      Оплата вендинговой машины-->

      <div class="right w-50">
        <div class="mt-5 h2">
          Внесенная сумма
        </div>

        <div class="mt-2 h2">
          {{deposit}} руб.
        </div>

        <div class="mt-5 mb-5">
          <el-button @click="getChange()" class="">Сдача</el-button>
        </div>

        <div class="mt-5 text-danger">
          В вендинговой машине сейчас: {{vmBankTotal}} руб. <br>
          <template v-for="item in bank">
            {{item.name}}₽ ({{item.amount}} шт.)
          </template>
        </div>
      </div>
    </div>

    <el-divider></el-divider>

<!--    Кошелек пользователя-->

    <div class="mt-3 mb-4">
      <h2>Кошелек пользователя: {{userWalletTotal}} руб.</h2>
      <div class="d-flex flex-row justify-content-center">
        <template v-for="item in wallet">

          <div class="m-3">

            <div @click="moveCoinToVM(item)" class="coin gold">
              <span>{{item.name}}₽</span>
            </div>

            <div class="mt-2">{{item.amount}} шт.</div>

            <div class="mt-2">
              <el-button @click="moveCoinToVM(item)">Внести {{item.name}}₽</el-button>
            </div>
          </div>
        </template>
      </div>
    </div>

    <el-divider></el-divider>

<!--    Сброс формы-->

    <div class="mt-3 mb-4">
      <el-button @click="resetData()">Reset data</el-button>
    </div>

  </div>
</template>

<script>

  let getDemoData = function () {
    return {
      successMsgBuy: 'Спасибо!',
      errorMsgNoMoney: 'Недостаточно средств',
      errorMsgNoProduct: 'К сожалению товар закончился',
      items: [
        {
          name: 'Чай',
          amount: 10,
          price: 13
        },
        {
          name: 'Кофе',
          amount: 18,
          price: 20
        },
        {
          name: 'Кофе с молоком',
          amount: 21,
          price: 20
        },
        {
          name: 'Сок',
          amount: 35,
          price: 15
        }
      ], //товары vm
      deposit: 0, //внесенная сумма в vm
      bank: [
        {
          name: 1,
          type: 'coin',
          amount: 100
        },
        {
          name: 2,
          type: 'coin',
          amount: 100
        },
        {
          name: 5,
          type: 'coin',
          amount: 100
        },
        {
          name: 10,
          type: 'coin',
          amount: 100
        }
      ], //кошелек vm
      wallet: [
        {
          name: 1,
          type: 'coin',
          amount: 10
        },
        {
          name: 2,
          type: 'coin',
          amount: 30
        },
        {
          name: 5,
          type: 'coin',
          amount: 20
        },
        {
          name: 10,
          type: 'coin',
          amount: 15
        }
      ] //кошелек пользователя
    }
  };

  export default {
    name: 'VendingMachine',
    comments: [],
    data() {
      return {
        successMsgBuy: '', //сообщение если товар успешно куплен
        errorMsgNoMoney: '', //сообщение если не хватает средств на покупку товара
        errorMsgNoProduct: '', //сообщение если не товра
        items: [], //товары vm
        deposit: 0, //внесенная сумма в vm
        bank: [], //кошелек vm
        wallet: [] //кошелек пользователя
      }
    },
    methods: {

      //купить товар
      buy: function (item) {

        let vm = this;

        if(item.amount <= 0){
          vm.alert(vm.errorMsgNoProduct);
          return false
        }

        if (vm.deposit < item.price) {
          vm.alert(vm.errorMsgNoMoney);
          return false
        }

        item.amount = item.amount -1;
        vm.deposit -= item.price;

        vm.alert(vm.successMsgBuy);

        return true;
      },

      //получить сдачу
      getChange: function (){
        let vm = this;
        vm.bank = vm.sortCoins(vm.bank,'desc');

        vm.bank.forEach((coin) => {

          let coinGroupTotal = coin.name * coin.amount

          if(coinGroupTotal > vm.deposit){
            for (let i = 0; i < coin.amount; i++) {

              if(coin.name > vm.deposit){
                continue;
              }

              if(vm.deposit === 0){
                continue;
              }

              vm.deposit = vm.deposit - coin.name;
              vm.moveCoinToWallet(coin)
            }
          }
        })
      },

      //перенести монеты из кошелька в vm
      moveCoinToVM: function (coin) {

        if (coin.amount > 0) {
          coin.amount = ((coin.name * coin.amount) - coin.name) / coin.name

          let depositCoin = this.bank.find(item => item.name === coin.name)

          if (depositCoin !== undefined) {
            depositCoin.amount += 1
          }

          if (depositCoin === undefined) {
            this.bank.push({
              name: coin.name,
              type: 'coin',
              amount: 1
            })
          }

          //calc
          this.deposit = this.deposit + coin.name;

        }
      },

      //перенести монеты из vm в кошелек
      moveCoinToWallet: function (coin) {

        if (coin.amount > 0) {
          coin.amount = ((coin.name * coin.amount) - coin.name) / coin.name

          let depositCoin = this.wallet.find(item => item.name === coin.name)

          if (depositCoin !== undefined) {
            depositCoin.amount += 1
          }

          if (depositCoin === undefined) {
            this.wallet.push({
              name: coin.name,
              type: 'coin',
              amount: 1
            })
          }

        }
      },

      //подсчет общей суммы в кошельке
      calculateTotal: function (coinsArray) {
        let total = 0;

        coinsArray.forEach((element) => {
          total += element.name * element.amount
        })

        return total
      },

      //сортировка кошелька по номиналу монеты
      sortCoins: function (items, sort = 'asc') {

        items.sort(function (a, b) {
          if (a.name > b.name) {
            return 1;
          }
          if (a.name < b.name) {
            return -1;
          }
          return 0;
        });

        if (sort === 'desc') {
          items = items.reverse()
        }

        return items
      },

      //альтернативный вывод alert(если в браузере заблокированы)
      alert: function (msg) {
        this.$alert(msg, 'Внимание', {
          confirmButtonText: 'OK',
        });
      },

      //установка начальных данных
      setDemoData: function () {
        let demoData = getDemoData();
        this.successMsgBuy = demoData.successMsgBuy;
        this.errorMsgNoMoney = demoData.errorMsgNoMoney;
        this.items = demoData.items;
        this.deposit = demoData.deposit;
        this.bank = demoData.bank;
        this.wallet = demoData.wallet;
      },

      //сброс
      resetData: function () {
        this.setDemoData();
      }

    },
    computed: {
      userWalletTotal: function () {
        return this.calculateTotal(this.wallet);
      },
      vmBankTotal: function () {
        return this.calculateTotal(this.bank);
      }
    },
    mounted() {

      //устанавливаем начальные данные
      this.getIndex();
      //this.setDemoData();

      //сортируем монету по убыванию номинала
      this.bank = this.sortCoins(this.bank,'desc');
    }
  }
</script>

<style scoped>

  .vm-items__card {
    width: 250px;
  }

  .image {
    width: 100%;
    display: block;
  }

</style>
