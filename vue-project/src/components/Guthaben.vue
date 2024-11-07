<template>
    <h4>Dein Guthaben</h4>
    <h1 id="guthaben">{{ total }}€</h1>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    total: {
        type: Number,
        required: true,
    },
});
</script>