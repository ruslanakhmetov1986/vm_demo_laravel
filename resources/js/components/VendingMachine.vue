<template>
    <div class="vm-wrapper d-flex flex-column">

        <!--      Витрина-->

        <div class="d-flex">
            <div class="left w-50">
                <div class="mt-5 h2">
                    Витрина
                </div>

                <div class="vm-items mt-5 d-flex flex-row">
                    <template v-for="item in items">
                        <el-card class="vm-items__card mr-3" :body-style="{ padding: '0px' }">
                            <img src="../../images/tea.jpg" class="image">
                            <div class="p-3 d-flex flex-column align-items-center">
                                <div class="font-weight-bold text-center" style="line-height: 1.1em; min-height: 40px">
                                    {{item.name}}
                                </div>
                                <div class="d-flex flex-column justify-content-center mt-2 text-center">

                                    <div class="text-info text-center" style="white-space: nowrap">{{item.amount}} шт.
                                    </div>
                                    <div class="font-weight-bold text-center" style="white-space: nowrap">{{item.price}}
                                        ₽
                                    </div>

                                    <el-button @click="buy(item)" type="text">Купить</el-button>
                                </div>
                            </div>
                        </el-card>
                    </template>
                </div>
            </div>


            <!--      Оплата вендинговой машины-->

            <div class="right w-50 ml-5">
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
            <h2 class="text-center">Кошелек пользователя: {{userWalletTotal}} руб.</h2>
            <div class="d-flex flex-row justify-content-center">
                <template v-for="item in wallet">

                    <div class="m-3 d-flex flex-column align-items-center">

                        <div @click="pay(item)" class="coin gold">
                            <span>{{item.name}}₽</span>
                        </div>

                        <div class="mt-2 text-center">{{item.amount}} шт.</div>

                        <div class="mt-2">
                            <el-button @click="pay(item)">Внести {{item.name}}₽</el-button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <el-divider></el-divider>

        <!--    Сброс формы-->

        <div class="mt-3 mb-4 d-flex align-items-center justify-content-center">
            <el-button @click="resetData()">Reset data</el-button>
        </div>


    </div>
</template>

<script>

    export default {
        name: 'VendingMachine',
        comments: [],
        data() {
            return {
                is_loading: true,
                successMsgBuy: 'Спасибо!',
                errorMsgNoMoney: 'Недостаточно средств',
                errorMsgNoProduct: 'К сожалению товар закончился',
                items: [],
                bank: [],
                wallet: [],
                deposit: 0,
                vmBankTotal: 0,
                userWalletTotal: 0
            }
        },
        methods: {

            setIndexData(data) {
                this.items = data.drinks;
                this.bank = data.bank;
                this.wallet = data.wallet;
                this.deposit = data.deposit;
                this.vmBankTotal = data.vmBankTotal;
                this.userWalletTotal = data.userWalletTotal;
            },

            //rest api

            getIndex() {

                this.is_loading = true;

                const url = `/api/index`;

                axios
                    .get(url)
                    .then(response => {
                        this.setIndexData(response.data.data);
                        console.log(response.data);
                    })
                    .catch(error => {
                        console.log(error);
                    })
                    .finally(() => (this.is_loading = false));
            },

            //купить товар
            buy: function (item) {

                if (item.amount <= 0) {
                    this.alert(this.errorMsgNoProduct);
                    return false
                }

                if (this.deposit < item.price) {
                    this.alert(this.errorMsgNoMoney);
                    return false
                }

                this.is_loading = true;

                const url = `/api/buy`;

                axios
                    .post(url, item)
                    .then(response => {
                        this.getIndex();
                        this.alert(this.successMsgBuy);
                    })
                    .catch(error => {
                        console.log(error);
                    })
                    .finally(() => (this.is_loading = false));

                return true;
            },

            //получить сдачу
            getChange: function () {

                if (this.deposit === 0) {
                    this.alert(this.errorMsgNoMoney);
                    return false
                }

                this.is_loading = true;

                const url = `/api/getChange`;

                axios
                    .get(url)
                    .then(response => {
                        this.getIndex();
                    })
                    .catch(error => {
                        console.log(error);
                    })
                    .finally(() => (this.is_loading = false));

            },

            pay: function (coin) {

                if (coin.amount <= 0) {
                    return false;
                }

                this.is_loading = true;

                const url = `/api/pay`;

                axios
                    .post(url, coin)
                    .then(response => {
                        console.log(response.data.data);
                        this.getIndex();
                    })
                    .catch(error => {
                        console.log(error);
                    })
                    .finally(() => (this.is_loading = false));

                return true;

            },

            //сброс
            resetData: function () {

                this.is_loading = true;

                const url = `/api/resetDemoData`;

                axios
                    .get(url)
                    .then(response => {

                        this.getIndex();
                        this.alert("Demo data reset success")

                    })
                    .catch(error => {
                        console.log(error);
                    })
                    .finally(() => (
                        this.is_loading = false
                    ));
            },

            alert: function (msg) {
                this.$alert(msg, 'I have something for you!', {
                    confirmButtonText: 'OK',
                });
            },

        },
        mounted() {
            this.getIndex();
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
