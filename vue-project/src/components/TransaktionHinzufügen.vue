<template>
  <h3>Neue Transaktion hinzufügen</h3>
  <form id="form" @submit.prevent="onSubmit">
    <div class="form-control">
      <label for="description">Beschreibung</label>
      <input type="text" id="description" v-model="description" placeholder="..." />
    </div>
    <div class="form-control">
      <label for="amount">Menge <br />
        (negativ = Ausgabe, positiv = Einnahme)</label>
      <input type="number" id="amount" v-model="amount" placeholder="..." />
    </div>
    <button class="btn">Transaktion hinzufügen</button>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import { useToast } from 'vue-toastification';

const description = ref('');  // Umbenennung von `text` zu `description`
const amount = ref('');

const emit = defineEmits(['transactionSubmitted']);

const toast = useToast();

// Funktion um das Formular zu verarbeiten
const onSubmit = () => {
  // Überprüfung, ob alle Felder ausgefüllt sind
  if (!description.value || !amount.value) {
    toast.error('Beide Felder müssen ausgefüllt sein!');
    return;
  }

  // Erstellt ein neues Transaktionsobjekt, wenn beide Felder ausgefüllt sind
  const transactionData = {
    description: description.value,  // Sende `description` anstelle von `text`
    amount: parseFloat(amount.value)  // Sicherstellen, dass der Betrag als Zahl übergeben wird
  }

  emit('transactionSubmitted', transactionData);

  // Setzt die Eingabefelder zurück
  description.value = '';
  amount.value = '';
};
</script>
