<template>
    <Header @logout="handleLogout" />
    <Login v-if="!isLoggedIn" @login="handleLogin" />
    <div v-if="isLoggedIn" class="container">
        <Guthaben :total="total" />
        <EinnahmenAusgaben :income="income" :expenses="expenses" />
        <Historie :transactions="formattedTransactions" @transactionDeleted="handleTransactionDeleted" />
        <TransaktionHinzufügen @transactionSubmitted="handleTransactionSubmitted" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useToast } from 'vue-toastification';
import Header from './components/Header.vue';
import Login from './components/login.vue';
import Guthaben from './components/Guthaben.vue';
import EinnahmenAusgaben from './components/EinnahmenAusgaben.vue';
import Historie from './components/Historie.vue';
import TransaktionHinzufügen from './components/TransaktionHinzufügen.vue';

const toast = useToast();

// Authentifizierung
const isLoggedIn = ref(false); // Status, ob der Benutzer eingeloggt ist
const user = ref(null); // Speichert Benutzerdaten

// Transaktionen
const transactions = ref([]); // Array für Transaktionen

// Berechnungen für Guthaben, Einnahmen und Ausgaben
const total = computed(() =>
    transactions.value.reduce((acc, transaction) => acc + Number(transaction.amount), 0)
);

const income = computed(() =>
    transactions.value.filter(t => Number(t.amount) > 0).reduce((acc, t) => acc + Number(t.amount), 0)
);

const expenses = computed(() =>
    transactions.value.filter(t => Number(t.amount) < 0).reduce((acc, t) => acc + Number(t.amount), 0)
);


// Benutzerdaten und Transaktionen laden
onMounted(async () => {
    await fetchUserData(); // Benutzerdaten laden
    if (isLoggedIn.value) {
        await fetchTransactions(); // Wenn eingeloggt, Transaktionen laden
    }
});

// Benutzer-Daten aus dem Backend holen
const fetchUserData = async () => {
    const response = await fetch('http://localhost/Projekt/vue-project/backend/getUserData.php', {
        method: 'GET',
        credentials: 'include', // Cookies (Session) senden
    });

    const result = await response.json();
    if (result.user) {
        isLoggedIn.value = true;
        user.value = result.user;
        await fetchTransactions(); // Wenn Benutzer eingeloggt, Transaktionen laden
    } else {
        toast.error(result.message || 'Fehler beim Laden der Benutzerdaten.');
    }
};

// Handle Login
const handleLogin = async (userData) => {
    isLoggedIn.value = true;
    user.value = userData;
    await fetchTransactions(); // Transaktionen nach dem Login laden
};

// Handle Logout
const handleLogout = async () => {
    isLoggedIn.value = false;
    user.value = null;
    transactions.value = [];
    await fetch('http://localhost/Projekt/vue-project/backend/logout.php', { method: 'POST' });
    toast.success('Erfolgreich ausgeloggt'); // Toast-Nachricht nach erfolgreichem Logout
};

// Transaktionen aus dem Backend laden
const fetchTransactions = async () => {
    const response = await fetch('http://localhost/Projekt/vue-project/backend/getTransactions.php', {
        method: 'GET',
        credentials: 'include', // Cookies (Session) senden
    });

    const result = await response.json();
    if (result.transactions) {
        transactions.value = result.transactions; // Transaktionen laden, wenn vorhanden
    } else {
        toast.error(result.message || 'Fehler beim Laden der Transaktionen.'); // Fehlermeldung, falls keine Transaktionen vorhanden
    }
};

// Neue Transaktion hinzufügen
const handleTransactionSubmitted = async (transactionData) => {
    const response = await fetch('http://localhost/Projekt/vue-project/backend/addTransaction.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' // Content-Type als JSON setzen
        },
        body: JSON.stringify({
            amount: transactionData.amount,
            description: transactionData.description
        }),
        credentials: 'include', // Cookies (Session) senden
    });

    const result = await response.json();
    if (result.message === 'Transaktion hinzugefügt') {
        await fetchTransactions(); // Transaktionen nach dem Hinzufügen aktualisieren
        toast.success('Transaktion erfolgreich hinzugefügt'); // Erfolgreiche Nachricht anzeigen
    } else {
        toast.error(result.message || 'Fehler beim Hinzufügen der Transaktion');
    }
};

// Transaktion löschen
const handleTransactionDeleted = async (id) => {
    try {
        const response = await fetch(`http://localhost/Projekt/vue-project/backend/deleteTransaction.php?id=${id}`, {
            method: 'GET',
            credentials: 'include', // Cookies (Session) senden
        });

        // Fehlerbehandlung für ungültige oder fehlende JSON-Antwort
        if (!response.ok) {
            throw new Error('Serverfehler');
        }

        const result = await response.json(); // Erwartet, dass der Server eine JSON-Antwort sendet
        if (result.message === 'Transaktion gelöscht') {
            await fetchTransactions(); // Transaktionen nach dem Löschen aktualisieren
            toast.success('Transaktion erfolgreich gelöscht');
        } else {
            toast.error(result.message || 'Fehler beim Löschen der Transaktion');
        }
    } catch (error) {
        console.error('Fehler beim Löschen der Transaktion:', error);
        toast.error('Fehler beim Löschen der Transaktion');
    }
};

// Formatierte Transaktionen mit 2 Dezimalstellen
const formattedTransactions = computed(() => {
    return transactions.value.map(transaction => ({
        ...transaction,
        amount: formatAmount(transaction.amount), // Betrag formatieren
        description: transaction.description // Beschreibung hinzufügen
    }));
});


// Hilfsfunktion zur Formatierung der Beträge
const formatAmount = (amount) => {
    const numericAmount = Number(amount);
    if (isNaN(numericAmount)) {
        return '0.00'; // Oder eine andere Fehlerbehandlung, falls der Betrag ungültig ist
    }
    return numericAmount.toFixed(2); // Formatierung auf 2 Dezimalstellen
};
</script>
