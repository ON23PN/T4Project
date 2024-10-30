<template>
    <Header />
    <div class="container">
        <Guthaben />
        <EinnahmenAusgaben />
        <Historie />
        <TransaktionHinzufügen />
    </div>
</template>

<script>
import Header from './components/Header.vue';
import Guthaben from './components/Guthaben.vue';
import EinnahmenAusgaben from './components/EinnahmenAusgaben.vue';
import Historie from './components/Historie.vue';
import TransaktionHinzufügen from './components/TransaktionHinzufügen.vue';

export default {
    components: {
        Header,
        Guthaben,
        EinnahmenAusgaben,
        Historie,
        TransaktionHinzufügen,
    },
};
</script>