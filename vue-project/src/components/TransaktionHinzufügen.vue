<template>
  <h3>Neue Transaktion hinzufügen</h3>
  <form id="form" @submit.prevent="onSubmit">
    <div class="form-control">
      <label for="text">Beschreibung</label>
      <input type="text" id="text" v-model="text" placeholder="..." />
    </div>
    <div class="form-control">
      <label for="amount">Menge <br />
        (negativ = Ausgabe, positiv = Einnahme)</label>
      <input type="text" id="amount" v-model="amount" placeholder="..." />
    </div>
    <button class="btn">Transaktion hinzufügen</button>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import { useToast } from 'vue-toastification';

const text = ref('');
const amount = ref('');

const emit = defineEmits(['transactionSubmitted']);

const toast = useToast();

//Funktion um das Formular zu verarbeiten
const onSubmit = () => {
  //Überprüfung ob alle Felder ausgefüllt sind
  if (!text.value || !amount.value) {
    toast.error('Beide Felder müssen ausgefüllt sein!');
    return;
  }

  //Erstellt ein neues Transaktionsobjekt, wenn beide Felder ausgefüllt sind
  const transactionData = {
    text: text.value,
    amount: parseFloat(amount.value)
  }

  emit('transactionSubmitted', transactionData);

  //Setzt die Eingabefelder zurück
  text.value = '';
  amount.value = '';
};

</script>