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

import { ref, computed, onMounted } from 'vue';

const toast = useToast();

//Funktion für Transaktionen
const transactions = ref([]);

onMounted(() => {
    const savedTransactions = JSON.parse(localStorage.getItem
        ('transactions'));

    if (savedTransactions) {
        transactions.value = savedTransactions;
    }
});

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

    saveTransactionsToLocalStorage();

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

    saveTransactionsToLocalStorage();

    toast.success('Transaktion erfolgreich gelöscht');
};

//Speicherung im Local Storage
const saveTransactionsToLocalStorage = () => {
    localStorage.setItem('transactions', JSON.stringify(transactions.value));
}

</script>