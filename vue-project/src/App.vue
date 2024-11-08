<template>
    <Header />
    <div class="container">
        <Guthaben :total="+total" />
        <EinnahmenAusgaben :income="+income" :expenses="+expenses" />
        <Historie :transactions="transactions" @transactionDeleted="handleTransactionDeleted" />
        <TransaktionHinzufügen @transactionSubmitted="handleTransactionSubmitted" />
    </div>
</template>

<script setup>
import Header from './components/Header.vue';
import Guthaben from './components/Guthaben.vue';
import EinnahmenAusgaben from './components/EinnahmenAusgaben.vue';
import Historie from './components/Historie.vue';
import TransaktionHinzufügen from './components/TransaktionHinzufügen.vue';

import { useToast } from 'vue-toastification';

import { ref, computed } from 'vue';

const toast = useToast();

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

//Neue Transaktion hinzufügen
const handleTransactionSubmitted = (transactionData) => {
    transactions.value.push({
        id: generateUniqueId(),
        text: transactionData.text,
        amount: transactionData.amount,
    });

    toast.success('Transaktion erfolgreich hinzugefügt');

};

//ID generieren
const generateUniqueId = () => {
    return Math.floor(Math.random() * 1000000);
}

//Transaktionen löschen
const handleTransactionDeleted = (id) => {
    transactions.value = transactions.value.filter((transaction) =>
        transaction.id !== id);

    toast.success('Transaktion erfolgreich gelöscht');
};

</script>