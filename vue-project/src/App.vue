<template>
    <Header />
    <div class="container">
        <Guthaben :total="total" />
        <EinnahmenAusgaben :income="income" :expenses="expenses" />
        <Historie :transactions="transactions" />
        <TransaktionHinzufügen />
    </div>
</template>

<script setup>
import Header from './components/Header.vue';
import Guthaben from './components/Guthaben.vue';
import EinnahmenAusgaben from './components/EinnahmenAusgaben.vue';
import Historie from './components/Historie.vue';
import TransaktionHinzufügen from './components/TransaktionHinzufügen.vue';

import { ref, computed } from 'vue';

//Beispiel Transaktionen:
const transactions = ref([
    { id: 1, text: 'Flower', amount: -19.25 },
    { id: 2, text: 'Salary', amount: 20 },
    { id: 3, text: 'Food', amount: 40 },
]);

//Guthaben berechnen
const total = computed(() => {
    return transactions.value.reduce((acc, transaction) => {
        return acc + transaction.amount;
    }, 0);
});

//Einnahmen berechnen (nur positive Beträge)
const income = computed(() => {
    return Number(
        transactions.value
            .filter((transaction) => transaction.amount > 0)
            .reduce((acc, transaction) => acc + transaction.amount, 0)
            .toFixed(2)
    );
});

//Ausgaben berechnen (nur negative Beträge)
const expenses = computed(() => {
    return Number(
        transactions.value
            .filter((transaction) => transaction.amount < 0)
            .reduce((acc, transaction) => acc + transaction.amount, 0)
            .toFixed(2)
    );
});
</script>