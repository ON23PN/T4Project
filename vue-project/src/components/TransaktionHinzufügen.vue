<template>
  <h3>Neue Transaktion hinzufügen</h3>
  <form id="form">
    <div class="form-control">
      <label for="text">Beschreibung</label>
      <input type="text" id="text" placeholder="..." />
    </div>
    <div class="form-control">
      <label for="amount">Menge <br />
        (Negativ = Ausgabe, Positiv = Einnahme)</label>
      <input type="number" id="amount" placeholder="..." />
    </div>
    <button class="btn">Transaktion hinzufügen</button>
  </form>
</template>